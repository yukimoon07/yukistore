<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Sprint 1 — Account Inventory: trạng thái kho 4 mức cho game_accounts
 * available | reserved | sold | disabled
 *
 * Map dữ liệu cũ: available→available, sold→sold, installment→reserved.
 * Cột status cũ GIỮ NGUYÊN để không phá code/luồng mua hàng hiện có.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('game_accounts', function (Blueprint $table) {
            $table->enum('stock_status', ['available', 'reserved', 'sold', 'disabled'])
                ->default('available');
            $table->boolean('is_visible')->default(true);
            $table->dateTime('reserved_until')->nullable();

            $table->index(['stock_status', 'is_visible']);
        });

        // Map dữ liệu hiện có theo giá trị status cũ
        DB::table('game_accounts')->where('status', 'sold')->update(['stock_status' => 'sold']);
        DB::table('game_accounts')->where('status', 'installment')->update(['stock_status' => 'reserved']);
        DB::table('game_accounts')->where('status', 'available')->update(['stock_status' => 'available']);
    }

    public function down(): void
    {
        Schema::table('game_accounts', function (Blueprint $table) {
            $table->dropIndex(['stock_status', 'is_visible']);
            $table->dropColumn(['stock_status', 'is_visible', 'reserved_until']);
        });
    }
};
