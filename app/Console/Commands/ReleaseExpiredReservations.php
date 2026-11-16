<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Tự nhả tài khoản "reserved" đã quá hạn giữ chỗ về "available".
 * Chạy định kỳ qua cron: * * * * * php artisan accounts:release-expired-reservations
 */
class ReleaseExpiredReservations extends Command
{
    protected $signature = 'accounts:release-expired-reservations';

    protected $description = 'Chuyển tài khoản reserved quá hạn giữ chỗ về available';

    public function handle(): int
    {
        $now = now();
        $total = 0;

        foreach (['game_accounts', 'random_category_accounts'] as $table) {
            $updated = DB::table($table)
                ->where('stock_status', 'reserved')
                ->whereNotNull('reserved_until')
                ->where('reserved_until', '<=', $now)
                ->update([
                    'stock_status'  => 'available',
                    'reserved_until' => null,
                    'updated_at'    => $now,
                ]);

            $this->info("{$table}: đã nhả " . $updated . ' tài khoản.');
            $total += $updated;
        }

        $this->info('Tổng: ' . $total . ' tài khoản đã được nhả.');

        return self::SUCCESS;
    }
}
