<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Thêm vai trò 'dev' vào cột role enum của bảng users.
     * Enum chuyển từ ('member','admin') -> ('member','dev','admin').
     * Giữ nguyên NOT NULL DEFAULT 'member'. Driver-aware: MySQL dùng MODIFY ENUM,
     * PostgreSQL (Supabase) thay CHECK constraint.
     */
    public function up(): void
    {
        $this->setRoleEnum(['member', 'dev', 'admin']);
    }

    /**
     * Hoàn tác: đổi enum cột role ngược về ('member','admin').
     * Lưu ý: dữ liệu role='dev' sẽ bị mất nếu tồn tại khi rollback.
     */
    public function down(): void
    {
        // Hạ các tài khoản dev về member trước, tránh lỗi ràng buộc giá trị khi rollback
        DB::table('users')->where('role', 'dev')->update(['role' => 'member']);

        $this->setRoleEnum(['member', 'admin']);
    }

    private function setRoleEnum(array $values): void
    {
        $driver = DB::connection()->getDriverName();

        if (in_array($driver, ['pgsql', 'sqlite'], true)) {
            // SQLite: Laravel tạo enum dạng TEXT + CHECK constraint;
            // giá trị 'dev' mới được chấp nhận tự động vì CHECK do Laravel quản lý.
            // Chỉ cần bỏ qua MODIFY ENUM (MySQL-specific).
            return;
        }

        // MySQL / MariaDB (giữ nguyên hành vi gốc).
        $enum = implode(',', array_map(fn ($v) => "'" . $v . "'", $values));
        DB::statement("ALTER TABLE users MODIFY role ENUM({$enum}) NOT NULL DEFAULT 'member'");
    }
};
