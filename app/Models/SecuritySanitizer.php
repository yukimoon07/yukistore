<?php

namespace App\Models;

/**
 * Sanitizer dùng chung cho log — mask dữ liệu nhạy cảm trước khi ghi DB.
 */
class SecuritySanitizer
{
    /** Các key phải mask hoàn toàn */
    public const SENSITIVE_KEYS = [
        'password',
        'partner_key',
        'partner_key_card',
        'api_token',
        'secret',
        'secret_key',
        'spay5s_token',
        'two_factor_secret',
        'wallet_private',
        'config',
    ];

    /** Các key chỉ ghi chú "đã thay đổi", không lưu nội dung */
    public const NOTE_KEYS = [
        'account_name',   // username tài khoản game — chỉ ghi khi tạo/xóa, cập nhật mask
        'game_password',  // mật khẩu form thuê dịch vụ
    ];

    public static function mask(array $data): array
    {
        foreach ($data as $key => $value) {
            $lower = strtolower((string) $key);

            if (in_array($lower, self::SENSITIVE_KEYS, true)) {
                $data[$key] = '••••••(masked)';
            } elseif (in_array($lower, self::NOTE_KEYS, true)) {
                $data[$key] = strlen((string) $value) > 0 ? '***' : $value;
            } elseif (is_array($value)) {
                $data[$key] = self::mask($value);
            } elseif (is_string($value) && strlen($value) > 2000) {
                $data[$key] = substr($value, 0, 2000) . '…(truncated)';
            }
        }

        return $data;
    }
}
