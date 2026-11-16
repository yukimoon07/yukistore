# HANDOFF.md — Bàn giao dự án ShopGame

*Cập nhật: 14/11/2026. Tài liệu này đủ để một AI/session mới tiếp tục mà không cần đọc lại lịch sử hội thoại.*

## 1. Dự án đang làm gì

Nâng cấp website **ShopGame** (Laravel 11, bán acc game + cày thuê) để **đưa lên production public**: làm sạch localhost/demo/secret, sửa bảo mật, UI Premium Dark, chuẩn bị release package. Sprint 14/11: nâng cấp Admin Panel (quản trị nhanh, cài đặt nâng cao).

## 2. Công nghệ

- Laravel 11, PHP 8.2+ (user chạy 8.3.36 Laragon trên Windows), Blade, MySQL
- Admin template: `public/cmsbvq/**` (KHÔNG XÓA)
- Frontend: CSS tĩnh `public/css/` (style, legacy-compat, modern-gaming, **shopgame-theme**), jQuery, Iconify, FontAwesome
- Build: Vite 5 (Mix đã loại) — Blade không dùng @vite, asset tĩnh
- Auth: Breeze + Socialite + Sanctum; 2FA TOTP tự viết

## 3. Trạng thái hiện tại

**Toàn bộ 8 phase đã hoàn thành phần CODE và kiểm tra cú pháp (255 file PHP — 0 lỗi).**

Backup trước mọi thay đổi: `storage/backups/shopgame_before_phase1_20261113_1622.tar.gz` (47MB).

Chi tiết từng thay đổi: đọc `docs/CHANGELOG.md` + `docs/TASKS.md`.
Quyết định kỹ thuật: `docs/DECISIONS.md` (D1–D17).
Kiến trúc: `docs/ARCHITECTURE.md`.

## 4. Đã hoàn thành (tóm tắt)

- **P0:** backup, audit
- **P1:** xóa terminal.php, __MACOSX, .DS_Store, CSS trùng; `.env.example` sạch; SSL verify bật; `admin:create-admin` thay seeder mật khẩu; sửa .bat
- **P2:** 9 seeder demo → `database/seeders/local-demo/`; DatabaseSeeder chỉ seed khung; ConfigSeeder sạch (không secret/thương hiệu); xóa review giả + toast mua giả + giá hardcode + JSON-LD giá/rating giả; service ẩn khi chưa có giá ("Giá đang cập nhật" + khóa form); footer/support chỉ hiện khi config có giá trị
- **P3:** mã hóa mật khẩu game (Crypt, idempotent + command backfill); ServiceOrder transaction + lockForUpdate + check package-service + lỗi thân thiện; route đặt dịch vụ cần auth+not-banned; HtmlSanitizer áp dụng 7 vị trí; lockForUpdate cho mọi luồng trừ tiền (mua acc, random, rút); PurchaseHistory ghi khi mua (nguồn reviews thật)
- **P4:** `shopgame-theme.css` (tokens #2563EB/#0B0F16/Inter/radius/motion/reduced-motion); style.css đỏ→tokens; homepage spotlight render động từ DB
- **P5:** favicon.svg + logo.svg (wordmark tự thiết kế), fallback khi config rỗng
- **P6:** dashboard notifications limit 50, profit margin qua config; adminFilter macro đã có sẵn chuẩn
- **P7:** N+1 HomeController sửa (aggregate + withCount); loại antd 269KB + font Roboto + via.placeholder; **sửa vite build fail** (entry trỏ file không tồn tại)
- **P8:** DEPLOYMENT.md, SECURITY.md, TAO_RELEASE.bat; kiểm tra content/security criteria
- **Sprint 14/11 (Admin Panel):** xem `docs/CHANGELOG.md` [1.1.0] — Cài đặt nâng cao (bảo trì/giới hạn rút tiền/biên lợi nhuận), Quản trị nhanh dashboard, cache config toàn bảng, tìm kiếm+lọc lịch sử rút tiền, log hành động admin, bộ trang lỗi dark theme

## 5. Chưa hoàn thành / cần USER chạy trên máy

1. `CHAY_MIGRATE.bat` — 9 migrations sprint trước chưa chạy
2. `php artisan accounts:encrypt-passwords --dry-run` → chạy thật (mã hóa mật khẩu game)
3. Quyết reset kho: `CHAY_RESET_KHO.bat` (dry-run trước; giữ game_groups)
4. `CHAY_TAO_ADMIN.bat` → tạo admin + bật 2FA
5. Admin Panel: nhập logo/banner/favicon/liên hệ/OAuth client mới/cổng thanh toán
6. Thêm danh mục, acc game, dịch vụ + giá chính thức
7. `npm install && npm run build` (verify pipeline)
8. Kiểm thử trình duyệt 360px–1920px
9. `TAO_RELEASE.bat` → đóng gói
10. **Revoke OAuth Google/Facebook client cũ** (secret đã lộ trong ZIP gốc): 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, 481beed7538a8b7318c45e94401f4e3c

## 6. Lỗi quan trọng đã gặp (đừng lặp lại)

- **Regex mass-edit `getMessage()`** phá cú pháp 12 controller → phải khôi phục từ backup. Bài học: không sửa hàng loạt bằng regex đa dòng; sửa từng chỗ hoặc dùng pattern chặt. Quyết định D14: giữ getMessage() ở admin-only.
- **`rm -rf __MACOSX`** treo (1066 file rác MAC) → xóa từng con-dir trước rồi rmdir.
- **`sed -i '396,450d'`** xóa sai dòng trong app.blade.php → khôi phục từ backup tar rồi ghép lại bằng sed -n '1,243p' + '448,451p'.
- Sandbox không có PHP/npm network → kiểm cú pháp bằng `/tmp/phpsyntax.py` (token-based, xử lý string/comment/heredoc) + `/tmp/bladecheck.py` (false positive: inline @section, @empty thuộc @forelse).
- Xóa file workspace cần tool `allow_cowork_file_delete` (đã bật).

## 7. Quyết định KHÔNG ĐƯỢC thay đổi

- D3: reset kho chỉ xóa 4 bảng (giữ game_groups)
- D5: không tái tạo seeder mật khẩu admin cứng
- D7: secret OAuth cũ coi như lộ — buộc rotate
- D8: không hiển thị giá khi chưa cấu hình; không tự sinh giá
- D10: KHÔNG đổi APP_KEY khi DB đã có dữ liệu mã hóa
- D13: không hardcode token/ảnh site khác
- D15: không khôi phục laravel-mix
- D17: thứ tự CSS — shopgame-theme.css load cuối

## 8. Nhiệm vụ tiếp theo (nếu được yêu cầu tiếp tục)

1. Hỗ trợ user chạy migrate/backfill (xử lý lỗi nếu có khi chạy thật) — **cần migrate để dùng được admin_activity_logs + cài đặt nâng cao**
2. Rà soát kết quả kiểm thử trình duyệt user báo lại
3. Chuẩn hóa thêm admin CRUD views nếu user yêu cầu (bảng/search UI)
4. Kiểm tra `php artisan test` khi user chạy (sandbox không chạy được)
5. SEO: robots.txt + sitemap động + meta per-page
6. Test Feature cho luồng tiền (purchase/withdraw/service order)
7. Song ngữ VI/EN theo I18N_PLAN.md; UI redesign theo REDESIGN_PLAN.md (đối chiếu lại vì Phase 4 đã làm một phần)
8. Chế độ bảo trì hiện bật/tắt qua config `site_maintenance` + thông điệp; trang 503 dùng `resources/views/errors/minimal.blade.php` (đọc message từ `config('app.maintenance_message')` — có thể nối config DB sau nếu muốn)

## 9. File cần đọc đầu tiên

1. `docs/HANDOFF.md` (file này)
2. `docs/PROJECT_CONTEXT.md` — bối cảnh + hạn chế môi trường
3. `docs/TASKS.md` — trạng thái chi tiết từng phase
4. `docs/DECISIONS.md` — D1–D17
5. `docs/CHANGELOG.md` — file đã sửa/tạo/xóa
