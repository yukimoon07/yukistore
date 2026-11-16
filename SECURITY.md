# SECURITY.md — Bảo mật ShopGame

## 1. Tổng quan mô hình bảo mật

| Lớp | Cơ chế |
|---|---|
| Phiên | Laravel session file, cookie `HttpOnly`, `SESSION_SECURE_COOKIE=true` (HTTPS only), SameSite=Lax |
| CSRF | Token `@csrf` trên mọi form POST + middleware `VerifyCsrfToken` |
| Mật khẩu | Bcrypt (Laravel Hash) cho tài khoản hệ thống; **Laravel Crypt (AES-256)** cho mật khẩu game game |
| 2FA | TOTP RFC 6238 tự triển khai (PHP thuần), secret mã hóa, recovery code lưu bcrypt hash |
| Phân quyền | 3 vai trò: `member` / `dev` / `admin`; middleware `admin`, `super-admin`, `not-banned` |
| SQL Injection | 100% Eloquent/Query Builder với parameter binding |
| XSS | `{{ }}` mặc định escape; nội dung HTML Admin qua `App\Support\HtmlSanitizer` (allowlist) |
| Rate limit | `throttle` trên callback API, đăng nhập (RateLimiter), API công khai |
| Bí mật | Không hardcode secret trong mã nguồn — qua `.env` hoặc Admin Panel |

## 2. Dữ liệu nhạy cảm và cách xử lý

### Mật khẩu tài khoản game
- Lưu mã hóa AES-256 bằng `APP_KEY`.
- Chỉ người mua mới xem được mật khẩu tài khoản đã mua; Admin xem qua trang quản trị.
- Không ghi mật khẩu vào log (`SecuritySanitizer::mask` mask các key nhạy cảm khi ghi AdminActivityLog).
- Backfill từ dữ liệu cũ: `php artisan accounts:encrypt-passwords` (idempotent).

### APP_KEY
- Là gốc của mọi mã hóa. **Mất APP_KEY = mất toàn bộ mật khẩu game mã hóa + 2FA secret.**
- Không bao giờ commit `.env` vào git; không gửi APP_KEY qua chat/email.

### API token bên thứ ba
- `thegioidev_api_token`, `dichvudark_api_token`, partner key thanh toán: cấu hình trong Admin Panel, lưu trong bảng `configs`.
- Khi ghi log thay đổi cấu hình, giá trị bị mask.

## 3. Các lỗ hổng đã được xử lý (nhật ký)

1. **`public/terminal.php`** — cho phép chạy `artisan` qua HTTP công khai (RCE) → đã xóa khỏi dự án.
2. **SSL verification tắt** trong `TheGioiDevService` và `DichVuDarkService` (`SSL_VERIFYPEER=false`) → bật `true`/`2`.
3. **Admin mặc định** (`admin123456`, `yukimoon/iloveher`) trong seeders → xóa; thay bằng `admin:create-admin` (mật khẩu tự sinh/ẩn nhập).
4. **Secret OAuth hardcode** trong `ConfigSeeder` (Google/Facebook client secret) → coi như đã lộ; seeder mới xóa giá trị; **cần revoke + tạo client mới**.
5. **Đặt dịch vụ không kiểm tra quan hệ package-service** → đã chặn: package phải thuộc service và đang active.
6. **Double-spend** khi gửi nhiều request cùng lúc (mua acc / đặt dịch vụ / rút tiền) → `lockForUpdate()` trong transaction cho mọi luồng trừ tiền.
7. **Lỗi kỹ thuật trả ra client** (`$e->getMessage()`, curl error, raw response) → log server-side, trả message thân thiện.
8. **Đặt dịch vụ không cần đăng nhập** → route yêu cầu `auth` + `not-banned`.
9. **XSS qua nội dung Admin** (`{!! !!}` chưa lọc) → `HtmlSanitizer` allowlist ở mọi điểm render config/news/mission/page.
10. **User bị ban vẫn dùng phiên cũ** → middleware `EnsureUserNotBanned`.
11. **`/api/auto-bank-deposit` công khai** → yêu cầu `auth` + `admin` + throttle.

## 4. Checklist bảo mật khi vận hành

- [ ] `APP_DEBUG=false`, `APP_ENV=production`.
- [ ] HTTPS bật, `SESSION_SECURE_COOKIE=true`.
- [ ] Đã đổi mật khẩu admin + bật 2FA.
- [ ] OAuth client cũ đã revoke, client mới đã cấu hình.
- [ ] Toàn bộ API key bên thứ ba nhập qua Admin Panel (không nằm trong code/git).
- [ ] Cron backup database hằng ngày; kiểm tra khôi phục định kỳ.
- [ ] `storage/logs` không public; log được rotate (LOG_CHANNEL=daily).
- [ ] Giới hạn truy cập `/admin` theo IP nếu có thể (tầng firewall).
- [ ] Theo dõi `admin_activity_logs` định kỳ.
- [ ] Khi có sự cố: thu hồi session (`php artisan cache:clear`, đổi APP_KEY nếu nghi ngờ lộ), rotate secret liên quan.

## 5. Quy tắc phát triển

- Không thêm `{!! !!}` mới cho dữ liệu người dùng; nếu cần HTML Admin thì qua `HtmlSanitizer::clean()`.
- Mọi luồng trừ tiền PHẢI nằm trong `DB::transaction` + `lockForUpdate` bản ghi user.
- Không trả chi tiết exception cho client; dùng `Log::error` + message thân thiện.
- Không thêm route POST công khai không có throttle/CSRF.
- Không lưu secret vào file code, log, hay seed data.
