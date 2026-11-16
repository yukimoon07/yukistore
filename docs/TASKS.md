# TASKS.md — Tiến độ triển khai

Ký hiệu: ✅ xong + đã kiểm tra | 🟡 code xong, chờ user chạy trên máy | ⬜ chưa làm | ⛔ blocked

## PHASE 0 — Backup & Audit ✅
- ✅ Backup toàn project (trừ vendor, __MACOSX) → `storage/backups/shopgame_before_phase1_20261113_1622.tar.gz` (47MB)
- ✅ Audit codebase: cấu trúc, routes, seeders, services, views, config
- ✅ Phát hiện: terminal.php, 2 seeder admin mật khẩu cứng, SSL verify tắt, secret OAuth hardcode, review giả, toast mua giả, giá demo, N+1 ở HomeController

## PHASE 1 — Production Cleanup & Security ✅
- ✅ Xóa `public/terminal.php` (chạy artisan qua HTTP công khai — RCE risk)
- ✅ Xóa `check_wheel_hist.php` (debug script ở root)
- ✅ Xóa `__MACOSX/` (1066 file rác) + toàn bộ `.DS_Store`
- ✅ Xóa 26 file CSS trùng có `@time=` trong `public/cmsbvq/template/frontend/css/`
- ✅ `.env.example` mới: production-ready, không secret, không localhost, không thông tin bên thứ ba
- ✅ Bật `CURLOPT_SSL_VERIFYPEER=true` / `VERIFYHOST=2` ở TheGioiDevService + DichVuDarkService
- ✅ Bỏ trả `$curlErr`, `raw` response, HTTP body nội bộ ra frontend (log phía server)
- ✅ Xóa `database/seeders/UsersTableSeeder.php` (admin/moderator/staff mật khẩu `admin123456`)
- ✅ Tạo `app/Console/Commands/CreateAdminCommand.php` — `admin:create-admin` (nhập ẩn, tự sinh mật khẩu mạnh, không hardcode)
- ✅ Viết lại `CHAY_TAO_ADMIN.bat` (không còn in mật khẩu công khai)
- ✅ Chỉnh `routes/api.php` — `/api/auto-bank-deposit` đã yêu cầu auth+admin+throttle (kiểm tra lại, đã đúng từ sprint trước)

## PHASE 2 — Remove Demo Data & Prices ✅ (code) / 🟡 (chờ chạy migrate)
- ✅ Xóa 8 seeder demo khỏi `database/seeders/`: GameAccount, RandomCategoryAccounts, ServicePackages, LuckyWheelHistories, MoneyWithdrawalHistories, WithdrawalHistories, Notifications, BankAccount
- ✅ Di chuyển nguyên bản các seeder demo sang `database/seeders/local-demo/` (namespace `Database\Seeders\LocalDemo`), chặn chạy nếu APP_ENV=production
- ✅ `DatabaseSeeder.php` chỉ seed dữ liệu khung: ConfigSeeder, GameCategories, RandomCategories, GameServices, LuckyWheels
- ✅ `ConfigSeeder.php` viết lại: không secret, không thương hiệu/logo/banner/domain bên thứ ba, trường nhận diện để trống, payment + social tắt mặc định
- ✅ Xóa toàn bộ review giả ở `resources/views/user/home.blade.php` (8 review static + text giả ngẫu nhiên) — ẩn section khi không có dữ liệu thật
- ✅ Viết lại `resources/views/user/reviews.blade.php` — chỉ hiển thị PurchaseHistory thật, xóa 24 tên giả + pagination giả
- ✅ `HomeController::getRealReviewEntries()` + `maskName()` — nguồn dữ liệu thật
- ✅ Xóa toast "vừa có người mua" giả (markup + CSS + JS `showFakePurchase`) khỏi `layouts/user/app.blade.php`
- ✅ Bỏ số điện thoại/social giả fallback (`0123456789`, `https://facebook.com`) ở floating support
- ✅ Xóa credit "CMSBVQ.COM" khỏi footer, bỏ beacon Cloudflare của site khác
- ✅ Footer: chỉ hiện Email/Hotline/Địa chỉ/Giờ làm việc khi Admin đã cấu hình
- ✅ `GameService` thêm `activePackages()`, `min_price`, `is_orderable`
- ✅ Trang service/show: chỉ render gói `active=true AND price>0`; empty state "Giá dịch vụ đang được cập nhật"; khóa form đặt hàng; BỎ JSON-LD giá giả (5000/500000) + rating giả 4.9/1280
- ⬜ **Còn:** Xử lý giá demo còn tồn trong DB local (user tự chạy hoặc xác nhận xóa)
- ⬜ **Còn:** review home.blade nếu còn sót text/giá hardcode khác

## PHASE 3 — Business Logic & DB Security 🟡
- ✅ Mã hóa mật khẩu game: accessor set/get Crypt ở `GameAccount` + `RandomCategoryAccount` (idempotent, không double-encrypt, fallback plain cũ)
- ✅ `app/Console/Commands/EncryptGamePasswordsCommand.php` — `accounts:encrypt-passwords --dry-run` backfill an toàn
- ✅ `ServiceOrderController::processOrder()` viết lại: transaction bao trọn, `lockForUpdate()` user, package phải thuộc đúng service + active + price hợp lệ, service phải active, chặn double-submit, không trả `$e->getMessage()`
- ✅ Route `POST /service/{slug}/order` thêm middleware `auth` + `not-banned`
- ✅ `app/Support/HtmlSanitizer.php` — allowlist tag/attr, chặn script/iframe/onclick/javascript:
- ✅ Áp dụng sanitizer: `home_notification`, `top_deposit_reward` (home), `terms_of_use`, `privacy_policy` (HomeController)
- ⬜ **Còn:** sanitize `$news->content`, `$mission->guide_steps`, `$content` (page)
- ⬜ **Còn:** kiểm tra luồng mua account (`GameAccountController::purchase`) — cùng pattern double-spend, cần rà
- ⬜ **Còn:** kiểm tra `RandomAccountController`, `WithdrawalController` transaction
- 🟡 Chờ user: `php artisan accounts:encrypt-passwords --dry-run` sau backup

## PHASE 3 — Business Logic & DB Security ✅
- ✅ Mã hóa mật khẩu game: accessor set/get Crypt ở `GameAccount` + `RandomCategoryAccount` (idempotent, không double-encrypt, fallback plain cũ)
- ✅ `app/Console/Commands/EncryptGamePasswordsCommand.php` — `accounts:encrypt-passwords --dry-run` backfill an toàn
- ✅ `ServiceOrderController::processOrder()` viết lại: transaction bao trọn, `lockForUpdate()` user, package phải thuộc đúng service + active + price hợp lệ, service phải active, chặn double-submit, không trả `$e->getMessage()`
- ✅ Route `POST /service/{slug}/order` thêm middleware `auth` + `not-banned`
- ✅ `app/Support/HtmlSanitizer.php` — allowlist tag/attr, chặn script/iframe/onclick/javascript:
- ✅ Áp dụng sanitizer: `home_notification`, `top_deposit_reward` (home), `terms_of_use`, `privacy_policy`, `news->content`, `mission->guide_steps`, `page.blade.php` $content
- ✅ `GameAccountController::purchase` — khóa user + log lỗi an toàn + ghi `PurchaseHistory` (nguồn dữ liệu reviews thật)
- ✅ `RandomAccountController::purchase` — khóa user + log lỗi an toàn
- ✅ `RandomCategoryController::purchase` — khóa user + log lỗi an toàn
- ✅ `WithdrawalController::store` — kiểm tra số dư trong transaction + lockForUpdate
- 🟡 Chờ user: `php artisan accounts:encrypt-passwords --dry-run` sau backup

## PHASE 4 — UI/UX Premium Dark ✅ (code)
- ✅ `public/css/shopgame-theme.css` — design tokens chính thức theo spec (#2563EB primary, #0B0F16 bg, Inter, radius/motion tokens), load cuối trong head; typography cấp bậc; buttons/forms/cards/badges/modals/toasts/scrollbar chuẩn hóa; focus-visible; prefers-reduced-motion; tabular-nums cho tiền
- ✅ `public/css/style.css` — chuyển toàn bộ theme đỏ (#dc2626/#b91c1c/#991b1b) sang tokens hệ thống mới (0 màu đỏ hardcode còn lại)
- ✅ head/header/menu-mobile — brand gradient & màu theo tokens
- ✅ Homepage spotlight: BỎ 3 thẻ hardcode (Blox Fruits 7.000đ, Genshin 3.000đ, Túi Mù 50K) → render động từ `$services` DB + minPrice từ activePackages; empty khi chưa có dữ liệu
- ✅ service/show-all: bỏ tên game hardcode khỏi title/hero; card hiện "từ X đ" từ DB hoặc "Giá đang cập nhật"; nút mờ khi chưa có giá
- ✅ `HomeController` — sửa N+1: categoryStats/randomStats/serviceOrderCounts aggregate GROUP BY, wheels dùng withCount (đây cũng là phần Phase 7)
- ⬜ **Còn:** kiểm tra trực quan 360px–1920px trên máy user (sandbox không có browser)
- Xóa `menu-mobile.blade.php`? KHÔNG — kiểm tra thêm trước khi quyết (file mồ côi nhưng spec yêu cầu không xóa vội)

## PHASE 5 — Branding & Assets 🟡
- ✅ `public/img/favicon.svg` + `public/img/logo.svg` — wordmark ShopGame tự thiết kế (S gradient xanh), không sao chép ai
- ✅ head.blade favicon fallback → favicon.svg; header logo fallback → logo.svg
- ✅ Bỏ credit CMSBVQ, ảnh tuanori.vn, beacon Cloudflare site khác
- ⬜ **Cần user:** upload logo/banner/favicon OFFICIAL qua Admin Panel (trường config rỗng sẵn sàng)

## PHASE 6 — Admin Dashboard ✅
- ✅ Rà soát DashboardController — KPI thật từ DB, phân biệt nạp/mua/rút/refund, chart 7 ngày, comparison logic, pending tasks
- ✅ `adminFilter` macro (AppServiceProvider) đã có search + date filter + pagination chuẩn cho mọi CRUD — không cần viết lại
- ✅ Dashboard notifications giới hạn 50 dòng (tránh load toàn bảng)
- ✅ Profit margin `0.4` hardcode → `config_get('estimated_profit_margin', 0.4)` (admin chỉnh được)

## PHASE 7 — Performance ✅
- ✅ HomeController N+1: categoryStats/randomStats/serviceOrderCounts aggregate GROUP BY, wheels `withCount('histories')`
- ✅ Loại `antd.min.css` (269KB CDN) — không có component AntD nào thực dùng
- ✅ Loại font Roboto khỏi Google Fonts URL (chỉ Inter)
- ✅ Loại `via.placeholder.com` (dịch vụ ngoài) ở news/random/home
- ✅ Thêm width/height + loading=lazy cho ảnh mới
- ✅ **Sửa lỗi chặn build:** `vite.config.js` trỏ `resources/assets/*` (không tồn tại) → npm run build FAIL. Đã tạo `resources/css/app.css` + `resources/js/app.js`, cập nhật vite config
- ✅ Loại `webpack.mix.js` + `laravel-mix` khỏi package.json (không blade nào dùng `mix()`)

## PHASE 8 — Testing & Release ✅
- ✅ `DEPLOYMENT.md` — 11 bước: môi trường, dependency, .env, migrate, tạo admin, quyền, storage link, cache, cron, nginx, cấu hình nội dung, backup/rollback, checklist sau deploy
- ✅ `SECURITY.md` — mô hình bảo mật, xử lý dữ liệu nhạy cảm, 11 lỗ hổng đã sửa, checklist vận hành, quy tắc phát triển
- ✅ `TAO_RELEASE.bat` — đóng gói release, tự chặn nếu lọt .env/sql/terminal.php
- ✅ **Kiểm thử:** 255 file PHP toàn project — 0 lỗi cú pháp
- ✅ Xóa `AdminAccountSeeder.php` (còn sót mật khẩu công khai)
- ✅ Sửa user-facing còn lộ `getMessage()`: CardDepositController, InstallmentController (×2)
- ⚠️ **Quyết định:** giữ `getMessage()` trong 43 chỗ **admin-only** (trang quản trị sau đăng nhập) để admin debug — xem DECISIONS D14
- ⬜ **Cần user chạy trên máy:** migrate, encrypt-passwords, tạo admin, cấu hình nội dung, kiểm thử trình duyệt

## CÒN LẠI CHO USER (không làm được trong sandbox)
1. `CHAY_MIGRATE.bat` — chạy migration (9 migration sprint trước chưa chạy)
2. `php artisan accounts:encrypt-passwords --dry-run` → xem → chạy thật
3. Quyết định reset kho: `CHAY_RESET_KHO.bat` (dry-run trước)
4. `CHAY_TAO_ADMIN.bat` → tạo admin mới, bật 2FA
5. Admin Panel → nhập logo/banner/favicon/liên hệ/OAuth mới/cổng thanh toán
6. Thêm danh mục + tài khoản game + dịch vụ + giá chính thức
7. Kiểm thử trình duyệt 360px–1920px
8. `npm install && npm run build` (kiểm tra pipeline)
9. Chạy `TAO_RELEASE.bat` để đóng gói
10. Revoke OAuth Google/Facebook client cũ (đã lộ trong ZIP)
