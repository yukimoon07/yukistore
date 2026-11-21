<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('discount_codes')) {
            $exists = DB::table('discount_codes')->where('code', 'WELCOME10')->exists();
            if (!$exists) {
                DB::table('discount_codes')->insert([
                    'code' => 'WELCOME10',
                    'discount_type' => 'percentage',
                    'discount_value' => 10,
                    'max_discount_value' => null,
                    'min_purchase_amount' => 0,
                    'is_active' => '1',
                    'usage_limit' => null,
                    'usage_count' => 0,
                    'per_user_limit' => null,
                    'applicable_to' => null,
                    'description' => 'Mã giảm giá chào mừng 10% (Welcome 10% Off)',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('discount_codes')) {
            DB::table('discount_codes')->where('code', 'WELCOME10')->delete();
        }
    }
};
