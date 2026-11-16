<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Sprint 1 — Hệ thống 6 nhóm kinh doanh (Business Units)
 * Accounts | Boosting Services | Link Monetization | Tools | Cloud Phone | Other
 *
 * Dữ liệu 6 nhóm được seed rỗng sản phẩm (chỉ danh mục), KHÔNG seed sản phẩm mẫu.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_units', function (Blueprint $table) {
            $table->id();
            $table->string('name');                       // Tên hiển thị
            $table->string('slug')->unique();             // Đường dẫn
            $table->string('icon')->nullable();           // Mã icon (iconify), ví dụ "ph:sword"
            $table->string('image')->nullable();          // Ảnh đại diện (nếu admin upload)
            $table->text('description')->nullable();      // Mô tả ngắn
            $table->integer('sort_order')->default(0);    // Thứ tự hiển thị
            $table->boolean('is_active')->default(true);  // Ẩn/hiện
            $table->timestamps();
        });

        // 6 nhóm kinh doanh mặc định theo yêu cầu — có thể sửa/xóa/ẩn từ Admin Panel sau.
        $units = [
            ['Accounts',         'accounts',          'ph:sword-bold',          1],
            ['Boosting Services','boosting-services', 'ph:lightning-bold',      2],
            ['Link Monetization','link-monetization', 'ph:link-bold',           3],
            ['Tools',            'tools',             'ph:wrench-bold',         4],
            ['Cloud Phone',      'cloud-phone',       'ph:device-mobile-bold',  5],
            ['Other',            'other',             'ph:package-bold',        6],
        ];

        foreach ($units as [$name, $slug, $icon, $order]) {
            DB::table('business_units')->insert([
                'name' => $name,
                'slug' => $slug,
                'icon' => $icon,
                'sort_order' => $order,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Gắn các loại sản phẩm hiện có về nhóm tương ứng (nullable để không ép buộc dữ liệu cũ)
        Schema::table('game_categories', function (Blueprint $table) {
            $table->foreignId('business_unit_id')->nullable()
                ->constrained('business_units')->nullOnDelete();
            $table->integer('sort_order')->default(0);
        });

        Schema::table('game_services', function (Blueprint $table) {
            $table->foreignId('business_unit_id')->nullable()
                ->constrained('business_units')->nullOnDelete();
        });

        Schema::table('random_categories', function (Blueprint $table) {
            $table->foreignId('business_unit_id')->nullable()
                ->constrained('business_units')->nullOnDelete();
            $table->integer('sort_order')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('game_categories', function (Blueprint $table) {
            $table->dropConstrainedForeignId('business_unit_id');
            $table->dropColumn('sort_order');
        });
        Schema::table('game_services', function (Blueprint $table) {
            $table->dropConstrainedForeignId('business_unit_id');
        });
        Schema::table('random_categories', function (Blueprint $table) {
            $table->dropConstrainedForeignId('business_unit_id');
            $table->dropColumn('sort_order');
        });
        Schema::dropIfExists('business_units');
    }
};
