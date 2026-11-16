<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Sprint 1 — Nhật ký thay đổi tài khoản (audit trail cho Account Inventory).
 * KHÔNG lưu mật khẩu dạng plain trong log — chỉ ghi chú "đã thay đổi".
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('game_account_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_account_id')->constrained('game_accounts')->cascadeOnDelete();
            $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action');                     // created / updated / deleted / status_changed / viewed_password / imported
            $table->json('old_values')->nullable();       // Các trường thay đổi — giá trị cũ (đã mask)
            $table->json('new_values')->nullable();       // Các trường thay đổi — giá trị mới (đã mask)
            $table->timestamps();

            $table->index(['game_account_id', 'created_at']);
        });

        Schema::create('random_account_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('random_account_id')
                ->constrained('random_category_accounts')->cascadeOnDelete();
            $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action');
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->timestamps();

            $table->index(['random_account_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('random_account_logs');
        Schema::dropIfExists('game_account_logs');
    }
};
