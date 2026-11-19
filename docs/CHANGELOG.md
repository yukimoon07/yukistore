# CHANGELOG.md — Lịch sử thay đổi

## [1.2.0] — 19/11/2026 — Yuki Store: tích hợp danh mục ảnh + redesign "Graphite & Gold"

### Tích hợp sản phẩm & ảnh (từ 19 ảnh user cung cấp)
- **Mới**: `database/seeders/CatalogImageSeeder.php` — 7 game_groups + 10 game_categories + 6 random_categories, updateOrCreate theo slug (idempotent, chạy lại không trùng). Giá đọc trực tiếp từ chữ trên ảnh (2K/20K/50K/100K/99K/200K). KHÔNG tạo acc mẫu — tuân thủ D8.
- **Mới**: `storage/app/public/categories/catalog/` — 16 ảnh banner chuẩn hóa tên (fc-mobile.png, blox-fruits.png, genshin-impact*.png, arknights-endfield.png, valorant.png, fc-online.png, lien-quan-*.png) + `storage/app/public/banners/` 3 banner hero. Ảnh trùng md5 đã loại.
- **Mới**: `CHAY_SEED_DANHMUC.bat` — storage:link + seed + optimize:clear.
- Ảnh cũ trong DB giữ nguyên; seeder chỉ thêm/cập nhật bản ghi theo slug.

### Design system mới — "Graphite & Gold"
- **Viết lại**: `public/css/shopgame-theme.css` — tokens mới: nền graphite #08090B→#1A1D24, NHẤN gold #E8B33C (CTA + giá), Space Grotesk (display/tiền) + Be Vietnam Pro (thân bài), radius 4–10px, shadow tối giản. File định nghĩa LẠI mọi biến cũ (--primary/--bg-card/--text-muted/...) nên view chưa sửa vẫn nhận theme (D17 giữ nguyên: load cuối).
- **Sửa**: `layouts/user/head.blade.php` — load 2 font mới; bỏ khối :root inline cũ (2 nguồn sự thật).
- Component mới: .yk-btn (primary/ghost/danger + size), .yk-card, .yk-tile (product card ảnh 16/9, badge, giá gold), .yk-modal, .yk-empty, .yk-badge, .yk-breadcrumb, .yk-pager (phân trang 38px), .yk-skeleton, focus-visible gold, prefers-reduced-motion, min-height 44px vùng chạm mobile, overflow-x clip toàn cục.

### Redesign view
- **Viết lại**: `user/home.blade.php` (1232→~1090 dòng) — hero slider (dùng 3 banner kèm sẵn khi Admin chưa upload), panel top-nạp 2 tab, marquee + ticker gọn, flash sale tokens mới, lưới danh mục theo game, section dịch vụ/wheel/proof. JS slider/tab/countdown viết lại gọn, tôn trọng reduced-motion. Không còn 25 gradient hardcode; giữ toàn bộ dữ liệu động.
- **Viết lại**: `user/category/show-all.blade.php`, `show-group.blade.php`, `show.blade.php`, `user/service/show-all.blade.php`, `user/random/show-all.blade.php`, `user/pagination/custom.blade.php`, `components/hero-header.blade.php`.
- **Sửa**: `user/random/category.blade.php` — trang mua túi mù đổi sang yk-modal/yk-qty; JS mua hàng + id giữ nguyên 100%.
- **Sửa**: `user/account/detail.blade.php` — 3 nút mua chuyển sang tông gold; logic mua/trả góp không đổi.
- **Sửa**: header/footer — các accent cyan/blue còn sót đổi sang gold; footer/menu giữ cấu trúc.
- **Sửa logic thật (bug)**: `GameCategoryController::index` — filter `price_from`/`price_to` của form TRƯỚC ĐÂY KHÔNG được xử lý (form gửi 2 field này nhưng controller chỉ đọc `price_range`) → thêm xử lý; thêm sort `sort=price_asc|price_desc|oldest` (select mới trong view).

### Đã kiểm tra
- phpchk (brace/paren token): 6 file PHP OK. bladechk + trace thủ công: mọi view cân bằng (@section inline title + content chuẩn Laravel).
- Seeder đối chiếu schema từ shopgame.sql: đủ cột, 0 slug trùng, 23 bản ghi.
- 0 reference còn sót tới class cũ (gaming-card/hero-split/...) trong các view đã viết lại.
- ⚠️ Sandbox không có PHP/MySQL/npm nên CHƯA chạy migrate/seed/serve — user cần chạy `CHAY_SEED_DANHMUC.bat` sau `CHAY_MIGRATE.bat`.
- ⚠️ Profile/mission/withdraw pages còn accent blue nhẹ (thấp ưu tiên, không thuộc luồng mua chính).

## [1.1.0] — 14/11/2026 — Nâng cấp Admin Panel (dễ dùng + cấu hình chuyên sâu)

### Mới — Cài đặt nâng cao (Admin Panel)
- **Mới**: `app/Http/Controllers/Admin/SiteSettingsController.php` — trang "Cài đặt nâng cao": chế độ bảo trì (kèm thông điệp), giới hạn rút tiền min/max, biên lợi nhuận dashboard, bật/tắt popup chào mừng
- **Mới**: `resources/views/admin/settings/advanced.blade.php` — view tương ứng; route `admin.settings.advanced` + `admin.settings.advanced.update`
- **Mới**: `resources/views/errors/` — bộ trang lỗi theo theme dark (401/403/404/419/429/500/503 + minimal.blade.php dùng chung)
- **Sửa**: `app/Http/Controllers/User/WithdrawalController.php` — min/max rút tiền đọc từ config (`min_withdraw_money`/`max_withdraw_money`) thay vì hardcode; thông báo lỗi thân thiện
- **Sửa**: `resources/views/user/profile/withdraw.blade.php` — min/max hiển thị theo config

### Mới — Quản trị nhanh trên Dashboard
- **Mới**: `app/Http/Controllers/Admin/QuickActionController.php` — duyệt/từ chối rút tiền, hoàn thành đơn dịch vụ ngay từ dashboard (JSON, không reload); từ chối rút tiền hoàn tiền chuẩn (transaction + lockForUpdate + MoneyTransaction refund)
- **Sửa**: `app/Http/Controllers/Admin/DashboardController.php` — thêm `pendingCounts` (badge số việc đang chờ)
- **Sửa**: `resources/views/admin/dashboard.blade.php` — khối "Quản trị nhanh" (hàng đợi rút tiền + đơn dịch vụ, nút duyệt/từ chối AJAX với Swal); JS ở @push('scripts')

### Hiệu năng & dữ liệu admin
- **Sửa**: `app/helpers.php` — `config_get()` chuyển sang cache toàn bảng (`configs_all`, 1 query thay vì N); `config_set()` làm mới cache; `config_clear_cache()` không còn flush toàn bộ cache app (chỉ forget key configs)
- **Sửa**: `app/Http/Controllers/Admin/MoneyWithdrawalController.php` + `ResourceWithdrawalController.php` — thêm tìm kiếm (ID/user/ghi chú), lọc trạng thái, per_page động (trước đây load TOÀN BỘ bảng)
- **Sửa**: 2 view lịch sử rút tiền — thêm select lọc trạng thái

### Ghi log & giao diện admin
- **Sửa**: `app/Http/Controllers/Admin/MoneyWithdrawalController.php` — log approve/reject vào `admin_activity_logs`
- **Sửa**: `app/Http/Controllers/Admin/UserController.php` — log thay đổi role/balance/banned (old+new values)
- **Sửa**: `app/Http/Controllers/Admin/GameAccountController.php` — log xóa tài khoản game
- **Sửa**: `resources/views/layouts/admin/head.blade.php` — favicon không còn hardcode site khác; dùng config → fallback `/img/favicon.svg`
- **Sửa**: `resources/views/layouts/admin/footer.blade.php` — bỏ credit cá nhân "Bùi Văn Quyết", hiển thị site_name
- **Sửa**: `resources/views/layouts/admin/sidebar.blade.php` — thêm mục "Cài đặt nâng cao"

### Đã kiểm tra
- 15 file PHP + 11 blade: cân bằng cú pháp bằng script token (phpcheck.py/bladecheck2.py trong outputs) — 0 lỗi
- Route names gọi trong views đều tồn tại trong routes/admin.php
- ⚠️ Lưu ý: `admin_activity_logs` thuộc 9 migration 2026_11_13 chưa migrate — các tính năng ghi log chỉ hoạt động sau khi user chạy `CHAY_MIGRATE.bat`

## [1.0.0] — 13/11/2026 — Giai đoạn B, Phase 1-3 (code)

### Phase 1 — Production Cleanup & Security
- **Xóa**: `public/terminal.php`, `check_wheel_hist.php`, `__MACOSX/` (1066 file), 26 file `.css@time=...` trùng lặp, `.DS_Store`
- **Sửa**: `app/Services/TheGioiDevService.php` — bật SSL verify; bỏ curlErr/raw/response khỏi response lỗi (log server-side)
- **Sửa**: `app/Services/DichVuDarkService.php` — tương tự
- **Mới**: `.env.example` — production-ready (APP_ENV=production, DEBUG=false, SESSION_SECURE_COOKIE=true, LOG_LEVEL=warning), không secret
- **Xóa**: `database/seeders/UsersTableSeeder.php` (admin/moderator/staff mật khẩu cứng `admin123456`)
- **Mới**: `app/Console/Commands/CreateAdminCommand.php` — `admin:create-admin` (nhập ẩn + confirm, tự sinh mật khẩu 16 ký tự, không hardcode)
- **Sửa**: `CHAY_TAO_ADMIN.bat` — chuyển sang lệnh an toàn, không in mật khẩu công khai

### Phase 2 — Remove Demo Data & Prices
- **Xóa** (chuyển sang `database/seeders/local-demo/`, namespace `Database\Seeders\LocalDemo`, chặn chạy production): GameAccountSeeder, RandomCategoryAccountsTableSeeder, ServicePackagesSeeder (7 gói demo 40k-120k), LuckyWheelHistoriesTableSeeder, MoneyWithdrawalHistoriesTableSeeder, WithdrawalHistoriesTableSeeder, NotificationsTableSeeder, BankAccountSeeder (số tài khoản thật của cá nhân)
- **Sửa**: `database/seeders/DatabaseSeeder.php` — chỉ seed khung (config rỗng, danh mục, dịch vụ, vòng quay)
- **Sửa**: `database/seeders/ConfigSeeder.php` — không secret/thương hiệu; xóa logo/banner/favicon/domain bên thứ ba; site_name='ShopGame'; payment + social tắt mặc định; xóa secret OAuth cũ đã lộ (D7)
- **Sửa**: `resources/views/user/home.blade.php` — xóa 8 review giả + text ngẫu nhiên; ẩn section khi không có PurchaseHistory thật; sanitizer cho top_deposit_reward + home_notification
- **Sửa**: `resources/views/user/reviews.blade.php` — chỉ dùng dữ liệu thật, bỏ 24 tên giả + pagination giả
- **Sửa**: `app/Http/Controllers/User/HomeController.php` — `getRealReviewEntries()` + `maskName()`; terms/privacy dùng HtmlSanitizer
- **Sửa**: `resources/views/layouts/user/app.blade.php` — xóa toast mua giả (markup+CSS+JS); support buttons chỉ hiện khi config có giá trị; xóa beacon Cloudflare site khác
- **Sửa**: `resources/views/layouts/user/footer.blade.php` — bỏ credit CMSBVQ; liên hệ chỉ hiện khi có giá trị thật
- **Sửa**: `app/Models/GameService.php` — thêm `activePackages()` (active + price>0), `min_price`, `is_orderable`
- **Sửa**: `app/Http/Controllers/User/GameServiceController.php` — eager load `activePackages`
- **Sửa**: `resources/views/user/service/show.blade.php` — render gói hợp lệ; empty state "Giá dịch vụ đang được cập nhật"; khóa form đặt khi chưa có giá; xóa JSON-LD giá giả (5000/500000) và rating giả (4.9/1280)
- **Mới**: `public/img/favicon.svg`, `public/img/logo.svg` — fallback thương hiệu ShopGame (chữ S gradient xanh)

### Phase 3 — Business Logic & DB Security
- **Sửa**: `app/Models/GameAccount.php`, `app/Models/RandomCategoryAccount.php` — accessor mã hóa/giải mã Crypt cho `password` (idempotent, fallback plain cũ)
- **Mới**: `app/Console/Commands/EncryptGamePasswordsCommand.php` — `accounts:encrypt-passwords --dry-run` backfill an toàn (nhận dạng payload eyJ + 2 dấu chấm; không mã hóa 2 lần; cảnh báo nếu APP_KEY đã đổi)
- **Sửa**: `app/Http/Controllers/User/ServiceOrderController.php` — transaction bao trọn từ đọc số dư; `lockForUpdate()`; package phải thuộc service + active + price hợp lệ; service active; chặn double-submit; lỗi trả message thân thiện, log server-side
- **Sửa**: `routes/web.php` — `POST service/{slug}/order` thêm `auth` + `not-banned`
- **Mới**: `app/Support/HtmlSanitizer.php` — allowlist tag/attr; chặn script/iframe/form/input/onclick/javascript:; áp dụng: home_notification, top_deposit_reward, terms_of_use, privacy_policy

### Tài liệu
- **Mới**: `docs/PROJECT_CONTEXT.md`, `docs/DECISIONS.md`, `docs/TASKS.md`, `docs/ARCHITECTURE.md`, `docs/CHANGELOG.md`

### Đã kiểm tra
- 13 file PHP đã sửa: cân bằng cú pháp (script Python kiểm tra token string/comment/bracket) — 0 lỗi
- Blade home/reviews/show: kiểm tra cặp @section/@if/@forelse — OK

### Còn lại (Phase 2-3 tail)
- Sanitize `$news->content`, `$mission->guide_steps`, `page.blade.php` $content
- Rà transaction ở GameAccountController::purchase, RandomAccountController, WithdrawalController (double-spend)
- User chạy trên máy: `CHAY_MIGRATE.bat` → `accounts:encrypt-passwords --dry-run` → quyết định reset kho → `admin:create-admin` → nhập config thương hiệu thật (logo/banner/favicon/OAuth/ thanh toán)
---

## Phase 4-8 (bổ sung, cùng ngày 13/11/2026)

### CSS / Theme
- **Mới**: `public/css/shopgame-theme.css` — design tokens chính thức (#2563EB, #0B0F16, Inter, radius, motion, focus-visible, prefers-reduced-motion, tabular-nums)
- **Sửa**: `public/css/style.css` — toàn bộ #dc2626/#b91c1c/#991b1b → `var(--color-primary)` (0 đỏ hardcode còn lại)
- **Sửa**: `resources/views/layouts/user/head.blade.php` — load theme cuối; loại antd.min.css (269KB) + font Roboto; favicon fallback SVG; dns-prefetch cdnjs
- **Sửa**: `header.blade.php`, `menu-mobile.blade.php` — brand gradient & màu theo tokens

### Dữ liệu demo (tiếp)
- **Sửa**: `home.blade.php` — spotlight 3 thẻ hardcode (Blox Fruits 7.000đ/Genshin 3.000đ/Túi Mù 50K) → render động từ `$services` + minPrice; bỏ banner fallback ảnh demo; flash-sale placeholder → ẩn ảnh lỗi
- **Sửa**: `service/show-all.blade.php` — bỏ tên game hardcode ở title/hero; card hiện "từ X đ" từ DB hoặc "Giá đang cập nhật"; nút mờ khi chưa có giá
- **Sửa**: `category/show-all.blade.php` — bỏ liệt kê game hardcode trong hero
- **Sửa**: `news/index.blade.php`, `random/category.blade.php` — bỏ via.placeholder.com
- **Xóa**: `database/seeders/AdminAccountSeeder.php` (mật khẩu `iloveher`)

### Bảo mật (tiếp)
- **Sửa**: `HomeController` — sanitize terms/privacy, aggregate stats (N+1), withCount wheels
- **Sửa**: `CardDepositController`, `InstallmentController` — bỏ `getMessage()` ra client
- **Sửa**: `DashboardController` — notifications limit 50; profit margin qua config

### Build pipeline
- **Mới**: `resources/css/app.css`, `resources/js/app.js` (entry Tailwind/JS)
- **Sửa**: `vite.config.js` — trỏ file tồn tại (trước đó build FAIL)
- **Xóa**: `webpack.mix.js`; loại `laravel-mix` khỏi `package.json`

### Tài liệu & công cụ
- **Mới**: `DEPLOYMENT.md`, `SECURITY.md`, `TAO_RELEASE.bat`
- **Mới**: `docs/PROJECT_CONTEXT.md`, `docs/DECISIONS.md`, `docs/TASKS.md`, `docs/ARCHITECTURE.md`, `docs/CHANGELOG.md`, `docs/HANDOFF.md`

### Kiểm thử
- 255 file PHP (app, routes, config, database) — 0 lỗi cú pháp (script token-based)
- Blade home/reviews/show/show-all/app/footer — cặp directive hợp lệ
- Content audit: 0 review giả, 0 toast giả, 0 giá hardcode, 0 localhost trong views/seeders, 0 thương hiệu bên thứ ba, 0 rating giả
- Security audit: terminal.php đã xóa, 0 secret hardcode, 0 SSL verify tắt, 0 mật khẩu admin mặc định, 0 getMessage() lộ ra khách hàng
