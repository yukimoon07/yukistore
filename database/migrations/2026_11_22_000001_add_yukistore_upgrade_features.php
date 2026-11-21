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
        // 1. Cập nhật bảng users (Profile customization)
        if (Schema::hasTable('users')) {
            $userCols = [
                'name' => fn(Blueprint $table) => $table->string('name')->nullable(),
                'phone' => fn(Blueprint $table) => $table->string('phone')->nullable(),
                'avatar' => fn(Blueprint $table) => $table->string('avatar')->nullable(),
            ];
            foreach ($userCols as $col => $def) {
                if (!Schema::hasColumn('users', $col)) {
                    Schema::table('users', function (Blueprint $table) use ($def) {
                        $def($table);
                    });
                }
            }
        }

        // 2. Cập nhật bảng game_services (Dịch vụ nổi bật)
        if (Schema::hasTable('game_services')) {
            if (!Schema::hasColumn('game_services', 'is_featured')) {
                Schema::table('game_services', function (Blueprint $table) {
                    $table->boolean('is_featured')->default(false)->after('active');
                });
            }
        }

        // 3. Cập nhật bảng service_histories (Gói dịch vụ đã chọn & ghi chú)
        if (Schema::hasTable('service_histories')) {
            $shCols = [
                'package_names' => fn(Blueprint $table) => $table->text('package_names')->nullable(),
                'user_note' => fn(Blueprint $table) => $table->text('user_note')->nullable(),
            ];
            foreach ($shCols as $col => $def) {
                if (!Schema::hasColumn('service_histories', $col)) {
                    Schema::table('service_histories', function (Blueprint $table) use ($def) {
                        $def($table);
                    });
                }
            }
        }

        // 4. Cập nhật bảng random_categories (Tùy chỉnh mô tả, badge, 3 cam kết)
        if (Schema::hasTable('random_categories')) {
            $rcCols = [
                'custom_lead' => fn(Blueprint $table) => $table->text('custom_lead')->nullable(),
                'custom_badge' => fn(Blueprint $table) => $table->string('custom_badge')->nullable(),
                'guarantee_1' => fn(Blueprint $table) => $table->string('guarantee_1')->nullable(),
                'guarantee_2' => fn(Blueprint $table) => $table->string('guarantee_2')->nullable(),
                'guarantee_3' => fn(Blueprint $table) => $table->string('guarantee_3')->nullable(),
            ];
            foreach ($rcCols as $col => $def) {
                if (!Schema::hasColumn('random_categories', $col)) {
                    Schema::table('random_categories', function (Blueprint $table) use ($def) {
                        $def($table);
                    });
                }
            }
        }

        // 5. Cập nhật bảng game_categories (Tùy chỉnh mô tả, badge, 3 cam kết)
        if (Schema::hasTable('game_categories')) {
            $gcCols = [
                'custom_lead' => fn(Blueprint $table) => $table->text('custom_lead')->nullable(),
                'custom_badge' => fn(Blueprint $table) => $table->string('custom_badge')->nullable(),
                'guarantee_1' => fn(Blueprint $table) => $table->string('guarantee_1')->nullable(),
                'guarantee_2' => fn(Blueprint $table) => $table->string('guarantee_2')->nullable(),
                'guarantee_3' => fn(Blueprint $table) => $table->string('guarantee_3')->nullable(),
            ];
            foreach ($gcCols as $col => $def) {
                if (!Schema::hasColumn('game_categories', $col)) {
                    Schema::table('game_categories', function (Blueprint $table) use ($def) {
                        $def($table);
                    });
                }
            }
        }

        // 6. Tạo bảng order_reviews (Đánh giá sau khi hoàn tất đơn hàng)
        if (!Schema::hasTable('order_reviews')) {
            Schema::create('order_reviews', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('service_order_id')->nullable();
                $table->string('order_type')->default('service');
                $table->tinyInteger('rating')->default(5);
                $table->text('comment');
                $table->string('status')->default('approved'); // approved, pending, hidden
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_reviews');

        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn(['name', 'phone', 'avatar']);
            });
        }

        if (Schema::hasTable('game_services')) {
            Schema::table('game_services', function (Blueprint $table) {
                $table->dropColumn('is_featured');
            });
        }

        if (Schema::hasTable('service_histories')) {
            Schema::table('service_histories', function (Blueprint $table) {
                $table->dropColumn(['package_names', 'user_note']);
            });
        }

        if (Schema::hasTable('random_categories')) {
            Schema::table('random_categories', function (Blueprint $table) {
                $table->dropColumn(['custom_lead', 'custom_badge', 'guarantee_1', 'guarantee_2', 'guarantee_3']);
            });
        }

        if (Schema::hasTable('game_categories')) {
            Schema::table('game_categories', function (Blueprint $table) {
                $table->dropColumn(['custom_lead', 'custom_badge', 'guarantee_1', 'guarantee_2', 'guarantee_3']);
            });
        }
    }
};
