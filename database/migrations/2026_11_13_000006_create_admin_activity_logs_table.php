<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Sprint 1 — Admin Activity Log: ghi nhận mọi hành động quản trị quan trọng.
 * old_values/new_values phải được mask dữ liệu nhạy cảm trước khi ghi (xem SecuritySanitizer).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action');                     // login / logout / login_failed / product_created / ...
            $table->string('target_type')->nullable();    // Model class hoặc tên bảng
            $table->unsignedBigInteger('target_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->timestamps();

            $table->index(['admin_id', 'created_at']);
            $table->index(['action', 'created_at']);
            $table->index(['target_type', 'target_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_activity_logs');
    }
};
