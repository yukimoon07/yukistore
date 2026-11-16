<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Sprint 1 — Payment Methods: hệ phương thức thanh toán quản lý được từ Admin Panel.
 * config lưu JSON ĐÃ MÃ HÓA (Crypt::encryptString) — chỉ server giải mã khi cần.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();         // card / bank / usdt / momo / ...
            $table->string('name');                       // Tên hiển thị
            $table->string('logo')->nullable();           // Ảnh do admin upload
            $table->longText('instructions')->nullable(); // Hướng dẫn thanh toán (safe_html)
            $table->longText('config')->nullable();       // JSON mã hóa — secret
            $table->integer('sort_order')->default(0);
            $table->boolean('is_enabled')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
