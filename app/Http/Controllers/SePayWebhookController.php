<?php

namespace App\Http\Controllers;

use App\Models\AffiliateHistory;
use App\Models\BankAccount;
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
                    'authorization' => $request->header('Authorization') ? 'Bearer/Apikey [provided]' : null,
                    'api_key' => $request->header('X-API-Key') ?? $request->header('X-Secret-Key'),
                ],
            ]);

            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $payload = $request->json()->all();
        if (!is_array($payload) || empty($payload)) {
            // Also check standard form-data / POST input
            $payload = $request->all();
        }

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

        // Extract user ID from memo using all possible configured and common prefixes
        $userId = $this->extractUserId($content, $payload);

        if ($userId <= 0) {
            // Check if this is a test delivery from SePay (e.g. id=0 or sample memo)
            if ($transactionId === '0' || stripos($content, 'DH12345') !== false || stripos($content, 'test') !== false) {
                Log::info('SePay test webhook acknowledged successfully', [
                    'transaction_id' => $transactionId,
                    'content' => $content,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'SePay test webhook acknowledged',
                ]);
            }

            Log::notice('SePay webhook ignored: payment code not found', [
                'transaction_id' => $transactionId,
                'content' => $content,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Ignored: payment code not found',
            ]);
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

        return response()->json([
            'success' => true,
            'message' => 'Webhook received',
        ]);
    }

    private function isAuthenticated(Request $request): bool
    {
        $secret = trim((string) config('sepay.webhook_secret'));
        if ($secret === '') {
            return false;
        }

        // 1. API Key / Secret Key header & parameter checks
        $providedKey = null;

        // Authorization header
        $authHeader = trim((string) $request->header('Authorization'));
        if ($authHeader !== '') {
            if (stripos($authHeader, 'Apikey ') === 0) {
                $providedKey = substr($authHeader, 7);
            } elseif (stripos($authHeader, 'Bearer ') === 0) {
                $providedKey = substr($authHeader, 7);
            } else {
                $providedKey = $authHeader;
            }
        }

        // Custom API Key headers
        if (!$providedKey) {
            foreach (['X-API-Key', 'X-Secret-Key', 'Secret-Key', 'X-SePay-API-Key', 'X-Sepay-Api-Key', 'x-api-key'] as $h) {
                $val = trim((string) $request->header($h));
                if ($val !== '') {
                    $providedKey = $val;
                    break;
                }
            }
        }

        // Query string fallback (?token=... or ?key=... or ?secret=...)
        if (!$providedKey) {
            $providedKey = $request->query('token')
                ?? $request->query('key')
                ?? $request->query('secret')
                ?? $request->query('apiKey');
        }

        // Payload field fallback
        if (!$providedKey && is_array($request->json()->all())) {
            $payload = $request->json()->all();
            $providedKey = $payload['secret'] ?? $payload['apiKey'] ?? $payload['token'] ?? null;
        }

        if ($providedKey && hash_equals($secret, trim((string) $providedKey))) {
            return true;
        }

        // 2. HMAC-SHA256 signature verification (Official SePay method)
        $headerName = (string) config('sepay.signature_header', 'X-SePay-Signature');
        $signatureHeader = trim((string) ($request->header($headerName) ?? $request->header('x-sepay-signature') ?? ''));
        $rawBody = $request->getContent();

        if ($signatureHeader !== '' && $rawBody !== '') {
            $timestamp = trim((string) ($request->header('X-SePay-Timestamp') ?? $request->header('x-sepay-timestamp') ?? ''));
            $cleanSignature = $signatureHeader;

            // Extract t=... and v1=... (SePay Stripe-compatible format)
            if (preg_match('/v1=([a-f0-9]+)/i', $signatureHeader, $matchV1)) {
                $cleanSignature = $matchV1[1];
            }
            if (empty($timestamp) && preg_match('/t=(\d+)/i', $signatureHeader, $matchT)) {
                $timestamp = $matchT[1];
            }

            // Strip "sha256=" prefix if present
            if (stripos($cleanSignature, 'sha256=') === 0) {
                $cleanSignature = substr($cleanSignature, 7);
            }

            // Replay attack protection (5-minute tolerance if timestamp is present)
            if ($timestamp !== '' && is_numeric($timestamp)) {
                if (abs(time() - (int) $timestamp) > 300) {
                    Log::warning('SePay webhook rejected: timestamp expired', [
                        'timestamp' => $timestamp,
                        'current' => time(),
                    ]);
                    return false;
                }
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
                    hash_equals('sha256=' . $expectedHex, strtolower($cleanSignature)) ||
                    hash_equals($base64Hash = $expectedBase64, $cleanSignature)) {
                    return true;
                }
            }
        }

        return false;
    }

    private function extractUserId(string $content, array $payload): int
    {
        $prefix = (string) config('sepay.prefix', 'Yuki');

        // 1. Direct check on SePay recognized 'code' field (e.g. "Yuki001")
        if (!empty($payload['code'])) {
            $codeStr = trim((string) $payload['code']);
            $codeUserId = get_id_bank($prefix, $codeStr);
            if ($codeUserId <= 0) {
                $codeUserId = get_id_bank('Yuki', $codeStr);
            }
            if ($codeUserId <= 0 && preg_match('/\d+/', $codeStr, $m)) {
                $candidate = (int) $m[0];
                if ($candidate > 0 && User::whereKey($candidate)->exists()) {
                    $codeUserId = $candidate;
                }
            }
            if ($codeUserId > 0) {
                return $codeUserId;
            }
        }

        // 2. Check content with configured prefix
        $userId = get_id_bank($prefix, $content);

        // 3. Check configured prefixes from active bank accounts (e.g. 'yuki')
        if ($userId <= 0) {
            try {
                $dbPrefixes = BankAccount::whereNotNull('prefix')
                    ->where('prefix', '!=', '')
                    ->pluck('prefix')
                    ->unique();
                foreach ($dbPrefixes as $p) {
                    $userId = get_id_bank($p, $content);
                    if ($userId > 0) {
                        return $userId;
                    }
                }
            } catch (\Throwable $e) {}
        }

        // 4. Check common prefixes: 'Yuki', 'yuki', 'NAP', 'naptien', 'nap'
        if ($userId <= 0) {
            foreach (['Yuki', 'yuki', 'NAP', 'naptien', 'nap'] as $commonPrefix) {
                $userId = get_id_bank($commonPrefix, $content);
                if ($userId > 0) {
                    return $userId;
                }
            }
        }

        // 5. Regex fallback: find any word prefix directly followed by digits (e.g. 'Yuki001', 'yuki1', 'nap12')
        if ($userId <= 0) {
            if (preg_match('/[a-zA-Z]+(\d+)/', $content, $matches)) {
                $candidateId = (int) $matches[1];
                if ($candidateId > 0 && User::whereKey($candidateId)->exists()) {
                    return $candidateId;
                }
            }
        }

        return $userId;
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
