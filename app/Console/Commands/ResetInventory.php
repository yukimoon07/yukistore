<?php

namespace App\Console\Commands;

use App\Models\AdminActivityLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * RESET KHO TÀI KHOẢN — lệnh an toàn theo kế hoạch DATABASE_AND_SYNC_PLAN.md Phần II.
 *
 * Chỉ xóa đúng 4 bảng kho tài khoản (giữ lại game_groups theo phê duyệt):
 *   - game_accounts
 *   - game_categories
 *   - random_categories
 *   - random_category_accounts
 *
 * Quy trình bắt buộc:
 *   1. Backup mysqldump trước (tự động thực hiện vào storage/backups nếu mysqld có sẵn)
 *   2. Hiển thị COUNT trước khi xóa — yêu cầu gõ "RESET" để xác nhận
 *   3. DELETE trong transaction + báo cáo số dòng xóa
 *   4. Reset AUTO_INCREMENT về 1
 *   5. Ghi Admin Activity Log
 *
 * Cách chạy:
 *   php artisan accounts:reset-inventory          → hỏi xác nhận
 *   php artisan accounts:reset-inventory --force  → bỏ qua xác nhận (dùng sau khi đã xem thống kê)
 *   php artisan accounts:reset-inventory --dry-run → chỉ xem số liệu, KHÔNG xóa
 */
class ResetInventory extends Command
{
    protected $signature = 'accounts:reset-inventory
                            {--force : Bỏ qua bước xác nhận gõ tay}
                            {--dry-run : Chỉ hiển thị số liệu, không xóa}';

    protected $description = 'Reset toàn bộ kho tài khoản game về 0 (xóa 4 bảng kho, giữ users/orders/payments/game_groups)';

    /** Các bảng sẽ xóa — KHÔNG thêm bảng nào khác nếu không có phê duyệt mới */
    private const INVENTORY_TABLES = [
        'game_accounts',
        'game_categories',
        'random_categories',
        'random_category_accounts',
    ];

    public function handle(): int
    {
        $this->info('==============================================');
        $this->info('   RESET KHO TÀI KHOẢN GAME — SHOPGAME');
        $this->info('==============================================');

        // ===== BƯỚC 1: Kiểm tra bảng tồn tại + đếm số liệu =====
        $stats = [];
        $totalRows = 0;

        foreach (self::INVENTORY_TABLES as $table) {
            if (! Schema::hasTable($table)) {
                $this->error("Bảng {$table} không tồn tại — dừng để an toàn.");

                return self::FAILURE;
            }
            $count = DB::table($table)->count();
            $stats[$table] = $count;
            $totalRows += $count;
        }

        // Bảng liên quan có FK vào kho — kiểm tra để cảnh báo
        $dependent = [
            'purchase_history' => DB::table('purchase_history')->count(),
            'installments'     => Schema::hasTable('installments') ? DB::table('installments')->count() : 0,
        ];

        $this->table(
            ['Bảng', 'Số bản ghi sẽ bị XÓA'],
            array_map(fn ($t, $c) => [$t, number_format($c)], array_keys($stats), $stats)
        );

        $this->line('');
        $this->info('Các bảng KHÔNG bị đụng đến (bảo toàn):');
        $this->line('  users, money_transactions, purchase_history (' . number_format($dependent['purchase_history']) . ' dòng), installments,');
        $this->line('  game_groups (giữ lại theo phê duyệt), configs, bank_accounts, card_deposits, bank_deposits,');
        $this->line('  usdt_*, withdrawal_*, lucky_*, missions, news, notifications, payment_methods, service_histories…');
        $this->line('');

        if ($totalRows === 0) {
            $this->info('Kho đã trống từ trước — không có gì để xóa.');

            return self::SUCCESS;
        }

        if ($dependent['purchase_history'] > 0) {
            $this->warn('CẢNH BÁO: purchase_history có ' . $dependent['purchase_history'] .
                ' bản ghi tham chiếu game_accounts (FK CASCADE). Xóa kho sẽ CASCADE làm mất các dòng history này.');
            $this->warn('Theo nguyên tắc "không xóa lịch sử đơn hàng", hãy cân nhắc: các bản ghi là dữ liệu mua hàng cũ thật.');
            if (! $this->confirm('Tiếp tục với lịch sử mua hàng hiện có?', false)) {
                $this->info('Đã hủy. Không xóa gì cả.');

                return self::SUCCESS;
            }
        }

        if ($this->option('dry-run')) {
            $this->info('DRY-RUN: chưa xóa gì. Chạy lại không có --dry-run để thực hiện.');

            return self::SUCCESS;
        }

        // ===== BƯỚC 2: Backup =====
        $backupDir = storage_path('backups/reset-' . date('Ymd-His'));
        if (! is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $this->info('Đang backup 4 bảng vào: ' . $backupDir);
        $backupOk = $this->backupTables($backupDir);

        if (! $backupOk) {
            $this->warn('Không tìm thấy mysqldump trong PATH — hãy tự backup bằng HeidiSQL/phpMyAdmin trước khi tiếp tục.');
            if (! $this->confirm('Tôi ĐÃ backup xong, tiếp tục xóa?', false)) {
                $this->info('Đã hủy. Không xóa gì cả.');

                return self::SUCCESS;
            }
        } else {
            $this->info('Backup xong: ' . implode(', ', glob($backupDir . '/*.sql')));
        }

        // ===== BƯỚC 3: Xác nhận =====
        if (! $this->option('force')) {
            $this->warn('Gõ chính xác chữ RESET (chữ hoa) để xác nhận xóa ' . number_format($totalRows) . ' bản ghi:');
            $answer = $this->ask('Xác nhận');
            if ($answer !== 'RESET') {
                $this->info('Xác nhận không đúng — đã hủy, không xóa gì cả.');

                return self::SUCCESS;
            }
        }

        // ===== BƯỚC 4: Xóa trong transaction =====
        $this->info('Đang xóa trong transaction…');
        $deleted = [];

        DB::beginTransaction();
        try {
            foreach (self::INVENTORY_TABLES as $table) {
                $deleted[$table] = DB::table($table)->delete();
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error('LỖI khi xóa — đã ROLLBACK toàn bộ, dữ liệu nguyên vẹn: ' . $e->getMessage());

            return self::FAILURE;
        }

        // ===== BƯỚC 5: Reset AUTO_INCREMENT / sequence =====
        foreach (self::INVENTORY_TABLES as $table) {
            try {
                if (DB::connection()->getDriverName() === 'pgsql') {
                    // PostgreSQL (vd Supabase): đặt sequence về 1
                    DB::statement("ALTER SEQUENCE IF EXISTS {$table}_id_seq RESTART WITH 1");
                } else {
                    DB::statement("ALTER TABLE `{$table}` AUTO_INCREMENT = 1");
                }
            } catch (\Throwable $e) {
                $this->warn('Không reset được AUTO_INCREMENT cho ' . $table . ': ' . $e->getMessage());
            }
        }

        // ===== BƯỚC 6: Ghi log quản trị =====
        try {
            AdminActivityLog::record('inventory_reset', null, $stats, $deleted);
        } catch (\Throwable $e) {
            $this->warn('Không ghi được activity log (có thể chưa migrate bảng log): ' . $e->getMessage());
        }

        $this->info('');
        $this->info('================= KẾT QUẢ =================');
        foreach ($deleted as $table => $rows) {
            $this->info("  {$table}: đã xóa " . number_format($rows) . ' bản ghi');
        }
        $this->info('AUTO_INCREMENT đã reset về 1 cho 4 bảng.');
        $this->info('Backup lưu tại: ' . $backupDir);
        $this->info('Hoàn tất — kho tài khoản đã về 0. Website sẽ hiển thị trống cho đến khi Admin thêm mới.');

        return self::SUCCESS;
    }

    private function backupTables(string $dir): bool
    {
        $mysqldump = 'mysqldump';
        if (PHP_OS_FAMILY === 'Windows') {
            // Laragon mặc định: C:\laragon\bin\mysql\...\bin\mysqldump.exe
            $globs = glob('C:\\laragon\\bin\\mysql\\*\\bin\\mysqldump.exe');
            if (! empty($globs)) {
                $mysqldump = '"' . end($globs) . '"';
            }
        }

        $db   = config('database.connections.mysql.database');
        $user = config('database.connections.mysql.username');
        $pass = config('database.connections.mysql.password');
        $host = config('database.connections.mysql.host');

        $ok = true;
        foreach (self::INVENTORY_TABLES as $table) {
            $file = $dir . DIRECTORY_SEPARATOR . $table . '.sql';
            $cmd = $mysqldump . ' --no-tablespaces'
                . ' -h ' . escapeshellarg($host)
                . ' -u ' . escapeshellarg($user)
                . ($pass !== '' && $pass !== null ? ' -p' . escapeshellarg($pass) : '')
                . ' ' . escapeshellarg($db) . ' ' . escapeshellarg($table)
                . ' > ' . escapeshellarg($file);

            exec($cmd . ' 2>&1', $out, $code);
            if ($code !== 0) {
                $ok = false;
            }
        }

        return $ok;
    }
}
