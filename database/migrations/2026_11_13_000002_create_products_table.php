<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Sprint 1 — Bảng sản phẩm dùng chung cho Tools / Cloud Phone / Other
 * (các nhóm kinh doanh ngoài tài khoản game và dịch vụ cày thuê).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_unit_id')->nullable()
                ->constrained('business_units')->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('image')->nullable();
            $table->text('gallery')->nullable();          // JSON mảng đường dẫn ảnh
            $table->longText('description')->nullable();
            $table->unsignedBigInteger('price')->default(0);
            $table->unsignedInteger('stock')->default(0); // Tồn kho
            $table->boolean('is_visible')->default(true); // Ẩn/hiện trên website
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['business_unit_id', 'is_visible']);
        });

        Schema::create('product_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action');                     // created / updated / deleted
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->timestamps();

            $table->index(['product_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_logs');
        Schema::dropIfExists('products');
    }
};
