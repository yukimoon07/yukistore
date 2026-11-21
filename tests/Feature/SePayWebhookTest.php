<?php

namespace Tests\Feature;

use App\Models\AffiliateHistory;
use App\Models\BankAccount;
use App\Models\BankDeposit;
use App\Models\MoneyTransaction;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SePayWebhookTest extends TestCase
{
    use DatabaseTransactions;

    protected string $secret = 'whsec_vCcvKXv7jmI5OUqQzLKEcZzOuYxpI8AO';

    protected function setUp(): void
    {
        parent::setUp();
        config(['sepay.webhook_secret' => $this->secret]);
        config(['sepay.prefix' => 'NAP']);
    }

    public function test_rejects_unauthorized_request_without_signature(): void
    {
        $response = $this->postJson('/api/webhooks/sepay', [
            'id' => 'TX123456',
            'transferAmount' => 50000,
            'content' => 'NAP1',
        ]);

        $response->assertStatus(401);
    }

    public function test_rejects_invalid_signature(): void
    {
        $response = $this->withHeaders([
            'X-SePay-Signature' => 'invalid_signature_hex',
            'X-SePay-Timestamp' => (string) time(),
        ])->postJson('/api/webhooks/sepay', [
            'id' => 'TX123456',
            'transferAmount' => 50000,
            'content' => 'NAP1',
        ]);

        $response->assertStatus(401);
    }

    public function test_accepts_stripe_like_signature_format(): void
    {
        $user = User::create([
            'username' => 't_v1_user_' . uniqid(),
            'email' => 'tv1_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'balance' => 0,
            'total_deposited' => 0,
        ]);

        $body = json_encode([
            'id' => 'TX_TV1_' . time(),
            'transferAmount' => 50000,
            'content' => 'NAP' . $user->id,
            'transferType' => 'in',
        ]);
        $timestamp = (string) time();
        $hash = hash_hmac('sha256', $timestamp . '.' . $body, $this->secret);
        $signature = "t={$timestamp},v1={$hash}";

        $response = $this->call(
            'POST',
            '/api/webhooks/sepay',
            [],
            [],
            [],
            [
                'HTTP_X_SEPAY_SIGNATURE' => $signature,
                'CONTENT_TYPE' => 'application/json',
            ],
            $body
        );

        $response->assertStatus(200);
        $user->refresh();
        $this->assertEquals(50000, $user->balance);
    }

    public function test_accepts_yuki_prefix_from_screenshot(): void
    {
        $user = User::create([
            'username' => 'yuki_user_' . uniqid(),
            'email' => 'yuki_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'balance' => 0,
            'total_deposited' => 0,
        ]);

        $body = json_encode([
            'id' => 'TX_YUKI_' . time(),
            'transferAmount' => 50000,
            'content' => 'yuki' . $user->id,
            'transferType' => 'in',
            'gateway' => 'MBBank',
        ]);
        $timestamp = (string) time();
        $signature = 'sha256=' . hash_hmac('sha256', $timestamp . '.' . $body, $this->secret);

        $response = $this->call(
            'POST',
            '/api/webhooks/sepay',
            [],
            [],
            [],
            [
                'HTTP_X_SEPAY_SIGNATURE' => $signature,
                'HTTP_X_SEPAY_TIMESTAMP' => $timestamp,
                'CONTENT_TYPE' => 'application/json',
            ],
            $body
        );

        $response->assertStatus(200);
        $user->refresh();
        $this->assertEquals(50000, $user->balance);
    }

    public function test_accepts_sepay_pattern_yuki001_with_code(): void
    {
        $user = User::create([
            'username' => 'yuki_code_user_' . uniqid(),
            'email' => 'yukicode_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'balance' => 0,
            'total_deposited' => 0,
        ]);

        $paddedId = str_pad((string)$user->id, 3, '0', STR_PAD_LEFT);
        $body = json_encode([
            'id' => 'TX_YUKI_CODE_' . time(),
            'transferAmount' => 100000,
            'code' => 'Yuki' . $paddedId,
            'content' => 'Yuki' . $paddedId . ' chuyen khoan naptien',
            'transferType' => 'in',
            'gateway' => 'MBBank',
        ]);
        $timestamp = (string) time();
        $signature = 'sha256=' . hash_hmac('sha256', $timestamp . '.' . $body, $this->secret);

        $response = $this->call(
            'POST',
            '/api/webhooks/sepay',
            [],
            [],
            [],
            [
                'HTTP_X_SEPAY_SIGNATURE' => $signature,
                'HTTP_X_SEPAY_TIMESTAMP' => $timestamp,
                'CONTENT_TYPE' => 'application/json',
            ],
            $body
        );

        $response->assertStatus(200);
        $user->refresh();
        $this->assertEquals(100000, $user->balance);
    }

    public function test_acknowledges_sepay_test_ping(): void
    {
        $body = json_encode([
            'id' => 0,
            'transferAmount' => 50000,
            'content' => 'DH12345 chuyen tien',
            'transferType' => 'in',
            'gateway' => 'Vietcombank',
        ]);
        $timestamp = (string) time();
        $signature = 'sha256=' . hash_hmac('sha256', $timestamp . '.' . $body, $this->secret);

        $response = $this->call(
            'POST',
            '/api/webhooks/sepay',
            [],
            [],
            [],
            [
                'HTTP_X_SEPAY_SIGNATURE' => $signature,
                'HTTP_X_SEPAY_TIMESTAMP' => $timestamp,
                'CONTENT_TYPE' => 'application/json',
            ],
            $body
        );

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }

    public function test_accepts_apikey_authorization_fallback(): void
    {
        $user = User::create([
            'username' => 'apikey_user_' . uniqid(),
            'email' => 'apikey_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'balance' => 0,
            'total_deposited' => 0,
        ]);

        $txId = 'SEPAY_APIKEY_' . time() . '_' . rand(1000, 9999);
        $response = $this->withHeaders([
            'Authorization' => 'Apikey ' . $this->secret,
        ])->postJson('/api/webhooks/sepay', [
            'id' => $txId,
            'transferAmount' => 50000,
            'content' => 'NAP' . $user->id,
            'gateway' => 'MBBank',
            'transferType' => 'in',
        ]);

        $response->assertStatus(200);
        $user->refresh();
        $this->assertEquals(50000, $user->balance);
    }

    public function test_ignores_outgoing_transfer(): void
    {
        $body = json_encode([
            'id' => 'TX_OUT_001',
            'transferAmount' => 100000,
            'content' => 'NAP1 thanh toan tien hang',
            'transferType' => 'out',
        ]);
        $timestamp = (string) time();
        $signature = 'sha256=' . hash_hmac('sha256', $timestamp . '.' . $body, $this->secret);

        $response = $this->call(
            'POST',
            '/api/webhooks/sepay',
            [],
            [],
            [],
            [
                'HTTP_X_SEPAY_SIGNATURE' => $signature,
                'HTTP_X_SEPAY_TIMESTAMP' => $timestamp,
                'CONTENT_TYPE' => 'application/json',
            ],
            $body
        );

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Ignored: not an incoming transfer']);
    }

    public function test_successfully_processes_deposit_and_affiliate_commission(): void
    {
        // Create referrer
        $referrer = User::create([
            'username' => 'referrer_user_' . uniqid(),
            'email' => 'referrer_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'balance' => 0,
            'total_commission' => 0,
        ]);

        // Create paying user
        $user = User::create([
            'username' => 'deposit_user_' . uniqid(),
            'email' => 'deposit_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'balance' => 10000,
            'total_deposited' => 0,
            'referrer_id' => $referrer->id,
        ]);

        $txId = 'SEPAY_' . time() . '_' . rand(1000, 9999);
        $amount = 100000;
        $body = json_encode([
            'id' => $txId,
            'transferAmount' => $amount,
            'content' => 'NAP' . $user->id . ' nap tien shopgame',
            'accountNumber' => '0123456789',
            'gateway' => 'Vietcombank',
            'transferType' => 'in',
        ]);
        $timestamp = (string) time();
        $signature = 'sha256=' . hash_hmac('sha256', $timestamp . '.' . $body, $this->secret);

        $response = $this->call(
            'POST',
            '/api/webhooks/sepay',
            [],
            [],
            [],
            [
                'HTTP_X_SEPAY_SIGNATURE' => $signature,
                'HTTP_X_SEPAY_TIMESTAMP' => $timestamp,
                'CONTENT_TYPE' => 'application/json',
            ],
            $body
        );

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Webhook received']);

        // Check user balance
        $user->refresh();
        $this->assertEquals(110000, $user->balance);
        $this->assertEquals(100000, $user->total_deposited);

        // Check BankDeposit record
        $deposit = BankDeposit::where('transaction_id', $txId)->first();
        $this->assertNotNull($deposit);
        $this->assertEquals($user->id, $deposit->user_id);
        $this->assertEquals(100000, $deposit->amount);
        $this->assertEquals('sepay', $deposit->gateway);
        $this->assertEquals('completed', $deposit->status);

        // Check MoneyTransaction record
        $moneyTx = MoneyTransaction::where('reference_id', $txId)
            ->where('type', 'deposit')
            ->first();
        $this->assertNotNull($moneyTx);
        $this->assertEquals(10000, $moneyTx->balance_before);
        $this->assertEquals(110000, $moneyTx->balance_after);

        // Check Affiliate Commission (10% of 100,000 = 10,000)
        $referrer->refresh();
        $this->assertEquals(10000, $referrer->balance);
        $this->assertEquals(10000, $referrer->total_commission);

        $affiliateHistory = AffiliateHistory::where('referrer_id', $referrer->id)
            ->where('referred_id', $user->id)
            ->first();
        $this->assertNotNull($affiliateHistory);
        $this->assertEquals(10000, $affiliateHistory->commission_amount);

        // Test Idempotency: duplicate request should not credit again
        $dupResponse = $this->call(
            'POST',
            '/api/webhooks/sepay',
            [],
            [],
            [],
            [
                'HTTP_X_SEPAY_SIGNATURE' => $signature,
                'HTTP_X_SEPAY_TIMESTAMP' => $timestamp,
                'CONTENT_TYPE' => 'application/json',
            ],
            $body
        );
        $dupResponse->assertStatus(200);

        $user->refresh();
        $this->assertEquals(110000, $user->balance, 'Balance should not increase on duplicate transaction');
    }
}
