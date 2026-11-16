<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        $this->setStatusEnum(['available', 'sold', 'installment']);
    }

    public function down()
    {
        $this->setStatusEnum(['available', 'sold']);
    }

    /**
     * Đặt lại tập giá trị hợp lệ cho cột game_accounts.status.
     * - MySQL/MariaDB: dùng ALTER ... MODIFY COLUMN ENUM (như bản gốc).
     * - PostgreSQL (vd Supabase): Laravel tạo enum dạng varchar + CHECK constraint;
     *   ta thay constraint đó để thêm/bớt giá trị.
     */
    private function setStatusEnum(array $values): void
    {
        $driver = DB::connection()->getDriverName();

        if (in_array($driver, ['pgsql'], true)) {
            $list = implode(', ', array_map(fn ($v) => "'" . $v . "'", $values));
            DB::statement('ALTER TABLE game_accounts DROP CONSTRAINT IF EXISTS game_accounts_status_check');
            DB::statement("ALTER TABLE game_accounts ADD CONSTRAINT game_accounts_status_check CHECK (status IN ({$list}))");
            return;
        }

        // MySQL / MariaDB (giữ nguyên hành vi gốc).
        $enum = implode(', ', array_map(fn ($v) => "'" . $v . "'", $values));
        DB::statement("ALTER TABLE game_accounts MODIFY COLUMN status ENUM({$enum}) NOT NULL DEFAULT 'available'");
    }
};
