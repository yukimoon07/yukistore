<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('bank_deposits')) {
            return;
        }

        Schema::table('bank_deposits', function (Blueprint $table) {
            if (!Schema::hasColumn('bank_deposits', 'bank_account_id')) {
                $table->unsignedBigInteger('bank_account_id')->nullable()->index();
            }
            if (!Schema::hasColumn('bank_deposits', 'status')) {
                $table->string('status')->default('completed')->index();
            }
            if (!Schema::hasColumn('bank_deposits', 'gateway')) {
                $table->string('gateway')->nullable()->index();
            }
            if (!Schema::hasColumn('bank_deposits', 'gateway_transaction_id')) {
                $table->string('gateway_transaction_id')->nullable();
            }
            if (!Schema::hasColumn('bank_deposits', 'raw_payload')) {
                $table->json('raw_payload')->nullable();
            }
        });

        Schema::table('bank_deposits', function (Blueprint $table) {
            $table->unique('gateway_transaction_id');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('bank_deposits')) {
            return;
        }

        Schema::table('bank_deposits', function (Blueprint $table) {
            $table->dropUnique(['gateway_transaction_id']);
            $table->dropColumn([
                'bank_account_id',
                'status',
                'gateway',
                'gateway_transaction_id',
                'raw_payload',
            ]);
        });
    }
};
