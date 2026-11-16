# DECISIONS.md — Các quyết định kỹ thuật đã thống nhất

*Quy tắc: quyết định mới ghi thêm, không xóa quyết định cũ trừ khi user phê duyệt thay đổi.*

## D1 — Không viết lại dự án, giữ kiến trúc hiện có
Laravel 11 + Blade + cmsbvq admin template + MySQL. Không đổi framework, không đổi tên bảng/model/route đang dùng.

## D2 — Quy trình 2 giai đoạn (đã phê duyệt 13/11/2026)
Giai đoạn A: phân tích + kế hoạch (xong, docs/phase-a/). Giai đoạn B: triển khai theo phase. Reset kho tài khoản cần xác nhận riêng qua `CHAY_RESET_KHO.bat`.

## D3 — Giữ `game_groups` khi reset kho
`accounts:reset-inventory` chỉ xóa 4 bảng: game_accounts, game_categories, random_categories, random_category_accounts.

## D4 — 2FA tự viết TOTP PHP thuần
Không cài package mới (sandbox không chạy composer trên máy user). TwoFactorService chuẩn RFC 6238.

## D5 — Xóa mật khẩu admin mặc định, dùng CLI an toàn
`AdminAccountSeeder` (yukimoon/iloveher) và `UsersTableSeeder` (admin123456) là rủi ro công khai. Thay bằng `php artisan admin:create-admin`: nhập mật khẩu ẩn có confirm, enter trống → tự sinh 16 ký tự mạnh hiển thị một lần. `CHAY_TAO_ADMIN.bat` đã chuyển sang lệnh này. **Không tạo lại seeder mật khẩu cứng dưới mọi hình thức.**

## D6 — Seeder tách production / demo
`DatabaseSeeder` chỉ seed cấu trúc khung (config rỗng, danh mục, dịch vụ, vòng quay). Dữ liệu demo chuyển sang `database/seeders/local-demo/` (namespace LocalDemo) và bị chặn nếu APP_ENV=production. Local chạy: `php artisan db:seed --class="Database\\Seeders\\LocalDemo\\LocalDemoSeeder"`.

## D7 — Secret OAuth cũ coi như đã lộ
`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` (Google) và `481beed7538a8b7318c45e94401f4e3c` (Facebook) có trong ZIP gốc + ConfigSeeder. ConfigSeeder mới sẽ ghi đè trường secret khi seed (xóa giá trị cũ). **User cần tạo OAuth client mới trên Google/Facebook Console và revoke client cũ.**

## D8 — Giá dịch vụ: không hiển thị giá khi chưa cấu hình
Chỉ render gói `active=1 AND price>0`. Không có gói hợp lệ → empty state "Giá dịch vụ đang được cập nhật" + khóa form đặt hàng. JSON-LD không xuất giá giả. Cấu trúc `service_packages` + form Admin được GIỮ NGUYÊN.

## D9 — Đánh giá chỉ từ dữ liệu thật
Không có bảng review/comment trong DB → chỉ hiển thị "đã mua #id" từ PurchaseHistory, tên che 3+2 ký tự. Không bịa text đánh giá. ẩn section khi trống.

## D10 — Mã hóa mật khẩu game bằng Laravel Crypt (APP_KEY)
idempotent: nhận dạng payload đã mã hóa bằng tiền tố `eyJ` + 2 dấu chấm. Plain cũ trả nguyên (chưa chuyển), backfill bằng `accounts:encrypt-passwords`. **KHÔNG đổi APP_KEY sau khi mã hóa — sẽ mất toàn bộ mật khẩu game.**

## D11 — HTML render theo allowlist
`App\Support\HtmlSanitizer` cho nội dung Admin (notification, top_deposit_reward, terms, privacy). Nội dung khách nhập luôn `{{ }}`.

## D12 — Lỗi API/exception không trả chi tiết kỹ thuật
Log phía server, trả message thân thiện tiếng Việt. Không trả `$e->getMessage()`, curl error, raw response.

## D13 — Không lưu token của site khác
Beacon Cloudflare (token ff8b28... của shopaccgamev2.tuanori.vn), ảnh zalo/facebook từ tuanori.vn đã bị loại. Support buttons chỉ hiện khi config có giá trị.

## D14 — getMessage() chỉ chấp nhận trong ADMIN, không trong USER
Toàn bộ luồng khách hàng (mua acc, đặt dịch vụ, rút tiền, nạp thẻ, trả góp) đã sạch `getMessage()` — trả message thân thiện + Log::error.
Còn 43 chỗ ở `app/Http/Controllers/Admin/*` giữ nguyên chi tiết lỗi vì: (1) chỉ admin/dev đăng nhập mới thấy, (2) cần thiết để debug vận hành, (3) spec cấm lộ chi tiết "ra client/khách hàng". **Không sửa hàng loạt bằng regex** — một lần thử đã phá cú pháp 12 file và phải khôi phục từ backup.

## D15 — Vite là pipeline build duy nhất
`webpack.mix.js` + `laravel-mix` đã xóa khỏi package.json. `vite.config.js` trỏ `resources/css/app.css` + `resources/js/app.js` (đã tạo). Blade hiện KHÔNG dùng `@vite()` — asset phục vụ tĩnh từ `public/`. Entry tồn tại để `npm run build` không thất bại và làm chỗ mở rộng.
**Lý do:** config cũ trỏ `resources/assets/*` không tồn tại → build FAIL, chặn release.

## D16 — Không dùng CDN Ant Design
`antd.min.css` (269KB) đã loại khỏi head.blade. Kiểm tra: không view nào dùng component AntD thật (chỉ class `ant-*` tự viết trong style block). Nếu sau này cần, bật lại theo hướng dẫn trong comment tại head.blade.php.

## D17 — Thứ tự CSS cố định
`style.css` → `legacy-compat.css` → `modern-gaming.css` → **`shopgame-theme.css`** (cuối). File theme chỉ chuẩn hóa token/component, không phá layout. Theme đỏ cũ (#dc2626) trong style.css đã chuyển thành `var(--color-primary)` — 0 màu đỏ hardcode.
