# SPRINT 1 — BÁO CÁO TRIỂN KHAI (Giai đoạn B)

**Ngày:** 13/11/2026
**Phạm vi:** Sprint 0 (chuẩn bị) + Sprint 1 (nền tảng bảo mật & database) + yêu cầu bổ sung (2FA, auth khách hàng)
**Trạng thái:** Hoàn thành phần code — **CHƯA chạy migrate trên DB** (cần bạn chạy trên máy có MySQL).

---

## 1. Quyết định đã thực hiện theo phê duyệt của bạn

| Quyết định | Cách hiện thực |
|---|---|
| Giữ lại `game_groups` sau reset | Command `accounts:reset-inventory` chỉ xóa 4 bảng kho: `game_accounts`, `game_categories`, `random_categories`, `random_category_accounts`. `game_groups` không nằm trong danh sách xóa. |
| Cài 2FA cho admin | `TwoFactorService` (TOTP RFC 6238 tự viết PHP thuần, không cần cài thêm package) + trang `/admin/security/two-factor` + màn hình challenge khi đăng nhập. |
| Hoàn thiện đăng nhập/đăng ký khách hàng | Chặn user `banned` khi đăng nhập/đăng nhập Google/Facebook; validate username chặt; chống spam đăng ký; hiện lỗi validation; chỉ báo độ mạnh mật khẩu. |

---

## 2. File đã tạo mới

### Migrations (7 file — chỉ THÊM bảng/cột, không xóa gì)
```
database/migrations/2026_11_13_000001_create_business_units_table.php
database/migrations/2026_11_13_000002_create_products_table.php
database/migrations/2026_11_13_000003_add_inventory_to_game_accounts_table.php
database/migrations/2026_11_13_000004_add_inventory_to_random_category_accounts_table.php
database/migrations/2026_11_13_000005_create_account_logs_tables.php
database/migrations/2026_11_13_000006_create_admin_activity_logs_table.php
database/migrations/2026_11_13_000007_create_payment_methods_table.php
database/migrations/2026_11_13_000008_add_two_factor_to_users_table.php
```

### Models (8 file)
```
app/Models/BusinessUnit.php        — 6 nhóm kinh doanh, đếm sản phẩm
app/Models/Product.php             — sản phẩm Tools/Cloud Phone/Other
app/Models/ProductLog.php
app/Models/GameAccountLog.php
app/Models/RandomAccountLog.php
app/Models/AdminActivityLog.php    — có method record() tự mask dữ liệu nhạy cảm
app/Models/PaymentMethod.php       — config mã hóa Crypt, toPublicArray() không lộ secret
app/Models/SecuritySanitizer.php   — mask password/token trước khi ghi log
app/Models/Concerns/TwoFactorAuthenticatable.php  — trait 2FA (secret mã hóa, recovery codes hashed)
```

### Services & Controllers
```
app/Services/TwoFactorService.php                    — TOTP thuần PHP
app/Http/Controllers/Auth/AdminLoginController.php   — đăng nhập admin riêng + 2FA flow
app/Http/Controllers/Admin/TwoFactorController.php   — bật/tắt/tạo lại mã khôi phục
app/Http/Middleware/EnsureUserNotBanned.php          — chặn phiên user bị khóa
```

### Views (4 file)
```
resources/views/layouts/admin/auth.blade.php        — layout riêng cho trang đăng nhập (không có sidebar)
resources/views/admin/login.blade.php               — đăng nhập quản trị
resources/views/admin/two-factor-challenge.blade.php — nhập OTP khi đăng nhập
resources/views/admin/two-factor.blade.php          — trang thiết lập 2FA
```

### Commands (2 file)
```
app/Console/Commands/ResetInventory.php              — reset kho an toàn: dry-run, backup, xác nhận, transaction, log
app/Console/Commands/ReleaseExpiredReservations.php  — tự nhả tài khoản reserved quá hạn
```

### Files hỗ trợ (3 file .bat)
```
CHAY_MIGRATE.bat      — backup + chạy migration + xóa cache
CHAY_RESET_KHO.bat    — chạy reset kho (hỏi dry-run hay RESET)
CHAY_WEB.bat          — cập nhật, hiện thêm địa chỉ /admin/login
```

---

## 3. File đã chỉnh sửa

| File | Thay đổi |
|---|---|
| `app/Models/User.php` | Thêm trait `TwoFactorAuthenticatable`; ẩn `two_factor_secret`, `two_factor_recovery_codes` khỏi serialization |
| `app/Http/Kernel.php` | Đăng ký middleware `not-banned` |
| `routes/admin.php` | Thêm nhóm route `/admin/login`, `/admin/two-factor-challenge`, `/admin/security/two-factor` (đặt **trước** nhóm auth+admin) |
| `routes/api.php` | **Chặn lỗ hổng:** `/api/auto-bank-deposit` giờ yêu cầu `auth+admin+throttle:3,1`; callback thẻ thêm throttle 20/phút; thêm `/api/payment-methods` (không trả secret) |
| `routes/web.php` | Nhóm route profile dùng thêm middleware `not-banned` |
| `app/Http/Requests/Auth/LoginRequest.php` | Chặn đăng nhập khi `banned` (trước đây bỏ sót) |
| `app/Http/Controllers/Auth/RegisteredUserController.php` | Validate username 3-24 ký tự có regex, email `rfc,dns`, throttle 5 đăng ký/phút/IP, thông báo lỗi tiếng Việt |
| `app/Http/Controllers/Auth/GoogleController.php` | Chặn user `banned` |
| `app/Http/Controllers/Auth/FacebookController.php` | Chặn user `banned` |
| `resources/views/user/register.blade.php` | Thêm gợi ý username, thanh đo độ mạnh mật khẩu, kiểm tra mật khẩu khớp realtime |
| `resources/views/components/alert-error.blade.php` | Hiển thị cả lỗi validation (trước chỉ session error) |
| `resources/views/layouts/admin/sidebar.blade.php` | Thêm menu "Bảo mật hai lớp (2FA)" |
| `CHAY_WEB.bat` | Hiện link admin login |

---

## 4. Hai lỗi bảo mật đã sửa

1. **`/api/auto-bank-deposit` công khai** — bất kỳ ai cũng gọi được lệnh quét giao dịch ngân hàng. → Đã yêu cầu `auth+admin`, throttle 3/phút.
2. **User bị ban vẫn đăng nhập được** — chỉ chặn ở admin login, không chặn ở login thường / Google / Facebook. → Đã chặn ở cả 4 điểm + middleware chặn phiên đang hoạt động.

## 5. Ba lỗi tự phát hiện & đã sửa trong quá trình viết

| Lỗi | Cách sửa |
|---|---|
| `public game function gameServices()` — sai cú pháp | Sửa thành `public function` |
| Khối `foreach ($deleted as $table, )` placeholder sót lại | Xóa |
| View login admin kế thừa layout có sidebar → gọi `auth()->user()` khi chưa đăng nhập | Tạo `layouts/admin/auth.blade.php` riêng |

Ngoài ra đã đổi QR 2FA từ Google Chart API (gửi secret ra máy chủ Google) sang **nhập khóa thủ công + link `otpauth://`** — secret không rời khỏi server.

---

## 6. Cần bạn làm (theo thứ tự)

### Bước 1 — Backup DB (bắt buộc)
Mở HeidiSQL/phpMyAdmin → Export database `shopgame` → lưu file `.sql` ra ngoài thư mục web.

### Bước 2 — Chạy migration
Nhấp đôi **`CHAY_MIGRATE.bat`** trong thư mục dự án.
Kỳ vọng: 8 migration chạy thành công, không lỗi. Script tự backup 4 bảng trước khi migrate.

Nếu báo lỗi, gửi tôi nguyên văn thông báo — tôi sẽ sửa.

### Bước 3 — Kiểm tra nhanh
Mở `http://localhost:8080/admin/login` → đăng nhập bằng tài khoản admin có sẵn.
Vào menu **Bảo mật hai lớp (2FA)** → bật 2FA theo hướng dẫn → đăng xuất → đăng nhập lại để thử.

### Bước 4 (tùy chọn) — Reset kho tài khoản
Chạy **`CHAY_RESET_KHO.bat`** → gõ `dry-run` trước để xem số liệu → nếu đúng thì chạy lại và gõ `RESET`.
Command sẽ tự backup, hiện số dòng sẽ xóa, và yêu cầu gõ chữ `RESET` để xác nhận lần nữa.

---

## 7. Chưa làm trong Sprint này (thuộc Sprint 3–5)

- Trang Admin CRUD cho Business Units / Products / Payment Methods / Account Inventory (mới có database + model).
- Dashboard KPI mới.
- Giao diện website mới (tokens, homepage, listing, detail).
- Chuyển dữ liệu config thanh toán cũ sang bảng `payment_methods` (seeder).
- Ghi Activity Log tự động qua Observer cho mọi thao tác admin.

---

## 8. Kết quả kiểm tra

| Hạng mục | Kết quả |
|---|---|
| Cân bằng ngoặc toàn bộ file PHP | ✅ Đạt (script kiểm tự động, 0 sai) |
| Route name trong views mới khớp route đã đăng ký | ✅ Đạt (đã đối chiếu từng cái) |
| Icon FontAwesome 5 dùng đúng (admin template dùng FA5) | ✅ Đã sửa 3 chỗ dùng FA6 |
| Chạy `php -l` | ⚠️ **Chưa thực hiện** — sandbox không có PHP. Cần bạn chạy `CHAY_MIGRATE.bat` để xác nhận thực tế |
| Chạy `php artisan migrate` | ⚠️ **Chưa thực hiện** — cần MySQL trên máy bạn |
| Test 2FA end-to-end | ⚠️ **Chưa thực hiện** — chờ bạn chạy thử |

Tôi **không tuyên bố** hệ thống đã ổn định — phần chạy thực tế cần thực hiện trên máy bạn và tôi sẽ sửa ngay nếu có lỗi phát sinh.
