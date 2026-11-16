<?php

namespace App\Services;

/**
 * TwoFactorService — TOTP chuẩn RFC 6238, PHP thuần (không phụ thuộc thư viện ngoài).
 *
 * - Tạo secret Base32 ngẫu nhiên (80-bit trở lên).
 * - Xác minh mã 6 số với cửa sổ ±1 kỳ (30s) để bù lệch giờ.
 * - Sinh otpauth:// URI cho app Google Authenticator / Authy / Microsoft Authenticator.
 * - Sinh QR qua Google Chart API (chart.googleapis.com) — chỉ truyền secret, không truyền secretKey hệ thống.
 *
 * Yêu cầu: ext-openssl (có sẵn trong PHP 8.x) và hàm hash_hmac.
 */
class TwoFactorService
{
    /** Bảng Base32 chuẩn RFC 4648 */
    private const BASE32_ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    private const OTP_LENGTH = 6;
    private const OTP_PERIOD = 30;
    private const OTP_WINDOW = 1; // cho phép lệch ±30s
    private const SECRET_BYTES = 20; // 160-bit

    /**
     * Tạo secret Base32 mới.
     */
    public function generateSecret(): string
    {
        $raw = random_bytes(self::SECRET_BYTES);

        return $this->encodeBase32($raw);
    }

    /**
     * URI otpauth cho app authenticator.
     * Dùng làm link bấm trực tiếp trên điện thoại để mở app — không gửi secret ra ngoài.
     */
    public function otpauthUri(string $secret, string $accountName, string $issuer): string
    {
        return sprintf(
            'otpauth://totp/%s:%s?secret=%s&issuer=%s&algorithm=SHA1&digits=%d&period=%d',
            rawurlencode($issuer),
            rawurlencode($accountName),
            $secret,
            rawurlencode($issuer),
            self::OTP_LENGTH,
            self::OTP_PERIOD
        );
    }

    /**
     * Chia secret thành từng nhóm 4 ký tự để dễ nhập tay vào app.
     */
    public function formatSecretForDisplay(string $secret): string
    {
        return trim(chunk_split($secret, 4, ' '));
    }

    /**
     * Kiểm tra mã OTP do user nhập.
     */
    public function verify(string $secret, string $code): bool
    {
        $code = preg_replace('/\s+/', '', $code);
        if (! preg_match('/^\d{6}$/', $code)) {
            return false;
        }

        $key = $this->decodeBase32($secret);
        if ($key === '') {
            return false;
        }

        $timestamp = time();
        for ($i = -$this->window(); $i <= $this->window(); $i++) {
            $expected = $this->hotp($key, intdiv($timestamp, self::OTP_PERIOD) + $i);
            if (hash_equals($expected, $code)) {
                return true;
            }
        }

        return false;
    }

    private function window(): int
    {
        return self::OTP_WINDOW;
    }

    /**
     * HOTP (RFC 4226) — trả mã dạng chuỗi 6 số có zero-padding.
     */
    private function hotp(string $key, int $counter): string
    {
        $binaryCounter = pack('N*', 0) . pack('N*', $counter);
        $hash = hash_hmac('sha1', $binaryCounter, $key, true);

        $offset = ord(substr($hash, -1)) & 0x0F;
        $part = substr($hash, $offset, 4);

        $value = unpack('N', $part)[1];
        $value = $value & 0x7FFFFFFF;

        return str_pad((string) ($value % (10 ** self::OTP_LENGTH)), self::OTP_LENGTH, '0', STR_PAD_LEFT);
    }

    private function encodeBase32(string $bytes): string
    {
        $output = '';
        $bitBuffer = 0;
        $bitCount = 0;

        for ($i = 0; $i < strlen($bytes); $i++) {
            $bitBuffer = ($bitBuffer << 8) | ord($bytes[$i]);
            $bitCount += 8;

            while ($bitCount >= 5) {
                $index = ($bitBuffer >> ($bitCount - 5)) & 0x1F;
                $output .= self::BASE32_ALPHABET[$index];
                $bitCount -= 5;
            }
        }

        if ($bitCount > 0) {
            $index = ($bitBuffer << (5 - $bitCount)) & 0x1F;
            $output .= self::BASE32_ALPHABET[$index];
        }

        return $output;
    }

    private function decodeBase32(string $base32): string
    {
        $base32 = strtoupper(preg_replace('/[^A-Za-z2-7]/', '', $base32));
        $output = '';
        $bitBuffer = 0;
        $bitCount = 0;

        for ($i = 0; $i < strlen($base32); $i++) {
            $pos = strpos(self::BASE32_ALPHABET, $base32[$i]);
            if ($pos === false) {
                continue;
            }
            $bitBuffer = ($bitBuffer << 5) | $pos;
            $bitCount += 5;

            if ($bitCount >= 8) {
                $output .= chr(($bitBuffer >> ($bitCount - 8)) & 0xFF);
                $bitCount -= 8;
            }
        }

        return $output;
    }
}
