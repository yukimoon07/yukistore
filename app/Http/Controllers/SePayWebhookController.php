<?php

namespace App\Http\Controllers;

use App\Models\AffiliateHistory;
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
                'headers' => [
                    'signature' => $request->header(config('sepay.signature_header', 'X-SePay-Signature')),
                    'timestamp' => $request->header('X-SePay-Timestamp'),
                ],
            ]);

            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $payload = $request->json()->all();
        if (!is_array($payload) || empty($payload)) {
            return response()->json(['message' => 'Invalid JSON payload'], 422);
        }

        $transactionId = trim((string) ($payload['id'] ?? $payload['reference_number'] ?? $payload['referenceCode'] ?? ''));
        $amount = (int) round((float) ($payload['transferAmount'] ?? $payload['amount'] ?? 0));
        $content = trim((string) ($payload['content'] ?? $payload['description'] ?? ''));
        $accountNumber = trim((string) ($payload['accountNumber'] ?? ''));
        $bankName = trim((string) ($payload['gateway'] ?? $payload['bank'] ?? config('sepay.bank_name', 'SePay')));
        $bankAccountId = $payload['bank_account_id'] ?? $payload['bankAccountId'] ?? null;
        $transferType = strtolower(trim((string) ($payload['transferType'] ?? 'in')));

        if ($transactionId === '' || $amount <= 0 || $content === '') {
            return response()->json(['message' => 'Missing transaction fields'], 422);
        }

        // SePay sends both "in" (tiền vào) and "out" (tiền ra). Only credit incoming transfers.
        if ($transferType !== 'in') {
            Log::notice('SePay webhook ignored: not an incoming transfer', [
                'transaction_id' => $transactionId,
                'transfer_type' => $transferType,
            ]);

            return response()->json(['message' => 'Ignored: not an incoming transfer']);
        }

        $prefix = (string) config('sepay.prefix', 'NAP');
        $userId = get_id_bank($prefix, $content);

        // Fallback to parsed payment code from SePay payload if available
        if ($userId <= 0 && !empty($payload['code'])) {
            $userId = get_id_bank($prefix, (string) $payload['code']);
        }

        // Additional fallback: check common prefixes if customized prefix did not match
        if ($userId <= 0 && strcasecmp($prefix, 'NAP') !== 0) {
            $userId = get_id_bank('NAP', $content);
        }
        if ($userId <= 0 && strcasecmp($prefix, 'naptien') !== 0) {
            $userId = get_id_bank('naptien', $content);
        }

        if ($userId <= 0) {
            // Acknowledge delivery but do not credit an unrecognised memo.
            Log::notice('SePay webhook ignored: payment code not found', [
                'transaction_id' => $transactionId,
                'content' => $content,
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
                    ->orWhere('gateway_transaction_id', $transactionId)
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

                // Affiliate Commission Logic (10%)
                if ($user->referrer_id) {
                    $referrer = User::find($user->referrer_id);
                    if ($referrer) {
                        $commission = (int) round($amount * 0.10);
                        if ($commission > 0) {
                            $refPrevBalance = (int) $referrer->balance;
                            $referrer->balance = $refPrevBalance + $commission;
                            $referrer->total_commission = (int) $referrer->total_commission + $commission;
                            $referrer->save();

                            AffiliateHistory::create([
                                'referrer_id' => $referrer->id,
                                'referred_id' => $user->id,
                                'commission_amount' => $commission,
                                'type' => 'deposit',
                                'description' => 'Hoa hồng nạp tiền từ ' . $user->username,
                            ]);

                            MoneyTransaction::create([
                                'user_id' => $referrer->id,
                                'type' => 'deposit',
                                'amount' => $commission,
                                'balance_before' => $refPrevBalance,
                                'balance_after' => $referrer->balance,
                                'description' => 'Hoa hồng 10% nạp tiền từ ' . $user->username,
                                'reference_id' => $transactionId,
                            ]);
                        }
                    }
                }

                Log::info('SePay webhook processed successfully', [
                    'transaction_id' => $transactionId,
                    'user_id' => $user->id,
                    'amount' => $amount,
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
        $secret = trim((string) config('sepay.webhook_secret'));
        if ($secret === '') {
            return false;
        }

        $headerName = (string) config('sepay.signature_header', 'X-SePay-Signature');
        $providedSignature = trim((string) $request->header($headerName));
        $rawBody = $request->getContent();

        // 1. HMAC-SHA256 signature verification (Official SePay method)
        if ($providedSignature !== '' && $rawBody !== '') {
            $timestamp = trim((string) ($request->header('X-SePay-Timestamp') ?? $request->header('x-sepay-timestamp') ?? ''));

            // Replay attack protection (5-minute window if timestamp is present)
            if ($timestamp !== '' && is_numeric($timestamp)) {
                if (abs(time() - (int) $timestamp) > 300) {
                    Log::warning('SePay webhook rejected: timestamp expired', [
                        'timestamp' => $timestamp,
                        'current' => time(),
                    ]);
                    return false;
                }
            }

            // Strip "sha256=" prefix if sent by SePay
            $cleanSignature = $providedSignature;
            if (str_starts_with(strtolower($cleanSignature), 'sha256=')) {
                $cleanSignature = substr($cleanSignature, 7);
            }

            // Candidates to sign: {timestamp}.{rawBody} OR rawBody directly
            $payloadCandidates = [];
            if ($timestamp !== '') {
                $payloadCandidates[] = $timestamp . '.' . $rawBody;
            }
            $payloadCandidates[] = $rawBody;

            foreach ($payloadCandidates as $candidate) {
                $expectedHex = hash_hmac('sha256', $candidate, $secret);
                $expectedBase64 = base64_encode(hash_hmac('sha256', $candidate, $secret, true));

                if (hash_equals($expectedHex, $cleanSignature) ||
                    hash_equals('sha256=' . $expectedHex, strtolower($providedSignature)) ||
                    hash_equals($expectedBase64, $cleanSignature)) {
                    return true;
                }
            }
        }

        // 2. Fallback: API Key verification (Authorization: Apikey <KEY> or Bearer <KEY>)
        $authHeader = trim((string) $request->header('Authorization'));
        if ($authHeader !== '') {
            if (str_starts_with($authHeader, 'Apikey ')) {
                $apiKey = substr($authHeader, 7);
                if (hash_equals($secret, $apiKey)) {
                    return true;
                }
            } elseif (str_starts_with($authHeader, 'Bearer ')) {
                $bearer = substr($authHeader, 7);
                if (hash_equals($secret, $bearer)) {
                    return true;
                }
            }
        }

        // 3. Fallback: Custom Header API Key
        $customApiKey = trim((string) ($request->header('X-SePay-API-Key') ?? $request->header('X-API-Key') ?? ''));
        if ($customApiKey !== '' && hash_equals($secret, $customApiKey)) {
            return true;
        }

        return false;
    }

    private function normaliseBank(string $bankName): string
    {
        $map = [
            'vietinbank' => 'VietinBank',
            'viettinbank' => 'VietinBank',
            'vietin' => 'VietinBank',
            'vpb' => 'VPBank',
            'vpbank' => 'VPBank',
            'tpb' => 'TPBank',
            'tpbank' => 'TPBank',
            'acb' => 'ACB',
            'bidv' => 'BIDV',
            'mb' => 'MBBank',
            'mbbank' => 'MBBank',
            'ocb' => 'OCB',
            'kienlongbank' => 'KienLongBank',
            'msb' => 'MSB',
            'vietcombank' => 'Vietcombank',
            'vcb' => 'Vietcombank',
            'techcombank' => 'Techcombank',
            'tcb' => 'Techcombank',
            'agribank' => 'Agribank',
            'sacombank' => 'Sacombank',
            'hdbank' => 'HDBank',
            'shb' => 'SHB',
        ];

        $lower = strtolower(trim($bankName));
        return $map[$lower] ?? ($bankName ?: 'SePay');
    }
}
