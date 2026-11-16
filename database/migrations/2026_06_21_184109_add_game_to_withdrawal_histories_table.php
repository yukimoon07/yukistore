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
        if (!Schema::hasColumn('withdrawal_histories', 'game')) {
            Schema::table('withdrawal_histories', function (Blueprint $table) {
                $table->string('game')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('withdrawal_histories', 'game')) {
            Schema::table('withdrawal_histories', function (Blueprint $table) {
                $table->dropColumn('game');
            });
        }
    }
};
