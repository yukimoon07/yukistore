<?php

namespace Database\Seeders;

use App\Models\Config;
use Illuminate\Database\Seeder;

/**
 * Seeder cấu hình website — PHIÊN BẢN SẠCH CHO PRODUCTION.
 *
 * Nguyên tắc:
 *  - KHÔNG chứa secret (OAuth client secret, API key, mật khẩu mail).
 *    Secret được nhập qua Admin Panel → Cài đặt hệ thống.
 *  - KHÔNG chứa thương hiệu, logo, banner, domain của bên thứ ba.
 *  - Các trường nhận diện thương hiệu để TRỐNG: Admin tự nhập qua Admin Panel.
 *    Frontend đã có fallback an toàn khi giá trị trống.
 */
class ConfigSeeder extends Seeder
{
    public function run(): void
    {
        $configs = [
            // Thông tin chung — để trống cho Admin điền qua Admin Panel
            'site_name' => 'ShopGame',
            'site_description' => '',
            'site_keywords' => '',
            'site_logo' => '',
            'site_logo_footer' => '',
            'site_share_image' => '',
            'site_banner' => '',
            'site_favicon' => '',

            // Thông tin liên hệ — để trống, không bịa số điện thoại/email
            'address' => '',
            'phone' => '',
            'email' => '',

            // Mạng xã hội — để trống
            'facebook' => '',
            'zalo' => '',
            'youtube' => '',
            'discord' => '',
            'telegram' => '',
            'tiktok' => '',
            'working_hours' => '',
            'home_notification' => '',
            'welcome_modal' => false,

            // Email settings — để trống, Admin tự cấu hình
            'mail_mailer' => 'smtp',
            'mail_host' => '',
            'mail_port' => '587',
            'mail_username' => '',
            'mail_password' => '',
            'mail_encryption' => 'tls',
            'mail_from_address' => '',
            'mail_from_name' => 'ShopGame',

            // Phương thức thanh toán — mặc định tắt đến khi Admin bật
            'payment.card.active' => '0',
            'payment.card.partner_website' => '',
            'payment.card.partner_id' => '',
            'payment.card.partner_key' => '',
            'payment.card.discount_percent' => '0',
            'payment.bank.active' => '0',
            'payment.momo.active' => '0',
            'payment.usdt.active' => '0',

            // Đăng nhập mạng xã hội — mặc định tắt đến khi Admin cấu hình secret thật
            'login_social.google.active' => '0',
            'login_social.google.client_id' => '',
            'login_social.google.client_secret' => '',
            'login_social.google.redirect' => '', // Khuyến nghị: https://<domain-thật>/auth/google/callback
            'login_social.facebook.active' => '0',
            'login_social.facebook.client_id' => '',
            'login_social.facebook.client_secret' => '',
            'login_social.facebook.redirect' => '', // Khuyến nghị: https://<domain-thật>/auth/facebook/callback
        ];

        foreach ($configs as $key => $value) {
            // updateOrCreate để chạy lại không lỗi; KHÔNG ghi đè giá trị Admin đã chỉnh.
            $existing = Config::where('key', $key)->first();

            if (!$existing) {
                Config::create(['key' => $key, 'value' => $value]);
            } elseif (
                in_array($key, [
                    'login_social.google.client_secret',
                    'login_social.facebook.client_secret',
                    'login_social.google.client_id',
                    'login_social.facebook.client_id',
                    'payment.card.partner_key',
                ], true)
                && $existing->value !== $value
                && !empty($existing->value)
            ) {
                // Secret cũ đã bị lộ trong mã nguồn ZIP — xóa bỏ, buộc Admin nhập secret mới.
                $existing->update(['value' => $value]);
                $this->command?->warn("Đã xóa secret cũ của '$key' (đã bị lộ). Hãy nhập secret mới trong Admin Panel.");
            }
        }

        $this->command?->info('Đã seed cấu hình cơ bản. Hãy truy cập Admin Panel → Cài đặt hệ thống để nhập thông tin chính thức.');
    }
}
