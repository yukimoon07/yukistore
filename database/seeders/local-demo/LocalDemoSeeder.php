<?php

namespace Database\Seeders\LocalDemo;

use Illuminate\Database\Seeder;

/**
 * Seeder DỮ LIỆU DEMO — CHỈ dùng cho môi trường LOCAL/PHÁT TRIỂN.
 *
 * BẢO VỆ SẢN XUẤT: Lớp này KHÔNG bao giờ được gọi từ DatabaseSeeder
 * trong môi trường production. DatabaseSeeder đã tự chặn nếu APP_ENV=production.
 *
 * Mục đích: giúp lập trình viên dev có dữ liệu giả để thử giao diện:
 * tài khoản game giả, giao dịch giả, vòng quay may mắn giả, tài khoản ngân hàng giả.
 *
 * Cách dùng (chỉ ở local):
 *   php artisan db:seed --class="Database\\Seeders\\LocalDemo\\LocalDemoSeeder"
 *
 * ĐỪNG BAO GIỜ chạy lệnh này trên database thật của khách hàng.
 */
class LocalDemoSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('production')) {
            $this->command?->error('TỪ CHỐI: Seeder demo không được phép chạy trên môi trường production.');
            return;
        }

        $this->call([
            UsersTableSeeder::class,
            GameAccountSeeder::class,
            RandomCategoryAccountsTableSeeder::class,
            ServicePackagesSeeder::class,
            LuckyWheelHistoriesTableSeeder::class,
            MoneyWithdrawalHistoriesTableSeeder::class,
            WithdrawalHistoriesTableSeeder::class,
            NotificationsTableSeeder::class,
            BankAccountSeeder::class,
        ]);

        $this->command?->info('Đã seed dữ liệu DEMO (chỉ dùng ở local).');
    }
}