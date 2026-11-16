# ARCHITECTURE.md — Kiến trúc & cấu trúc mã nguồn

*Sơ đồ logic — kiểm tra code trực tiếp trước khi sửa.*

## Stack
- Laravel 11, PHP 8.2+ (user chạy 8.3.36 Laragon), Blade templates, MySQL
- Auth: Breeze (session) + Socialite (Google/Facebook qua config DB `login_social.*`) + Sanctum (API)
- Admin UI: template cmsbvq (`public/cmsbvq/template/frontend/` — KHÔNG XÓA, admin đang dùng)
- Frontend user: CSS thuần tại `public/css/{style,legacy-compat,modern-gaming,shop-ui}.css` + jQuery + Iconify + FontAwesome + fuiToast
- Build: Vite 5 + Laravel Mix cùng tồn tại trong package.json (chưa quyết — Phase 7)

## Luồng dữ liệu chính
```
routes/web.php → app/Http/Controllers/User/* → Eloquent Models → MySQL
routes/admin.php (middleware 'admin'/'super-admin') → app/Http/Controllers/Admin/*
routes/api.php → Sanctum/công khai có throttle
Views: resources/views/user/* (layout layouts/user/app.blade.php)
       resources/views/admin/* (layout layouts/admin/app.blade.php)
```

## Bảng DB chính
- users (role: member/dev/admin, balance, 2FA cols)
- game_accounts, game_categories, game_groups, random_categories, random_category_accounts
- game_services, service_packages (price, active), service_histories
- money_transactions (type: deposit/withdraw/purchase/refund/subtract), card/bank/usdt deposits
- withdrawal_histories (gold/gem), money_withdrawal_histories (tiền)
- discount_codes + usages, flash_sales + items, lucky_wheels + histories
- purchase_history (user_id, game_account_id, amount), affiliate tables, missions
- configs (key/value, cache 1 ngày qua ConfigHelper::get)
- Mới (sprint trước, CHƯA migrate): business_units, products, product_logs, account logs, admin_activity_logs, payment_methods, 2FA cols, dev role

## Helpers toàn cục (app/helpers.php)
- `config_get($key, $default)` — đọc configs DB + cache 1 ngày
- `config_set($key, $value)`
- `display_status()`, `display_status_admin()`, `display_status_transactions_admin()` — trả HTML badge (render {!! !!})

## Services
- `TheGioiDevService` — API hosting/cron/website thegioidev.com (token qua config DB `thegioidev_api_token`)
- `DichVuDarkService` — API dichvudark.vip (domain/tool)
- `TwoFactorService` — TOTP thuần PHP

## Commands artisan
- `accounts:reset-inventory [--dry-run]` — xóa 4 bảng kho (D3)
- `accounts:encrypt-passwords [--dry-run]` — backfill mã hóa mật khẩu game (D10)
- `admin:create-admin` — tạo admin an toàn (D5)
- `fetch:mb-transactions` — cron ngân hàng MB (schedule mỗi 10 phút)
- `app:clear` — clear mọi cache

## Middleware aliases (app/Http/Kernel.php)
`admin`, `super-admin`, `member`, `not-banned` (EnsureUserNotBanned), `setlocale`

## Các điểm nhạy cảm đã biết
- `resources/views/user/home.blade.php` (1300 dòng) — homepage lớn, nhiều section
- `HtmlSanitizer` (app/Support/) — dùng cho mọi nội dung Admin render {!! !!}
- `SecuritySanitizer` (app/Models/) — mask dữ liệu nhạy cảm khi ghi AdminActivityLog
- Config OAuth: bảng configs key `login_social.*` — secret KHÔNG được vào code
