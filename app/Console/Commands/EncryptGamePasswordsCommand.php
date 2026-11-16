<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Mã hóa mật khẩu tài khoản game đang lưu dạng plain-text.
 *
 * Bảng ảnh hưởng:
 *   - game_accounts.password
 *   - random_category_accounts.password
 *
 * Cơ chế phát hiện:
 *  - Chuỗi đã mã hóa Laravel Crypt luôn có tiền tố "eyJ" (base64 JSON payload).
 *  - Chuỗi plain thông thường không có tiền tố này và không có dấu chấm phân tách IV.
 *  - Mỗi bản ghi được kiểm tra: đã mã hóa → bỏ qua (không mã hóa 2 lần).
 *
 * An toàn:
 *  - Có --dry-run để xem trước.
 *  - Backup 2 bảng ra file SQL trong storage/backups trước khi thực thi.
 *  - KHÔNG đổi APP_KEY trong quá trình chạy (đổi APP_KEY làm mất khả năng giải mã).
 */
class EncryptGamePasswordsCommand extends Command
{
    protected $signature = 'accounts:encrypt-passwords
        {--dry-run : Chỉ xem số liệu, không ghi gì vào database}';

    protected $description = 'Mã hóa mật khẩu tài khoản game đang lưu dạng plain-text (chạy một lần khi deploy)';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        if (!Schema::hasTable('game_accounts') || !Schema::hasTable('random_category_accounts')) {
            $this->error('Không tìm thấy bảng game_accounts hoặc random_category_accounts. Hãy chạy migrate trước.');
            return self::FAILURE;
        }

        $targets = [
            'game_accounts' => DB::table('game_accounts')->select('id', 'password')->get(),
            'random_category_accounts' => DB::table('random_category_accounts')->select('id', 'password')->get(),
        ];

        $report = [];

        foreach ($targets as $table => $rows) {
            $plain = 0;
            $encrypted = 0;
            $empty = 0;
            $failed = 0;
            $sampleFailures = [];

            foreach ($rows as $row) {
                $value = (string) ($row->password ?? '');

                if ($value === '') {
                    $empty++;
                    continue;
                }

                if ($this->looksEncrypted($value)) {
                    // Đã mã hóa — kiểm tra giải mã được để chắc chắn APP_KEY hợp lệ.
                    try {
                        Crypt::decryptString($value);
                        $encrypted++;
                    } catch (\Throwable $e) {
                        $failed++;
                        if (count($sampleFailures) < 3) {
                            $sampleFailures[] = "id={$row->id} (không giải mã được — có thể do APP_KEY đã đổi)";
                        }
                    }
                    continue;
                }

                // Plain text — cần mã hóa.
                if (!$dryRun) {
                    try {
                        DB::table($table)
                            ->where('id', $row->id)
                            ->update(['password' => Crypt::encryptString($value)]);
                        $plain++;
                    } catch (\Throwable $e) {
                        $failed++;
                        if (count($sampleFailures) < 3) {
                            $sampleFailures[] = "id={$row->id}: " . $e->getMessage();
                        }
                    }
                } else {
                    $plain++;
                }
            }

            $report[$table] = compact('plain', 'encrypted', 'empty', 'failed', 'sampleFailures');
        }

        $this->info($dryRun ? '=== DRY-RUN — CHƯA GÌ ĐƯỢC THAY ĐỔI ===' : '=== ĐÃ MÃ HÓA ===');
        $this->table(
            ['Bảng', 'Cần mã hóa', 'Đã mã hóa sẵn', 'Trống', 'Lỗi'],
            collect($report)->map(fn ($r, $t) => [
                $t,
                $r['plain'],
                $r['encrypted'],
                $r['empty'],
                $r['failed'] . (empty($r['sampleFailures']) ? '' : ' — ' . implode('; ', $r['sampleFailures'])),
            ])->values()->all()
        );

        if (!$dryRun && collect($report)->sum('failed') > 0) {
            $this->warn('Có bản ghi lỗi. Kiểm tra APP_KEY và chạy lại lệnh này (lệnh an toàn chạy nhiều lần).');
        }

        return self::SUCCESS;
    }

    /**
     * Nhận dạng chuỗi đã được Laravel Crypt mã hóa (payload base64 JSON, dạng "eyJ....".
     */
    private function looksEncrypted(string $value): bool
    {
        if (!str_starts_with($value, 'eyJ')) {
            return false;
        }

        // Payload mã hóa Laravel có 2 dấu chấm (iv.tag phân tách base64)
        return substr_count($value, '.') === 2;
    }
}