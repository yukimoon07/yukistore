# DEPLOYMENT.md — Hướng dẫn triển khai ShopGame lên Production

> Tài liệu này KHÔNG chứa bất kỳ thông tin đăng nhập thật nào. Toàn bộ secret được cấu hình qua `.env` và Admin Panel.

## 1. Yêu cầu môi trường

| Thành phần | Yêu cầu |
|---|---|
| PHP | 8.2+ (khuyến nghị 8.3) với extension: `pdo_mysql`, `mbstring`, `openssl`, `curl`, `gd`, `zip`, `fileinfo`, `intl` |
| Composer | 2.x |
| MySQL / MariaDB | 8.0+ / 10.6+ |
| Web server | Nginx hoặc Apache, **document root trỏ vào thư mục `public/`** |
| HTTPS | Bắt buộc (SSL cert hợp lệ) trước khi mở public |
| Node.js + npm | Chỉ cần khi muốn build asset frontend (không bắt buộc — asset đã phục vụ tĩnh) |

## 2. Các bước triển khai

### 2.1. Upload mã nguồn

Giải nén bản release (đã không chứa `.env`, `shopgame.sql`, log, dữ liệu demo) vào thư mục ví dụ `/var/www/shopgame`.

### 2.2. Cài dependency

```bash
composer install --no-dev --optimize-autoloader
# (tùy chọn) nếu muốn build asset:
npm ci && npm run build
```

### 2.3. Cấu hình môi trường

```bash
cp .env.example .env
php artisan key:generate
```

Sửa `.env` với giá trị thật:

```env
APP_NAME="ShopGame"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://ten-mien-cua-ban.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=shopgame
DB_USERNAME=<user-db-riêng>
DB_PASSWORD=<mật-khẩu-mạnh>

SESSION_SECURE_COOKIE=true
LOG_CHANNEL=daily
LOG_LEVEL=warning
```

**Lưu ý:** APP_KEY sinh ra sau bước `key:generate` là gốc mã hóa dữ liệu (mật khẩu game, 2FA secret). **Tuyệt đối không đổi APP_KEY sau khi hệ thống đã có dữ liệu** — mọi dữ liệu mã hóa sẽ không giải mã được.

### 2.4. Khởi tạo database

```bash
php artisan migrate --force
```

Không chạy `db:seed` với cờ nào khác ngoài mặc định — seeder production chỉ tạo cấu trúc khung (danh mục rỗng, cấu hình rỗng).

> Nếu đang chuyển từ database cũ có sẵn mật khẩu game dạng plain-text, chạy một lần:
> ```bash
> php artisan accounts:encrypt-passwords --dry-run   # xem trước
> php artisan accounts:encrypt-passwords            # thực hiện
> ```

### 2.5. Tạo tài khoản Admin đầu tiên

```bash
php artisan admin:create-admin
```

Nhập username/email, để trống mật khẩu để hệ thống tự sinh mật khẩu mạnh (hiển thị đúng một lần). Đăng nhập tại `/admin/login`, **đổi mật khẩu ngay và bật 2FA** tại `/admin/security/two-factor`.

### 2.6. Quyền thư mục

```bash
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

### 2.7. Storage link (ảnh upload)

```bash
php artisan storage:link
```

### 2.8. Cache production

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

Sau mỗi lần deploy mới cần chạy lại `config:clear` + `config:cache`.

### 2.9. Scheduler (nạp tiền tự động qua ngân hàng)

Thêm cron chạy mỗi phút:

```cron
* * * * * cd /var/www/shopgame && php artisan schedule:run >> /dev/null 2>&1
```

Scheduler đã đăng ký `fetch:mb-transactions` mỗi 10 phút (quét giao dịch ngân hàng MB). Cấu hình tài khoản ngân hàng trong Admin Panel.

### 2.10. Cấu hình web server

**Nginx mẫu:**

```nginx
server {
    listen 443 ssl http2;
    server_name ten-mien-cua-ban.com;

    root /var/www/shopgame/public;
    index index.php;

    client_max_body_size 20m;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known) { deny all; }
}
```

Apache: dùng `.htaccess` có sẵn trong `public/`, bật `mod_rewrite`, `AllowOverride All`.

### 2.11. Cấu hình nội dung qua Admin Panel

Đăng nhập Admin → **Cài đặt hệ thống**, nhập:

1. Tên site, mô tả, từ khóa SEO.
2. Logo, favicon, banner chính thức (upload — không dùng ảnh từ dịch vụ ngoài).
3. Email, hotline, địa chỉ, giờ làm việc, mạng xã hội.
4. OAuth Google/Facebook: tạo client mới trên Google/Facebook Console, redirect URI `https://<domain>/auth/google/callback` và `/auth/facebook/callback`. **Client OAuth cũ đã bị lộ trong mã nguồn — revoke ở Console.**
5. Cổng thanh toán (thẻ cào / bank / MoMo / USDT) — nhập partner key thật, bật từng phương thức khi sẵn sàng.
6. Thêm danh mục game, tài khoản game, dịch vụ + giá chính thức.

## 3. Backup & rollback

- **Database:** `mysqldump -u <user> -p shopgame | gzip > backup_$(date +%F).sql.gz` hàng ngày (cron).
- **File upload:** đồng bộ `storage/app/public` và `public/uploads`.
- **Rollback code:** giữ bản release trước; quay lại bằng cách đổi symlink thư mục release.
- **Rollback APP_KEY: KHÔNG ĐƯỢC** — nếu mất APP_KEY mới thì khôi phục từ backup `.env`.

## 4. Kiểm tra sau deploy

- [ ] `https://<domain>` load được, khóa ổ SSL hợp lệ.
- [ ] `/admin/login` hiển thị, đăng nhập được bằng admin vừa tạo.
- [ ] `.env` không truy cập được qua web (thử `https://<domain>/.env` → 403/404).
- [ ] `public/terminal.php` KHÔNG tồn tại.
- [ ] Đăng ký/đăng nhập khách hàng hoạt động.
- [ ] Nạp tiền test 1 giao dịch nhỏ (thẻ cào hoặc bank) và hoàn tiền/xóa sau kiểm thử.
- [ ] Mua tài khoản game test → mật khẩu giải mã hiển thị đúng cho người mua.
- [ ] Đặt dịch vụ test → trừ số dư đúng, ghi lịch sử giao dịch.
- [ ] Log lỗi ghi tại `storage/logs/` (không public).

## 5. Vấn đề đã biết / lưu ý

- `shopgame.sql` ở bản gốc KHÔNG đưa lên production (chứa dữ liệu demo + secret). Khởi tạo DB bằng migrations.
- Nếu hosting không hỗ trợ Redis/Memcached, giữ `CACHE_DRIVER=file` và `SESSION_DRIVER=file` (mặc định của `.env.example`).
- Queue dùng `sync` mặc định; nếu chuyển sang `database` cần chạy worker: `php artisan queue:work`.
