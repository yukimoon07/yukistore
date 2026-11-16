<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Seeder chính — AN TOÀN CHO PRODUCTION.
 *
 * Chỉ seed những dữ liệu mang tính CẤU TRÚC / DANH MỤC KHUNG:
 * cấu hình website (rỗng, chờ Admin nhập), danh mục game, dịch vụ game.
 *
 * TUYỆT ĐỐI KHÔNG seed ở đây:
 *  - Tài khoản quản trị có mật khẩu cố định (dùng: php artisan admin:create-admin)
 *  - Khách hàng giả
 *  - Tài khoản game demo
 *  - Giá dịch vụ demo
 *  - Lịch sử giao dịch giả
 *
 * Nếu cần dữ liệu demo để làm giao diện ở local, chạy riêng:
 *   php artisan db:seed --class="Database\\Seeders\\LocalDemo\\LocalDemoSeeder"
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('production')) {
            $this->command?->warn('Đang seed trên môi trường PRODUCTION — chỉ seed dữ liệu cấu trúc, không seed dữ liệu demo.');
        }

        $this->call([
            ConfigSeeder::class,
            GameCategoriesTableSeeder::class,
            RandomCategoriesTableSeeder::class,
            GameServicesSeeder::class,
            LuckyWheelsTableSeeder::class,
        ]);

        $this->command?->info('Đã seed dữ liệu khung. Bước tiếp theo:');
        $this->command?->line('  1) php artisan admin:create-admin');
        $this->command?->line('  2) Đăng nhập Admin Panel → Cài đặt hệ thống để nhập thông tin thương hiệu.');
        $this->command?->line('  3) Thêm tài khoản game và giá dịch vụ CHÍNH THỨC trong Admin Panel.');
    }
}