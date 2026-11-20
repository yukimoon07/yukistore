<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\GameCategory;
use App\Models\GameGroup;
use App\Models\GameService;
use App\Models\RandomCategory;
use App\Models\ServicePackage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Idempotent: updateOrCreate theo slug. Chạy lại nhiều lần không tạo bản ghi trùng.
 * Tạo danh mục game, nhóm game, danh mục random và dịch vụ từ bộ ảnh trong 'shop img'.
 *
 * Chạy: php artisan db:seed --class=CatalogImageSeeder
 */
class CatalogImageSeeder extends Seeder
{
    private const IMG = '/storage/categories/catalog';

    public function run(): void
    {
        $now = now();

        // 1. ---- Game groups (Nhóm game theo nền tảng) ----
        $groups = [
            ['name' => 'Liên Quân Mobile',   'slug' => 'lien-quan-mobile',   'thumbnail' => self::IMG . '/thu-van-may-lien-quan-200k.png', 'order' => 1, 'active' => true],
            ['name' => 'FC Mobile',           'slug' => 'fc-mobile',           'thumbnail' => self::IMG . '/acc-tu-chon-fc-mobile.png',      'order' => 2, 'active' => true],
            ['name' => 'Genshin Impact',      'slug' => 'genshin-impact',      'thumbnail' => self::IMG . '/acc-genshin-impact.png',         'order' => 3, 'active' => true],
            ['name' => 'Arknights: Endfield', 'slug' => 'arknights-endfield',  'thumbnail' => self::IMG . '/acc-arknights-endfield.png',      'order' => 4, 'active' => true],
            ['name' => 'Valorant',            'slug' => 'valorant',            'thumbnail' => self::IMG . '/acc-valorant.png',               'order' => 5, 'active' => true],
            ['name' => 'FC Online',           'slug' => 'fc-online',           'thumbnail' => self::IMG . '/acc-fc-online.png',              'order' => 6, 'active' => true],
            ['name' => 'Blox Fruits',         'slug' => 'blox-fruits',         'thumbnail' => self::IMG . '/blox-fruits.png',                'order' => 7, 'active' => true],
        ];

        try {
            foreach ($groups as $g) {
                $payload = ['updated_at' => $now];
                if (Schema::hasColumn('game_groups', 'name')) $payload['name'] = $g['name'];
                if (Schema::hasColumn('game_groups', 'thumbnail')) $payload['thumbnail'] = $g['thumbnail'];
                if (Schema::hasColumn('game_groups', 'order')) $payload['order'] = $g['order'];
                if (Schema::hasColumn('game_groups', 'active')) $payload['active'] = $g['active'];

                if (Schema::hasColumn('game_groups', 'slug')) {
                    GameGroup::updateOrCreate(['slug' => $g['slug']], $payload + ['created_at' => $now]);
                } else {
                    GameGroup::create($payload + ['created_at' => $now]);
                }
            }
        } catch (\Throwable $e) {
            Log::error('CatalogImageSeeder - GameGroup Error: ' . $e->getMessage());
            $this->command?->error('GameGroup seed error: ' . $e->getMessage());
        }

        $gid = function (string $slug) {
            if (!Schema::hasColumn('game_groups', 'slug')) return null;
            return optional(GameGroup::where('slug', $slug)->first())->id;
        };

        // 2. ---- Danh mục nick chọn lọc (game_categories) từ ảnh shop img ----
        $gameCategories = [
            [
                'name' => 'Acc Tự Chọn FC Mobile',
                'slug' => 'acc-tu-chon-fc-mobile',
                'platform' => 'FC Mobile',
                'thumbnail' => self::IMG . '/acc-tu-chon-fc-mobile.png',
                'description' => 'Tài khoản FC Mobile tự chọn đa dạng OVR và đội hình khủng, bảo hành theo quy định cửa hàng.',
                'game_group' => 'fc-mobile',
            ],
            [
                'name' => 'Acc Genshin Impact',
                'slug' => 'acc-genshin-impact',
                'platform' => 'Genshin Impact',
                'thumbnail' => self::IMG . '/acc-genshin-impact.png',
                'description' => 'Tài khoản Genshin Impact nhiều nhân vật 5 sao và vũ khí giới hạn.',
                'game_group' => 'genshin-impact',
            ],
            [
                'name' => 'Acc Reroll Genshin Impact',
                'slug' => 'acc-reroll-genshin-impact',
                'platform' => 'Genshin Impact',
                'thumbnail' => self::IMG . '/acc-reroll-genshin-impact.png',
                'description' => 'Acc Genshin Impact reroll starter sở hữu tài nguyên dồi dào, sẵn sàng cày từ đầu.',
                'game_group' => 'genshin-impact',
            ],
            [
                'name' => 'Acc Arknights: Endfield',
                'slug' => 'acc-arknights-endfield',
                'platform' => 'Arknights',
                'thumbnail' => self::IMG . '/acc-arknights-endfield.png',
                'description' => 'Tài khoản Arknights: Endfield starter và mid-game, nhân vật hiếm.',
                'game_group' => 'arknights-endfield',
            ],
            [
                'name' => 'Acc Reroll Arknights: Endfield',
                'slug' => 'acc-reroll-ark-endfield',
                'platform' => 'Arknights',
                'thumbnail' => self::IMG . '/acc-reroll-ark-endfield.png',
                'description' => 'Acc reroll Arknights: Endfield khởi đầu chuẩn chỉ với nhiều lượt quay.',
                'game_group' => 'arknights-endfield',
            ],
            [
                'name' => 'Acc Valorant',
                'slug' => 'acc-valorant',
                'platform' => 'Valorant',
                'thumbnail' => self::IMG . '/acc-valorant.png',
                'description' => 'Acc Valorant đa dạng rank và skin súng, thông tin rõ ràng trước khi mua.',
                'game_group' => 'valorant',
            ],
            [
                'name' => 'Acc FC Online',
                'slug' => 'acc-fc-online',
                'platform' => 'FC Online',
                'thumbnail' => self::IMG . '/acc-fc-online.png',
                'description' => 'Tài khoản FC Online (FIFA Online 4) với đội hình và giá trị cầu thủ hấp dẫn.',
                'game_group' => 'fc-online',
            ],
            [
                'name' => 'Acc Liên Quân Trắng Thông Tin',
                'slug' => 'acc-lien-quan-trang-thong-tin',
                'platform' => 'Liên Quân',
                'thumbnail' => self::IMG . '/acc-lien-quan-trang-thong-tin.png',
                'description' => 'Acc Liên Quân trắng thông tin, an toàn để đổi thông tin và sử dụng lâu dài.',
                'game_group' => 'lien-quan-mobile',
            ],
            [
                'name' => 'Acc Reg Liên Quân',
                'slug' => 'acc-reg-lien-quan',
                'platform' => 'Liên Quân',
                'thumbnail' => self::IMG . '/acc-reg-lien-quan.png',
                'description' => 'Acc Reg Liên Quân tạo sẵn, cấp độ và tướng cơ bản, sẵn sàng leo rank.',
                'game_group' => 'lien-quan-mobile',
            ],
            [
                'name' => 'Acc Blox Fruits',
                'slug' => 'acc-blox-fruits',
                'platform' => 'Roblox',
                'thumbnail' => self::IMG . '/blox-fruits.png',
                'description' => 'Tài khoản Blox Fruits nhiều trái ác quỷ, cấp độ và vật phẩm giá trị.',
                'game_group' => 'blox-fruits',
            ],
        ];

        $hasGameGroupColGameCat = Schema::hasColumn('game_categories', 'game_group_id');
        $hasPlatformColGameCat = Schema::hasColumn('game_categories', 'platform');

        try {
            foreach ($gameCategories as $c) {
                $payload = [
                    'name' => $c['name'],
                    'thumbnail' => $c['thumbnail'],
                    'description' => $c['description'],
                    'active' => true,
                    'updated_at' => $now,
                ];
                if ($hasPlatformColGameCat) {
                    $payload['platform'] = $c['platform'];
                }
                if ($hasGameGroupColGameCat) {
                    $payload['game_group_id'] = $gid($c['game_group']);
                }
                GameCategory::updateOrCreate(['slug' => $c['slug']], $payload + ['created_at' => $now]);
            }
        } catch (\Throwable $e) {
            Log::error('CatalogImageSeeder - GameCategory Error: ' . $e->getMessage());
            $this->command?->error('GameCategory seed error: ' . $e->getMessage());
        }

        // 3. ---- Danh mục random / túi mù (random_categories) từ ảnh shop img ----
        $randomCategories = [
            [
                'name' => 'Thử Vận May Liên Quân 200K',
                'slug' => 'thu-van-may-lien-quan-200k',
                'platform' => 'Liên Quân',
                'thumbnail' => self::IMG . '/thu-van-may-lien-quan-200k.png',
                'description' => 'Thử vận may acc Liên Quân 200.000đ tỷ lệ trúng nick VIP cực cao, full tướng skin.',
                'game_group' => 'lien-quan-mobile',
                'price' => 200000,
            ],
            [
                'name' => 'Thử Vận May Liên Quân 100K',
                'slug' => 'thu-van-may-lien-quan-100k',
                'platform' => 'Liên Quân',
                'thumbnail' => self::IMG . '/thu-van-may-lien-quan-100k.png',
                'description' => 'Thử vận may acc Liên Quân 100.000đ. Giao acc tự động ngay sau thanh toán.',
                'game_group' => 'lien-quan-mobile',
                'price' => 100000,
            ],
            [
                'name' => 'Thử Vận May Liên Quân 50K',
                'slug' => 'thu-van-may-lien-quan-50k',
                'platform' => 'Liên Quân',
                'thumbnail' => self::IMG . '/thu-van-may-lien-quan-50k.png',
                'description' => 'Thử vận may acc Liên Quân 50.000đ. Phù hợp thử vận may chi phí thấp.',
                'game_group' => 'lien-quan-mobile',
                'price' => 50000,
            ],
            [
                'name' => 'Thử Vận May Liên Quân 20K',
                'slug' => 'thu-van-may-lien-quan-20k',
                'platform' => 'Liên Quân',
                'thumbnail' => self::IMG . '/thu-van-may-lien-quan-20k.png',
                'description' => 'Thử vận may acc Liên Quân 20.000đ.',
                'game_group' => 'lien-quan-mobile',
                'price' => 20000,
            ],
            [
                'name' => 'Thử Vận May Liên Quân 2K',
                'slug' => 'thu-van-may-lien-quan-2k',
                'platform' => 'Liên Quân',
                'thumbnail' => self::IMG . '/thu-van-may-lien-quan-2k.png',
                'description' => 'Gói thử vận may 2.000đ — acc Liên Quân ngẫu nhiên, cơ hội nhận acc ngon giá rẻ.',
                'game_group' => 'lien-quan-mobile',
                'price' => 2000,
            ],
            [
                'name' => 'Túi Mù Liên Quân 99K',
                'slug' => 'tui-mu-99k',
                'platform' => 'Liên Quân',
                'thumbnail' => self::IMG . '/tui-mu-99k.png',
                'description' => 'Túi mù may mắn Liên Quân 99.000đ — cơ hội nhận acc/skin giá trị cao.',
                'game_group' => 'lien-quan-mobile',
                'price' => 99000,
            ],
            [
                'name' => 'Thử Vận May Blox Fruits',
                'slug' => 'thu-van-may-blox-fruits',
                'platform' => 'Roblox',
                'thumbnail' => self::IMG . '/blox-fruits-dragon.png',
                'description' => 'Thử vận may Blox Fruits ngẫu nhiên, cơ hội nhận tài khoản VIP có Melee V2, Trái Ác Quỷ vĩnh viễn.',
                'game_group' => 'blox-fruits',
                'price' => 50000,
            ],
        ];

        $hasGameGroupColRandCat = Schema::hasColumn('random_categories', 'game_group_id');
        $hasPlatformColRandCat = Schema::hasColumn('random_categories', 'platform');
        $hasTagImageCol = Schema::hasColumn('random_categories', 'tag_image');
        $hasPriceCol = Schema::hasColumn('random_categories', 'price');

        try {
            foreach ($randomCategories as $c) {
                $payload = [
                    'name' => $c['name'],
                    'thumbnail' => $c['thumbnail'],
                    'description' => $c['description'],
                    'active' => true,
                    'updated_at' => $now,
                ];
                if ($hasPlatformColRandCat) {
                    $payload['platform'] = $c['platform'];
                }
                if ($hasPriceCol) {
                    $payload['price'] = $c['price'];
                }
                if ($hasGameGroupColRandCat) {
                    $payload['game_group_id'] = $gid($c['game_group']);
                }
                if ($hasTagImageCol) {
                    $payload['tag_image'] = null;
                }
                RandomCategory::updateOrCreate(['slug' => $c['slug']], $payload + ['created_at' => $now]);
            }
        } catch (\Throwable $e) {
            Log::error('CatalogImageSeeder - RandomCategory Error: ' . $e->getMessage());
            $this->command?->error('RandomCategory seed error: ' . $e->getMessage());
        }

        // 4. ---- Dịch vụ cày thuê mặc định (game_services) nếu bảng đang trống ----
        try {
            if (Schema::hasTable('game_services') && GameService::count() === 0) {
                $defaultServices = [
                    [
                        'name' => 'Cày Thuê Liên Quân Mobile',
                        'slug' => 'cay-thue-lien-quan-mobile',
                        'thumbnail' => self::IMG . '/thu-van-may-lien-quan-200k.png',
                        'description' => 'Dịch vụ cày thuê rank Liên Quân Mobile uy tín, nhanh chóng, bảo mật tài khoản 100%.',
                        'type' => 'leveling',
                        'active' => true,
                        'packages' => [
                            ['name' => 'Kéo rank Kim Cương lên Tinh Anh', 'price' => 50000, 'estimated_time' => 24, 'description' => 'Kéo theo sao an toàn.'],
                            ['name' => 'Kéo rank Tinh Anh lên Cao Thủ', 'price' => 100000, 'estimated_time' => 48, 'description' => 'Kéo rank thần tốc.'],
                        ]
                    ],
                    [
                        'name' => 'Cày Thuê Blox Fruits',
                        'slug' => 'cay-thue-blox-fruits',
                        'thumbnail' => self::IMG . '/blox-fruits.png',
                        'description' => 'Dịch vụ cày cấp độ, săn Melee V2, thức tỉnh Trái Ác Quỷ uy tín giá rẻ.',
                        'type' => 'leveling',
                        'active' => true,
                        'packages' => [
                            ['name' => 'Cày Max Level (2550)', 'price' => 60000, 'estimated_time' => 24, 'description' => 'Cày từ Sea 1 đến Sea 3.'],
                            ['name' => 'Săn Godhuman / Cursed Dual Katana', 'price' => 120000, 'estimated_time' => 48, 'description' => 'Đầy đủ nguyên liệu.'],
                        ]
                    ],
                    [
                        'name' => 'Cày Thuê Genshin Impact',
                        'slug' => 'cay-thue-genshin-impact',
                        'thumbnail' => self::IMG . '/acc-genshin-impact.png',
                        'description' => 'Dịch vụ dọn map 100%, làm nhiệm vụ Ma Thần, farm thánh di vật.',
                        'type' => 'leveling',
                        'active' => true,
                        'packages' => [
                            ['name' => 'Dọn Map 100% (Mondstadt / Liyue / Inazuma)', 'price' => 70000, 'estimated_time' => 36, 'description' => 'Mở full rương và phong thần đồng.'],
                        ]
                    ],
                ];

                foreach ($defaultServices as $ds) {
                    $svc = GameService::create([
                        'name' => $ds['name'],
                        'slug' => $ds['slug'],
                        'thumbnail' => $ds['thumbnail'],
                        'description' => $ds['description'],
                        'type' => $ds['type'],
                        'active' => $ds['active'],
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);

                    if (Schema::hasTable('service_packages')) {
                        foreach ($ds['packages'] as $pkg) {
                            ServicePackage::create([
                                'game_service_id' => $svc->id,
                                'name' => $pkg['name'],
                                'price' => $pkg['price'],
                                'estimated_time' => $pkg['estimated_time'],
                                'description' => $pkg['description'],
                                'active' => true,
                                'created_at' => $now,
                                'updated_at' => $now,
                            ]);
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::error('CatalogImageSeeder - GameService Error: ' . $e->getMessage());
        }

        $this->command?->info('CatalogImageSeeder: đã tạo/cập nhật đầy đủ nhóm game, danh mục nick, danh mục random và dịch vụ!');
    }
}
