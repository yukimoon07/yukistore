<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

/**
 * User — bổ sung thuộc tính 2FA (two_factor_secret, recovery codes).
 * ĐÃ CÓ trong dự án: HasApiTokens, role enum, password hash...
 * File này KHÔNG thay model User gốc mà thêm trait để tránh xung đột merge.
 * => User gốc (app/Models/User.php) sẽ thêm 2 method nhỏ ở bước tích hợp.
 */
trait TwoFactorAuthenticatable
{
    public function hasTwoFactorEnabled(): bool
    {
        return ! empty($this->two_factor_secret) && ! empty($this->two_factor_confirmed_at);
    }

    public function getTwoFactorSecret(): ?string
    {
        if (empty($this->two_factor_secret)) {
            return null;
        }

        try {
            return Crypt::decryptString($this->two_factor_secret);
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function setTwoFactorSecret(string $plainSecret): void
    {
        $this->two_factor_secret = Crypt::encryptString($plainSecret);
    }

    public function generateRecoveryCodes(): array
    {
        $codes = [];
        for ($i = 0; $i < 8; $i++) {
            $codes[] = Str::upper(Str::random(5)) . '-' . Str::upper(Str::random(5));
        }

        $this->two_factor_recovery_codes = Crypt::encryptString(json_encode(
            array_map(fn ($c) => bcrypt($c), $codes)
        ));
        $this->save();

        return $codes; // Chỉ hiện 1 lần cho user
    }

    public function consumeRecoveryCode(string $code): bool
    {
        if (empty($this->two_factor_recovery_codes)) {
            return false;
        }

        try {
            $hashed = json_decode(Crypt::decryptString($this->two_factor_recovery_codes), true) ?: [];
        } catch (\Throwable $e) {
            return false;
        }

        foreach ($hashed as $index => $hash) {
            if (password_verify($code, $hash)) {
                unset($hashed[$index]); // Mã 1 lần dùng
                $this->two_factor_recovery_codes = Crypt::encryptString(json_encode(array_values($hashed)));
                $this->save();

                return true;
            }
        }

        return false;
    }
}
