<?php

namespace Database\Seeders;

use App\Models\GameCategory;
use App\Models\GameGroup;
use App\Models\RandomCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

/**
 * Idempotent: updateOrCreate theo slug. Chạy lại nhiều lần không tạo bản ghi trùng.
 * Chỉ tạo/cập nhật danh mục + ảnh từ bộ banner đã chuẩn hoá; KHÔNG tạo tài khoản game mẫu.
 *
 * Chạy: php artisan db:seed --class=CatalogImageSeeder
 */
class CatalogImageSeeder extends Seeder
{
    private const IMG = '/storage/categories/catalog';

    public function run(): void
    {
        $now = now();

        // ---- Game groups (theo nền tảng / game) ----
        $groups = [
            ['name' => 'Liên Quân Mobile',  'slug' => 'lien-quan-mobile',  'thumbnail' => self::IMG . '/lien-quan-200k.png',   'order' => 1, 'active' => true],
            ['name' => 'Blox Fruits',        'slug' => 'blox-fruits',        'thumbnail' => self::IMG . '/blox-fruits.png',       'order' => 2, 'active' => true],
            ['name' => 'Genshin Impact',     'slug' => 'genshin-impact',     'thumbnail' => self::IMG . '/genshin-impact.png',    'order' => 3, 'active' => true],
            ['name' => 'FC Mobile',          'slug' => 'fc-mobile',          'thumbnail' => self::IMG . '/fc-mobile.png',         'order' => 4, 'active' => true],
            ['name' => 'Arknights: Endfield','slug' => 'arknights-endfield', 'thumbnail' => self::IMG . '/arknights-endfield.png','order' => 5, 'active' => true],
            ['name' => 'Valorant',           'slug' => 'valorant',           'thumbnail' => self::IMG . '/valorant.png',          'order' => 6, 'active' => true],
            ['name' => 'FC Online',          'slug' => 'fc-online',          'thumbnail' => self::IMG . '/fc-online.png',         'order' => 7, 'active' => true],
        ];
        foreach ($groups as $g) {
            GameGroup::updateOrCreate(
                ['slug' => $g['slug']],
                $g + ['created_at' => $now, 'updated_at' => $now]
            );
        }
        $gid = fn (string $slug) => optional(GameGroup::where('slug', $slug)->first())->id;

        // ---- Danh mục nick chọn lọc (game_categories) — giá hiển thị là MIN giá acc available ----
        $gameCategories = [
            [
                'name' => 'Acc FC Mobile (EA Sports FC)',
                'slug' => 'acc-fc-mobile',
                'platform' => 'FC Mobile',
                'thumbnail' => self::IMG . '/fc-mobile.png',
                'description' => 'Tài khoản FC Mobile đa dạng OVR và cầu thủ, bảo hành theo quy định cửa hàng.',
                'game_group' => 'fc-mobile',
            ],
            [
                'name' => 'Acc Blox Fruits',
                'slug' => 'acc-blox-fruits',
                'platform' => 'Roblox',
                'thumbnail' => self::IMG . '/blox-fruits.png',
                'description' => 'Tài khoản Blox Fruits nhiều trái ác quỷ, cấp độ và vật phẩm giá trị.',
                'game_group' => 'blox-fruits',
            ],
            [
                'name' => 'Acc Blox Fruits Premium',
                'slug' => 'acc-blox-fruits-premium',
                'platform' => 'Roblox',
                'thumbnail' => self::IMG . '/blox-fruits-dragon.png',
                'description' => 'Dòng Blox Fruits cao cấp: trái hiếm, race V4 và chỉ số mạnh.',
                'game_group' => 'blox-fruits',
            ],
            [
                'name' => 'Acc Genshin Impact AR Cao',
                'slug' => 'acc-genshin-impact-ar-cao',
                'platform' => 'Genshin Impact',
                'thumbnail' => self::IMG . '/genshin-impact.png',
                'description' => 'Acc Genshin Impact AR cao, nhiều nhân vật 5 sao và vũ khí giới hạn.',
                'game_group' => 'genshin-impact',
            ],
            [
                'name' => 'Acc Genshin Impact Reroll / Starter',
                'slug' => 'acc-genshin-impact-reroll',
                'platform' => 'Genshin Impact',
                'thumbnail' => self::IMG . '/genshin-impact-2.png',
                'description' => 'Acc Genshin reroll sở hữu nhân vật/ vũ khí mong muốn, sẵn sàng cày từ đầu.',
                'game_group' => 'genshin-impact',
            ],
            [
                'name' => 'Acc Genshin Impact Full 5★',
                'slug' => 'acc-genshin-impact-full-5sao',
                'platform' => 'Genshin Impact',
                'thumbnail' => self::IMG . '/genshin-impact-3.png',
                'description' => 'Tuyển acc Genshin đội hình mạnh, nhiều limited 5 sao và tối ưu build.',
                'game_group' => 'genshin-impact',
            ],
            [
                'name' => 'Acc Arknights: Endfield',
                'slug' => 'acc-arknights-endfield',
                'platform' => 'Arknights',
                'thumbnail' => self::IMG . '/arknights-endfield.png',
                'description' => 'Tài khoản Arknights: Endfield starter và mid-game, nhân vật hiếm.',
                'game_group' => 'arknights-endfield',
            ],
            [
                'name' => 'Acc Valorant',
                'slug' => 'acc-valorant',
                'platform' => 'Valorant',
                'thumbnail' => self::IMG . '/valorant.png',
                'description' => 'Acc Valorant đa dạng rank và skin súng, thông tin rõ ràng trước khi mua.',
                'game_group' => 'valorant',
            ],
            [
                'name' => 'Acc FC Online',
                'slug' => 'acc-fc-online',
                'platform' => 'FC Online',
                'thumbnail' => self::IMG . '/fc-online.png',
                'description' => 'Tài khoản FC Online (FIFA Online 4) với đội hình và giá trị cầu thủ hấp dẫn.',
                'game_group' => 'fc-online',
            ],
            [
                'name' => 'Acc Liên Quân Reg / Trắng Thông Tin',
                'slug' => 'acc-lien-quan-trang-thong-tin',
                'platform' => 'Liên Quân',
                'thumbnail' => self::IMG . '/lien-quan-trang-thong-tin.png',
                'description' => 'Acc Liên Quân trắng thông tin, an toàn để đổi thông tin và sử dụng lâu dài.',
                'game_group' => 'lien-quan-mobile',
            ],
        ];
        foreach ($gameCategories as $c) {
            $payload = [
                'name' => $c['name'],
                'platform' => $c['platform'],
                'thumbnail' => $c['thumbnail'],
                'description' => $c['description'],
                'active' => true,
                'game_group_id' => $gid($c['game_group']),
                'updated_at' => $now,
            ];
            GameCategory::updateOrCreate(['slug' => $c['slug']], $payload + ['created_at' => $now]);
        }

        // ---- Danh mục random / túi mù (random_categories) — có giá cố định theo ảnh ----
        $randomCategories = [
            [
                'name' => 'Acc Liên Quân Random 200K',
                'slug' => 'acc-lien-quan-random-200k',
                'platform' => 'Liên Quân',
                'thumbnail' => self::IMG . '/lien-quan-200k.png',
                'description' => 'Vòng quay / random acc Liên Quân trị giá ~200.000đ. Tỷ lệ tướng/skin theo mô tả khi mở bán.',
                'game_group' => 'lien-quan-mobile',
                'price' => 200000,
            ],
            [
                'name' => 'Acc Liên Quân Random 100K',
                'slug' => 'acc-lien-quan-random-100k',
                'platform' => 'Liên Quân',
                'thumbnail' => self::IMG . '/lien-quan-100k.png',
                'description' => 'Random acc Liên Quân trị giá ~100.000đ. Giao acc tự động sau thanh toán.',
                'game_group' => 'lien-quan-mobile',
                'price' => 100000,
            ],
            [
                'name' => 'Acc Liên Quân Random 50K',
                'slug' => 'acc-lien-quan-random-50k',
                'platform' => 'Liên Quân',
                'thumbnail' => self::IMG . '/lien-quan-50k.png',
                'description' => 'Random acc Liên Quân trị giá ~50.000đ. Phù hợp thử vận may chi phí thấp.',
                'game_group' => 'lien-quan-mobile',
                'price' => 50000,
            ],
            [
                'name' => 'Acc Liên Quân Random 20K',
                'slug' => 'acc-lien-quan-random-20k',
                'platform' => 'Liên Quân',
                'thumbnail' => self::IMG . '/lien-quan-20k.png',
                'description' => 'Random acc Liên Quân trị giá ~20.000đ.',
                'game_group' => 'lien-quan-mobile',
                'price' => 20000,
            ],
            [
                'name' => 'Acc Liên Quân Random 2K Thử Vận May',
                'slug' => 'acc-lien-quan-random-2k',
                'platform' => 'Liên Quân',
                'thumbnail' => self::IMG . '/lien-quan-2k.png',
                'description' => 'Gói thử vận may 2.000đ — acc Liên Quân ngẫu nhiên, vui là chính.',
                'game_group' => 'lien-quan-mobile',
                'price' => 2000,
            ],
            [
                'name' => 'Túi Mù Liên Quân 99K',
                'slug' => 'tui-mu-lien-quan-99k',
                'platform' => 'Liên Quân',
                'thumbnail' => self::IMG . '/lien-quan-99k-tui-mu.png',
                'description' => 'Túi mù Liên Quân 99.000đ — cơ hội nhận acc/skin giá trị cao.',
                'game_group' => 'lien-quan-mobile',
                'price' => 99000,
            ],
        ];
        foreach ($randomCategories as $c) {
            $payload = [
                'name' => $c['name'],
                'platform' => $c['platform'],
                'thumbnail' => $c['thumbnail'],
                'description' => $c['description'],
                'active' => true,
                'game_group_id' => $gid($c['game_group']),
                'price' => $c['price'],
                'updated_at' => $now,
            ];
            if (Schema::hasColumn('random_categories', 'tag_image')) {
                $payload['tag_image'] = $payload['tag_image'] ?? null;
            }
            RandomCategory::updateOrCreate(['slug' => $c['slug']], $payload + ['created_at' => $now]);
        }

        $this->command?->info('CatalogImageSeeder: đã cập nhật ' . count($groups) . ' nhóm, ' . count($gameCategories) . ' danh mục nick, ' . count($randomCategories) . ' danh mục random (idempotent).');
        $this->command?->line('Ảnh nằm tại storage/app/public/categories/catalog/. Nếu ảnh chưa hiển thị, chạy: php artisan storage:link');
    }
}
