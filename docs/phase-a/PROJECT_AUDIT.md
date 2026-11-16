# PROJECT AUDIT — Phân tích hiện trạng hệ thống ShopGame

> **Phiên bản:** 1.0 — Ngày lập: 12/11/2026
> **Phạm vi:** Toàn bộ source code trong thư mục dự án (Frontend, Backend, Database, Admin Panel, Auth, API)
> **Trạng thái:** Giai đoạn A — chỉ phân tích, KHÔNG chỉnh sửa code.

---

## 1. Tổng quan dự án

Website thương mại điện tử chuyên về: bán tài khoản game (cả tài khoản chính lẫn "tài khoản random"), dịch vụ cày thuê, làm nhiệm vụ vượt link kiếm tiền, bán hosting/website (tích hợp TheGioiDev & DichVuDark), vòng quay may mắn, nhiệm vụ kiếm tiền, ví nội bộ (số dư, nạp/rút).

Hệ thống đã từng qua một đợt nâng cấp UI/UX + đa ngôn ngữ trước đó (xem `IMPLEMENTATION_REPORT.md` cũ ngày 12/11/2026), tuy nhiên nhiều hạng mục còn dở dang và không đồng bộ.

## 2. Kiến trúc & công nghệ hiện tại

| Hạng mục | Giá trị thực tế trong code |
|---|---|
| Framework | **Laravel 11** (`laravel/framework: ^11.0`, PHP ^8.2) |
| Môi trường chạy | Laragon trên Windows (`CHAY_WEB.bat` chạy `php-8.3.36 artisan serve --port=8080`), DB MySQL (`shopgame`, user root, không mật khẩu) |
| Frontend | **Blade templates**, không dùng Vue/React. CSS thuần trong `public/css/` (`style.css` 5.851 dòng, `modern-gaming.css`, `legacy-compat.css`) + template admin từ `/cmsbvq/template/frontend/` |
| JS | jQuery 3.7.1, Iconify, ApexCharts (dashboard admin), Select2, DataTables, SweetAlert2, fui-toast — chủ yếu từ CDN |
| Build tool | `vite.config.js` trỏ tới `resources/assets/css/app.css` **không tồn tại** → Vite thực chất không được dùng; mọi asset đều là file tĩnh trong `public/` |
| Auth | Laravel Breeze (session-based), Google/Facebook Socialite, Sanctum (API token, ít dùng) |
| Session / Cache | `SESSION_DRIVER=file`, `CACHE_DRIVER=file`, `QUEUE_CONNECTION=sync` |
| DB | MySQL, InnoDB, utf8mb4_unicode_ci. Dump đầy đủ tại `shopgame.sql` (300KB) |
| Múi giờ ngôn ngữ | Hệ thống i18n tự xây qua bảng `languages` + `resources/lang`, middleware `SetLocale` |
| Tests | Gần như trống: chỉ `ExampleTest`, `Auth/*`, `ProfileTest` mặc định của Breeze |

## 3. Sơ đồ cấu trúc thư mục then chốt

```
shopgame/
├── app/
│   ├── Console/Commands/        → CheckUsdtDeposits, FetchMBTransactions, ClearAllCaches
│   ├── Helpers/UploadHelper.php → upload/delete ảnh vào storage
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/  (25 controllers)
│   │   │   ├── User/   (15 controllers)
│   │   │   └── Auth/   (Breeze + Google + Facebook)
│   │   ├── Middleware/ → AdminMiddleware, MemberMiddleware, AffiliateMiddleware, SetLocale…
│   │   └── Requests/Auth/LoginRequest.php
│   ├── Models/ (41 models)
│   └── Services/ → TheGioiDevService.php, DichVuDarkService.php (API hosting/domain)
├── database/migrations/ (50 file)
├── resources/views/ → admin/ (79 blade), user/ (43 blade), layouts/{admin,user}
├── routes/ → web.php, admin.php, api.php, auth.php (member.php trống không dùng)
├── public/ → css/, assets/, cmsbvq/template (admin UI kit), storage/ (uploads symlink), uploads/banks
└── shopgame.sql → dump dữ liệu thực tế hiện có
```

## 4. Mô hình dữ liệu kinh doanh hiện có (36 bảng)

**Sản phẩm — 3 hệ song song (điểm phức tạp lớn nhất):**

1. **Tài khoản chính:** `game_categories` (danh mục) → `game_accounts` (từng tài khoản, có `account_name`, `password` plain, `price`, `status` enum `available|sold|installment`, `details` JSON, `thumb`, `images` JSON, `buyer_id`).
2. **Tài khoản random:** `random_categories` → `random_category_accounts` (1679 bản ghi, `status` enum `available|sold`, `server`, `thumbnail`, `note_buyer`).
3. **Dịch vụ cày thuê:** `game_services` (type: `gold|gem|leveling`) → `service_packages` → đặt hàng ghi vào `service_histories` (status `pending|processing|completed|cancelled`).

**Hệ thống chung:** `game_groups` (nhóm game: Liên Quân, Free Fire…), `attributes` (thuộc tính động), `flash_sales` + `flash_sale_items` (flash sale theo category), `discount_codes` + `discount_code_usages`, `news`, `notifications`.

**Ví & tiền:** `users.balance`, `money_transactions` (deposit/withdraw/purchase/refund + balance_before/after), `card_deposits` (callback thẻ cào qua partner `khothegame.com`, sign md5), `bank_deposits` (auto fetch MB Bank qua command `fetch:mb-transactions`), `usdt_deposits` + `usdt_accounts` (Spay5s), `withdrawal_histories` (gold/gem), `money_withdrawal_histories` (rút tiền mặt).

**Khác:** `lucky_wheels` + `lucky_wheel_histories` + `reward_items`, `missions` + `mission_giftcodes` + `mission_histories` (vượt link kiếm tiền — bảng mới 11/2026, **chưa có trong dump SQL**), `installments` (trả góp game account), affiliate (`affiliate_histories`, `users.referrer_id`), `website_orders` (mua hosting qua TheGioiDev API), `configs` (key-value cấu hình), `languages`.

## 5. Luồng nghiệp vụ hiện hoạt (đã xác minh qua code)

**Mua tài khoản chính** (`User\GameAccountController@purchase`): `DB::transaction` + `lockForUpdate` → kiểm tra flash sale → giảm giá → trừ `users.balance` → đặt `game_accounts.status = sold, buyer_id` → ghi `money_transactions`. **Ổn định về race condition.**

**Mua tài khoản random** (`User\RandomAccountController@purchase`): tương tự, ghi `money_transactions` với `reference_id = 'RA-{id}'`. Ổn định.

**Thuê dịch vụ** (`User\ServiceOrderController@processOrder`): tạo `service_histories` (pending) → trừ số dư → admin xử lý thủ công.

**Nạp tiền:** 3 kênh — thẻ cào (callback API `/api/callback/card`), ATM/QR (build ảnh VietQR **client-side** từ `img.vietqr.io` + thông tin `bank_accounts`), USDT (lệnh `CheckUsdtDeposits` poll Spay5s). Nạp ATM hiện là **khai báo tay + admin duyệt** qua `bank_deposits`.

**Điểm chết đã phát hiện (sẽ sửa ở Giai đoạn B):**
- Route `/api/auto-bank-deposit` gọi command `fetch:mb-transactions` **không có auth/throttle** → ai gọi cũng được (quét giao dịch ngân hàng).
- `RandomAccountPurchase` model có `fillable` đầy đủ nhưng **không được dùng** trong luồng mua random (không có bảng tương ứng trong dump) — code chết gây hiểu nhầm.
- `MemberMiddleware` không được gắn vào route nào.

## 6. Admin Panel hiện trạng

- Route `routes/admin.php` — prefix `admin`, middleware `['auth', 'admin']`, 25 controllers, 79 views.
- Layout: template **Berry** (`/cmsbvq/template/frontend/`) — dark/light mode, sidebar, ApexCharts dashboard.
- Dashboard (`Admin\DashboardController`): thống kê users, accounts (tính cả status `locked`/`pending` **không tồn tại trong enum** → luôn = 0), services, packages, categories, wheels, transaction summary, biểu đồ 7 ngày (N+1 query).
- CRUD đầy đủ cho: users, categories, game-groups, accounts, random-categories, random-accounts, services, packages, bank-accounts, usdt-accounts, discount-codes, flash-sales, lucky-wheels, reward-items, news, missions, notifications, settings (general/social/email/payment/login/terms), history (7 loại), withdrawals, installments, affiliates.
- Điểm yếu: không có Activity Log, không có 2FA, không có trang login riêng cho admin (dùng chung `/login` với user), dashboard còn số liệu tính sai (trừ `LuckyWheelHistory::sum('total_cost')` từ doanh thu), nhiều trang history/danh sách load không phân trang tốt, chưa có SEO/UX đồng bộ với website frontend (dùng 2 hệ design khác nhau).

## 7. Bảo mật hiện trạng

| Khu vực | Hiện trạng | Đánh giá |
|---|---|---|
| Phân quyền | `AdminMiddleware` check `role === 'admin'` ở **backend**, mọi admin route đều có middleware. Người dùng tự đăng ký luôn `role = member` (không nhận `role` từ input). | ✅ Tốt |
| Mật khẩu | bcrypt (default `config/hashing.php`), `User` cast `password => hashed`. | ✅ Đạt |
| Brute force | Breeze `ensureIsNotRateLimited` (5 lần/phút). | ✅ Đạt (cần riêng cho admin login) |
| CSRF | VerifyCsrfToken toàn cục, không loại trừ route nào. API callback thẻ cào dùng sign md5 riêng. | ✅ Đạt |
| SQL Injection | Toàn bộ dùng Eloquent/Query Builder, không thấy raw query với input user. | ✅ Đạt |
| XSS | Có 12 chỗ `{!! !!}` trong views user — chủ yếu render HTML config do **admin nhập** (mô tả, banner, reward content) → rủi ro thấp nhưng cần liệt kê kiểm soát. | ⚠️ Cần rà soát |
| **Mật khẩu tài khoản game** | Lưu **plain text** trong `game_accounts.password`, chỉ hiển thị cho buyer sau khi mua (trang `purchased-accounts`). Nhưng tài khoản admin có thể xem → chấp nhận được về nghiệp vụ, **cần kiểm soát hơn ở danh sách & log**. | ⚠️ Đánh giá lại |
| **API token trong DB** | `configs` chứa `thegioidev_api_token` thật trong dump. Admin settings hiển thị key một phần. | ⚠️ Chấp nhận (server-side) nhưng cần mask |
| Session | file driver, lifetime chuẩn, `session()->regenerate()` khi login. | ✅ Đạt |
| Upload | `UploadHelper` validate mimes jpeg/png/jpg/gif. Không kiểm tra MIME thực (content). | ⚠️ Cần siết |
| 2FA | Không có. | ❌ Đề xuất bổ sung |
| Activity Log | Không có. | ❌ Đề xuất bổ sung |

## 8. UI/UX hiện trạng

- **Frontend (user):** phong cách "Minimalist Dark Slate Cyber" mới áp dụng gần đây (theo báo cáo cũ), header submenu 2 cấp, hero 30/70 (Top nạp + banner slider), bento grid services, danh mục. CSS thuần 8.000+ dòng, font Inter/Roboto, Iconify + FontAwesome + Ant Design CSS (nhiều nguồn icon trùng lặp).
- **Admin:** template Berry khác biệt hoàn toàn với frontend (light/dark riêng).
- Vấn đề: thừa CSS (`legacy-compat.css` 1.029 dòng), nhiều icon lib song song, vài chỗ style inline dài, còn gradient/glow đậm (`box-shadow` neon ở nút nạp tiền), không có design token chung (biến CSS rời rạc giữa 2 hệ), responsive còn lỗi ở bảng admin trên mobile.

## 9. Khoảng trống so với yêu cầu đề bài

1. **Chưa có hệ danh mục theo 6 nhóm kinh doanh** (Accounts / Boosting / Link Monetization / Tools / Cloud Phone / Other) — hiện chỉ có "game groups" theo tựa game + 2 loại danh mục riêng biệt.
2. **Chưa có trang quản lý "phương thức thanh toán" động** — payment đang hard-code theo key config (card/bank/momo/usdt), frontend render theo điều kiện `config_get`, không thêm/xóa/đổi logo/hướng dẫn được từ admin.
3. **Chưa có Account Inventory chuyên biệt** với 4 trạng thái Available/Reserved/Sold/Disabled (hiện chỉ `available|sold|installment`), chưa có lịch sử thay đổi (audit trail) cho từng tài khoản.
4. **Chưa có Activity Log admin.**
5. **Chưa có báo cáo doanh thu đúng nghĩa** (dashboard còn tính sai).
6. Reset dữ liệu kho (Phần 4 đề bài) — được lên kế hoạch chi tiết trong `DATABASE_AND_SYNC_PLAN.md`.

## 10. Kết luận khuyến nghị

Hệ thống nền tảng **tốt, đủ tin cậy để nâng cấp tiếp** (auth backend-kiểm-soát, transaction có lock, dữ liệu thật). Chiến lược: **bảo toàn toàn bộ chức năng cũ**, nâng cấp theo lớp: (1) chuẩn hóa danh mục 6 nhóm trên dữ liệu hiện có, (2) Account Inventory + audit trail, (3) Payment method manager, (4) Activity Log + bảo mật admin, (5) tinh giản UI/UX hai phía, (6) reset kho tài khoản theo quy trình an toàn có xác nhận.
