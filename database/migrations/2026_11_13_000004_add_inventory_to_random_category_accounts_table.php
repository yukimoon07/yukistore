<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Sprint 1 — Account Inventory cho tài khoản random:
 * mở rộng enum status available|sold → thêm reserved|disabled qua cột stock_status mới.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('random_category_accounts', function (Blueprint $table) {
            $table->enum('stock_status', ['available', 'reserved', 'sold', 'disabled'])
                ->default('available');
            $table->boolean('is_visible')->default(true);
            $table->dateTime('reserved_until')->nullable();

            $table->index(['stock_status', 'is_visible']);
        });

        DB::table('random_category_accounts')->where('status', 'sold')->update(['stock_status' => 'sold']);
        DB::table('random_category_accounts')->where('status', 'available')->update(['stock_status' => 'available']);
    }

    public function down(): void
    {
        Schema::table('random_category_accounts', function (Blueprint $table) {
            $table->dropIndex(['stock_status', 'is_visible']);
            $table->dropColumn(['stock_status', 'is_visible', 'reserved_until']);
        });
    }
};
