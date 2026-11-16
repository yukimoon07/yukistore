<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('missions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('image')->nullable();
            $table->string('service_type')->default('link1s'); // link1s, link4m, link123, dilink, web1s, review_map, custom
            $table->text('target_url')->nullable();
            $table->string('api_token')->nullable();
            $table->decimal('reward_min', 15, 2)->default(100);
            $table->decimal('reward_max', 15, 2)->default(300);
            $table->integer('cooldown_seconds')->default(120);
            $table->text('description')->nullable();
            $table->longText('guide_steps')->nullable();
            $table->boolean('status')->default(true);
            $table->integer('total_completed')->default(0);
            $table->timestamps();
        });

        Schema::create('mission_giftcodes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mission_id')->nullable()->constrained('missions')->onDelete('set null');
            $table->string('code')->unique();
            $table->decimal('reward_amount', 15, 2);
            $table->integer('usages_left')->default(1);
            $table->string('status')->default('active'); // active, used, expired
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        Schema::create('mission_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('mission_id')->nullable()->constrained('missions')->onDelete('set null');
            $table->string('code');
            $table->decimal('reward_amount', 15, 2);
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mission_histories');
        Schema::dropIfExists('mission_giftcodes');
        Schema::dropIfExists('missions');
    }
};
