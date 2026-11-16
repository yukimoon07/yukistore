# SECURITY PLAN — Kế hoạch bảo mật

> **Phiên bản:** 1.0 — Giai đoạn A (chỉ kế hoạch, chưa sửa code)
> **Nguyên tắc:** Nâng bảo mật mà không phá luồng nghiệp vụ hiện có (đặc biệt callback thanh toán), sửa theo mức ưu tiên rủi ro.

---

## 1. Đánh giá hiện trạng (từ PROJECT_AUDIT mục 7)

**Đã đạt:** phân quyền backend (`AdminMiddleware`), bcrypt, rate limit login, CSRF toàn cục, Eloquent (chống SQLi), session regenerate, upload validate đuôi file.

**Lỗ hổng / thiếu sót xếp theo mức rủi ro:**

| # | Vấn đề | Mức | Vị trí |
|---|---|---|---|
| 1 | `/api/auto-bank-deposit` công khai, không auth — ai cũng có thể kích hoạt lệnh quét giao dịch ngân hàng | **Cao** | `routes/api.php` |
| 2 | Không có Admin Activity Log | **Cao** | toàn hệ thống |
| 3 | Chưa có trang đăng nhập admin riêng + throttle riêng | Trung bình | routes/auth.php |
| 4 | Upload kiểm tra mimes (đuôi file) nhưng không kiểm MIME content, không giới hạn kích thước rõ ràng | Trung bình | `UploadHelper`, form validate |
| 5 | `{!! !!}` render HTML do admin nhập (12 chỗ views user) — chấp nhận được vì tin admin, nhưng cần chuẩn hóa whitelist tags | Trung bình | views user |
| 6 | Cột mật khẩu tài khoản game lưu plain (yêu cầu nghiệp vụ — phải giao cho buyer) — hiển thị trong purchased-accounts. Cần: mask trong admin danh sách, log khi xem | Trung bình | admin accounts index |
| 7 | Không 2FA admin | Trung bình | — |
| 8 | `config_get` trả về giá trị 민감 (API token) — admin view hiển thị đủ | Thấp | settings views |
| 9 | Không có security headers (CSP, X-Frame-Options…) | Thấp | middleware |
| 10 | `APP_DEBUG=true`, `APP_ENV=local` trong .env — phải đổi khi lên máy chủ thật | Thấp (cấu hình) | .env |

## 2. Biện pháp triển khai (Giai đoạn B)

### 2.1. Ưu tiên cao — làm đầu tiên

1. **Chốt `/api/auto-bank-deposit`:** chuyển thành Artisan command + cron (đã có sẵn `FetchMBTransactions`), đồng thời bọc route bằng `auth+admin` + `throttle:3,1` để chạy tay khi cần. Callback thẻ cào `/api/callback/card` **giữ nguyên** (partner cần gọi công khai, đã có sign md5 + kiểm tra partner_key — chỉ thêm throttle `10,1` + ghi log IP callback).
2. **Admin Activity Log** (đặc tả trong `ADMIN_PANEL_PLAN.md` mục 7): bảng + middleware `LogAdminActivity` áp cho toàn bộ route POST/PUT/DELETE của admin; đăng nhập/đăng xuất ghi qua event listener.
3. **Không cho user tự nâng quyền:** đã an toàn (register không nhận `role`); bổ sung test tự động + thêm `Rule::in` chặt ở `UserController@update` (hiện đã có `in:member,admin` — chỉ admin mới gọi được route này nên đạt; thêm test đảm bảo).

### 2.2. Đăng nhập admin

- Route `GET/POST /admin/login` (tên `admin.login`), view riêng theo design admin; middleware `guest`; sau đăng nhập thành công kiểm `role` — nếu không phải admin: đăng xuất + thông báo chung chung "Thông tin đăng nhập không đúng" (không tiết lộ tồn tại tài khoản).
- Throttle riêng `throttle:5,1` theo IP+username (Laravel RateLimiter — giữ `ensureIsNotRateLimited` của Breeze cho login thường).
- Session admin: giữ file driver; bật `password_confirm` (RequirePassword) cho các thao tác nhạy cảm (reset kho, đổi quyền).

### 2.3. 2FA cho Admin (đề xuất — cần phê duyệt trước khi cài)

- Gói `pragmarx/google2fa-laravel` (nhẹ, phổ biến, thuần PHP — không đụng nhiều).
- Luồng: trang Profile admin "Bật 2FA" → QR (Google Authenticator) → xác nhận 1 mã → lưu secret (encrypted) + 8 backup code (hashed, hiện 1 lần) → từ lần đăng nhập kế, sau bước mật khẩu nhập thêm OTP 6 số (30s window ±1).
- Bắt buộc với role admin sau khi bật tính năng; user member không bị ảnh hưởng.

### 2.4. Mật khẩu & hash

- Giữ bcrypt (đạt yêu cầu "Argon2id hoặc bcrypt"); nâng `HASH_DRIVER=argon2id` là tùy chọn không bắt buộc — bcrypt hiện tại đủ an toàn, đổi driver không ảnh hưởng mật khẩu cũ (Laravel verify tự nhận driver).
- Chính sách mật khẩu admin: tối thiểu 10 ký tự; chống dùng mật khẩu đã lộ (`Password::uncompromised()` của Laravel).

### 2.5. Upload & XSS

- `UploadHelper`: kiểm MIME thực bằng `finfo`/`getMimeType()`, giới hạn 5MB/ảnh, đổi tên ngẫu nhiên (đã có), chặn exe/svg (SVG chứa script — hiện chưa cho phép, giữ nguyên).
- Chuẩn hóa render rich-text admin: chỉ cho phép các tag an toàn qua helper `safe_html()` (whitelist: p, br, strong, em, ul, li, a[href http(s)], img) — áp cho mô tả danh mục/sản phẩm/hướng dẫn thanh toán.
- Escape mặc định mọi biến trong blade (giữ `{{ }}`); liệt kê + rà 12 chỗ `{!! !!}` hiện có, chuyển các chỗ không cần thiết sang `{{ }}`.

### 2.6. Bảo vệ dữ liệu nhạy cảm

- `payment_methods.config` mã hóa bằng `Crypt::encryptString`, chỉ server giải mã; không bao giờ trả config trong JSON công khai `/api/payment-methods`.
- Trang admin settings: hiển thị API key/secret dạng mask (`••••1234`), chỉ ghi đè khi nhập mới; không đổ lại giá trị cũ vào input password.
- Log: cấm ghi password, secret, OTP vào `admin_activity_logs`/`game_account_logs` (Observer mask trước khi ghi).
- `game_accounts.password`: thêm accessor `masked_password` dùng cho danh sách admin; xem plain phải bấm nút riêng (POST, có log).

### 2.7. Security headers & cấu hình

- Middleware thêm: `X-Content-Type-Options: nosniff`, `X-Frame-Options: SAMEORIGIN`, `Referrer-Policy: strict-origin-when-cross-origin`, Permissions-Policy tối thiểu. CSP ban đầu `report-only` để không phá CDN hiện có, sau đó siết.
- Checklist khi lên production: `APP_ENV=production`, `APP_DEBUG=false`, HTTPS, `SESSION_SECURE_COOKIE=true`, đổi `APP_KEY` riêng.

## 3. Bảng phân công file sửa (bảo mật)

| File | Thay đổi |
|---|---|
| `routes/api.php` | Throttle + auth cho auto-bank-deposit; throttle callback |
| `app/Http/Middleware/LogAdminActivity.php` | Mới |
| `app/Http/Middleware/SecurityHeaders.php` | Mới |
| `app/Http/Controllers/Auth/` | Thêm controller/method cho `/admin/login` + 2FA (sau duyệt) |
| `app/Helpers/UploadHelper.php` | MIME check + size limit |
| `app/helpers.php` | `safe_html()` |
| Views settings | Mask secret |
| `app/Observers/*` | Log + mask dữ liệu nhạy cảm |

## 4. Kiểm thử bảo mật (trước khi báo hoàn thành)

1. Tự nâng quyền: POST sửa user với `role=admin` từ tài khoản member → bị chặn (route + policy).
2. Truy cập `/admin*` không đăng nhập → redirect login; đăng nhập member → không vào được dashboard.
3. Brute force: sai mật khẩu 6 lần liên tiếp trong 1 phút → 429.
4. CSRF: POST không kèm token → 419.
5. SQLi: thử `' OR 1=1 --` ở search/listing → không lỗi, không leak.
6. XSS: lưu `<script>alert(1)</script>` vào tên sản phẩm → render dạng text.
7. Upload: file `.php` đổi đuôi `.jpg` → bị chặn bởi MIME check.
8. `/api/auto-bank-deposit` không đăng nhập → 401/403.
9. Log không chứa mật khẩu/token (grep sau khi thao tác).
10. Callback thẻ cào với sign sai → từ chối.
