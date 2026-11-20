<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('game_categories') && !Schema::hasColumn('game_categories', 'game_group_id')) {
            Schema::table('game_categories', function (Blueprint $table) {
                $table->unsignedBigInteger('game_group_id')->nullable();
            });
        }

        if (Schema::hasTable('random_categories') && !Schema::hasColumn('random_categories', 'game_group_id')) {
            Schema::table('random_categories', function (Blueprint $table) {
                $table->unsignedBigInteger('game_group_id')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('game_categories') && Schema::hasColumn('game_categories', 'game_group_id')) {
            Schema::table('game_categories', function (Blueprint $table) {
                $table->dropColumn('game_group_id');
            });
        }

        if (Schema::hasTable('random_categories') && Schema::hasColumn('random_categories', 'game_group_id')) {
            Schema::table('random_categories', function (Blueprint $table) {
                $table->dropColumn('game_group_id');
            });
        }
    }
};
