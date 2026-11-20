<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * Bổ sung tất cả các cột còn thiếu trong schema SQLite / MySQL
     * để đảm bảo CatalogImageSeeder, Admin CRUD và Frontend hoạt động trơn tru 100%.
     */
    public function up(): void
    {
        // 1. Bổ sung các cột cho game_groups
        if (Schema::hasTable('game_groups')) {
            $groupCols = [
                'name' => fn(Blueprint $table) => $table->string('name')->default(''),
                'slug' => fn(Blueprint $table) => $table->string('slug')->default(''),
                'thumbnail' => fn(Blueprint $table) => $table->string('thumbnail')->nullable(),
                'order' => fn(Blueprint $table) => $table->integer('order')->default(0),
                'active' => fn(Blueprint $table) => $table->boolean('active')->default(true),
                'link' => fn(Blueprint $table) => $table->string('link')->nullable(),
            ];

            foreach ($groupCols as $col => $definition) {
                if (!Schema::hasColumn('game_groups', $col)) {
                    Schema::table('game_groups', function (Blueprint $table) use ($definition) {
                        $definition($table);
                    });
                }
            }
        }

        // 2. Bổ sung các cột cho random_categories
        if (Schema::hasTable('random_categories')) {
            $randCols = [
                'price' => fn(Blueprint $table) => $table->decimal('price', 15, 2)->default(0),
                'game_group_id' => fn(Blueprint $table) => $table->unsignedBigInteger('game_group_id')->nullable(),
                'platform' => fn(Blueprint $table) => $table->string('platform')->nullable(),
                'tag_image' => fn(Blueprint $table) => $table->string('tag_image')->nullable(),
                'is_flash_sale' => fn(Blueprint $table) => $table->boolean('is_flash_sale')->default(false),
                'flash_sale_old_price' => fn(Blueprint $table) => $table->integer('flash_sale_old_price')->nullable(),
                'flash_sale_new_price' => fn(Blueprint $table) => $table->integer('flash_sale_new_price')->nullable(),
                'flash_sale_end_time' => fn(Blueprint $table) => $table->dateTime('flash_sale_end_time')->nullable(),
            ];

            foreach ($randCols as $col => $definition) {
                if (!Schema::hasColumn('random_categories', $col)) {
                    Schema::table('random_categories', function (Blueprint $table) use ($definition) {
                        $definition($table);
                    });
                }
            }
        }

        // 3. Bổ sung các cột cho game_categories
        if (Schema::hasTable('game_categories')) {
            $catCols = [
                'game_group_id' => fn(Blueprint $table) => $table->unsignedBigInteger('game_group_id')->nullable(),
                'platform' => fn(Blueprint $table) => $table->string('platform')->nullable(),
                'tag_image' => fn(Blueprint $table) => $table->string('tag_image')->nullable(),
                'is_flash_sale' => fn(Blueprint $table) => $table->boolean('is_flash_sale')->default(false),
                'flash_sale_old_price' => fn(Blueprint $table) => $table->integer('flash_sale_old_price')->nullable(),
                'flash_sale_new_price' => fn(Blueprint $table) => $table->integer('flash_sale_new_price')->nullable(),
                'flash_sale_end_time' => fn(Blueprint $table) => $table->dateTime('flash_sale_end_time')->nullable(),
            ];

            foreach ($catCols as $col => $definition) {
                if (!Schema::hasColumn('game_categories', $col)) {
                    Schema::table('game_categories', function (Blueprint $table) use ($definition) {
                        $definition($table);
                    });
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Safe no-op on production SQLite
    }
};
