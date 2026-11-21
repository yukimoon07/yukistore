<?php

namespace App\Http\Controllers;

use App\Models\BankDeposit;
use App\Models\MoneyTransaction;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SePayWebhookController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        if (!$this->isAuthenticated($request)) {
            Log::warning('SePay webhook rejected: invalid authentication', [
                'ip' => $request->ip(),
            ]);

            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $payload = $request->json()->all();
        if (!is_array($payload) || !$payload) {
            return response()->json(['message' => 'Invalid JSON payload'], 422);
        }

        $transactionId = trim((string) ($payload['id'] ?? $payload['reference_number'] ?? ''));
        $amount = (int) round((float) ($payload['transferAmount'] ?? $payload['amount'] ?? 0));
        $content = trim((string) ($payload['content'] ?? $payload['description'] ?? ''));
        $accountNumber = trim((string) ($payload['accountNumber'] ?? ''));
        $bankName = trim((string) ($payload['gateway'] ?? $payload['bank'] ?? config('sepay.bank_name')));
        $bankAccountId = $payload['bank_account_id'] ?? $payload['bankAccountId'] ?? null;

        if ($transactionId === '' || $amount <= 0 || $content === '') {
            return response()->json(['message' => 'Missing transaction fields'], 422);
        }

        $prefix = (string) config('sepay.prefix', 'NAP');
        $userId = get_id_bank($prefix, $content);
        if ($userId <= 0) {
            // Acknowledge valid delivery but do not credit an unrecognised memo.
            Log::notice('SePay webhook ignored: payment code not found', [
                'transaction_id' => $transactionId,
            ]);

            return response()->json(['message' => 'Ignored: payment code not found']);
        }

        try {
            DB::transaction(function () use (
                $transactionId,
                $amount,
                $content,
                $accountNumber,
                $bankName,
                $bankAccountId,
                $userId,
                $payload
            ): void {
                $deposit = BankDeposit::where('transaction_id', $transactionId)
                    ->lockForUpdate()
                    ->first();

                if ($deposit) {
                    return;
                }

                $user = User::whereKey($userId)->lockForUpdate()->first();
                if (!$user) {
                    Log::notice('SePay webhook ignored: user not found', [
                        'transaction_id' => $transactionId,
                        'user_id' => $userId,
                    ]);
                    return;
                }

                $balanceBefore = (int) $user->balance;
                $balanceAfter = $balanceBefore + $amount;

                BankDeposit::create([
                    'transaction_id' => $transactionId,
                    'user_id' => $user->id,
                    'account_number' => $accountNumber ?: 'unknown',
                    'amount' => $amount,
                    'content' => $content,
                    'bank' => $this->normaliseBank($bankName),
                    'bank_account_id' => is_numeric($bankAccountId) ? (int) $bankAccountId : null,
                    'status' => 'completed',
                    'gateway' => 'sepay',
                    'gateway_transaction_id' => $transactionId,
                    'raw_payload' => $payload,
                ]);

                $user->balance = $balanceAfter;
                $user->total_deposited = (int) $user->total_deposited + $amount;
                $user->save();

                MoneyTransaction::create([
                    'user_id' => $user->id,
                    'type' => 'deposit',
                    'amount' => $amount,
                    'balance_before' => $balanceBefore,
                    'balance_after' => $balanceAfter,
                    'description' => 'Nạp tiền qua SePay - Mã giao dịch: ' . $transactionId,
                    'reference_id' => $transactionId,
                ]);
            }, 3);
        } catch (\Throwable $e) {
            Log::error('SePay webhook processing failed', [
                'transaction_id' => $transactionId,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['message' => 'Temporary processing error'], 500);
        }

        return response()->json(['message' => 'Webhook received']);
    }

    private function isAuthenticated(Request $request): bool
    {
        $secret = (string) config('sepay.webhook_secret');
        $header = (string) config('sepay.signature_header', 'X-SePay-Signature');
        $providedSignature = trim((string) $request->header($header));
        $rawBody = $request->getContent();

        if ($secret === '' || $providedSignature === '' || $rawBody === '') {
            return false;
        }

        $expectedHex = hash_hmac('sha256', $rawBody, $secret);
        $expectedBase64 = base64_encode(hash_hmac('sha256', $rawBody, $secret, true));
        $encoding = strtolower((string) config('sepay.signature_encoding', 'hex'));
        $expected = $encoding === 'base64' ? $expectedBase64 : $expectedHex;

        // Accept only the configured encoding. Do not accept a plain secret key.
        return hash_equals($expected, $providedSignature);
    }

    private function normaliseBank(string $bankName): string
    {
        // The legacy column is an enum. Keep SePay webhooks compatible with it.
        $allowed = [
            'VPBank', 'TPBank', 'VietinBank', 'ACB', 'BIDV',
            'MBBank', 'OCB', 'KienLongBank', 'MSB',
        ];

        return in_array($bankName, $allowed, true) ? $bankName : 'VPBank';
    }
}
