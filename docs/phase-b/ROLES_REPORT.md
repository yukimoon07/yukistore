# Báo cáo: Hệ thống vai trò 3 cấp (member / dev / admin)

## Tổng quan
Đã triển khai hệ thống vai trò mới cho admin panel Laravel 11: **member / dev / admin**.
- **member**: không vào admin panel.
- **dev**: vào admin panel, dùng các trang quản trị dữ liệu (sản phẩm, danh mục, đơn hàng, người dùng, lịch sử, nạp/rút, vòng quay, news, mission...), nhưng bị chặn hoàn toàn ở Cài đặt hệ thống, cấp quyền, xem/đổi 2FA người khác, Nhật ký hoạt động.
- **admin**: toàn quyền, có thể cấp quyền dev cho user trong admin panel.

## File đã tạo (mới)
| # | File | Mục đích |
|---|------|----------|
| 1 | `database/migrations/2026_11_13_000009_add_dev_role_to_users_table.php` | Thêm giá trị `'dev'` vào enum `role` của bảng `users` |
| 2 | `app/Http/Middleware/SuperAdminMiddleware.php` | Chỉ cho role `admin` qua, ngược lại abort 403 |
| 3 | `app/Http/Controllers/Admin/RoleController.php` | Controller phân quyền: danh sách + assign |
| 4 | `app/Http/Controllers/Admin/ActivityLogController.php` | Controller xem Nhật ký hoạt động |
| 5 | `resources/views/admin/roles/index.blade.php` | View phân quyền (bảng admin/dev + form cấp quyền) |
| 6 | `resources/views/admin/activity-log/index.blade.php` | View Nhật ký hoạt động (bảng lọc, phân trang) |
| 7 | `database/seeders/AdminAccountSeeder.php` | Tạo tài khoản admin `yukimoon` |
| 8 | `CHAY_TAO_ADMIN.bat` | Batch chạy seeder tài khoản admin |
| 9 | `docs/phase-b/ROLES_REPORT.md` | Báo cáo này |

## File đã sửa
| # | File | Thay đổi |
|---|------|----------|
| 1 | `app/Models/User.php` | Thêm `isAdmin()`, `isDev()`, `canAccessAdminPanel()`, `isSuperAdmin()`, `canManageSettings()`, `canAssignRoles()`, `hasAnyRole()` |
| 2 | `app/Http/Middleware/AdminMiddleware.php` | Cho phép `canAccessAdminPanel()`; chưa đăng nhập → `route('admin.login')`; member đã đăng nhập → redirect home |
| 3 | `app/Http/Kernel.php` | Đăng ký alias `super-admin` cạnh `admin` |
| 4 | `routes/admin.php` | Group `settings/*` thêm `super-admin`; thêm group `roles/*` và `activity-log/*` (cả hai `super-admin`) |
| 5 | `app/Http/Controllers/Auth/AdminLoginController.php` | Dev cũng được đăng nhập admin panel (dùng `canAccessAdminPanel()`) |
| 6 | `app/Http/Controllers/Admin/UserController.php` | `update()`: role `member,dev,admin`; chặn dev sửa admin; nếu không phải admin thì giữ nguyên role (không trả lỗi). `destroy()`: dev không xóa được tài khoản admin |
| 7 | `resources/views/admin/users/edit.blade.php` | Thêm option `dev`; select chỉ hiện khi `canAssignRoles()`, nếu không hiển thị text readonly + hidden |
| 8 | `resources/views/admin/users/index.blade.php` | Hiển thị badge "Dev" riêng (trước đây dev sẽ bị hiện nhầm là Member) |
| 9 | `resources/views/layouts/admin/sidebar.blade.php` | Ẩn "Cài đặt hệ thống" nếu không phải `canManageSettings()`; thêm "Phân quyền" và "Nhật ký hoạt động" khi `canAssignRoles()` |

## Cách chạy
1. `CHAY_MIGRATE.bat` — chạy migration (bao gồm `2026_11_13_000009_add_dev_role_to_users_table.php`).
2. `CHAY_TAO_ADMIN.bat` — chạy `php artisan db:seed --class=AdminAccountSeeder --force`.

Tài khoản admin cấp cao được tạo:
- Username: `yukimoon`
- Password: `iloveher`
- Email: `yukimoon@shopgame.local`
- Role: `admin`

**Lưu ý:** Đây là tài khoản quyền cao nhất — hãy đổi mật khẩu ngay sau lần đăng nhập đầu tiên.

## Ma trận quyền
| Chức năng | Admin | Dev | Member |
|-----------|-------|-----|--------|
| Vào admin panel / thấy link admin panel | ✅ | ✅ | ❌ |
| Quản trị dữ liệu (SP, DM, đơn hàng, user, lịch sử, nạp/rút, vòng quay, news, mission) | ✅ | ✅ | ❌ |
| Cài đặt hệ thống (`admin/settings/*`, API token, email, thanh toán) | ✅ | ❌ | ❌ |
| Cấp/thu quyền (đổi role) | ✅ | ❌ | ❌ |
| Xem/đổi 2FA của người khác | ✅ | ❌ | ❌ |
| Xem Nhật ký hoạt động (Admin Activity Log) | ✅ | ❌ | ❌ |
| 2FA cho chính tài khoản mình | ✅ | ✅ (bảo mật cá nhân) | ❌ |

## Điểm rủi ro cần chủ dự án biết
1. **Migration enum trên MySQL:** Dùng `ALTER TABLE ... MODIFY role ENUM(...)`, an toàn trên MySQL 5.7+. Không làm mất dữ liệu role hiện có. Trong `down()`, các tài khoản `dev` được hạ về `member` trước khi thu enum (tránh lỗi "Data truncated" ở strict mode).
2. **Group `settings/*` bị chặt cho dev:** Toàn bộ `admin/settings/*` (bao gồm API token, email, thanh toán) hiện yêu cầu `super-admin` (chỉ admin). Dev không vào được tab Cài đặt hệ thống.
3. **2FA:** Nhóm `security/two-factor*` KHÔNG thêm `super-admin`, nên dev vẫn dùng được 2FA cho chính mình (bảo mật cá nhân). Chỉ admin mới xem/quản lý 2FA của người khác.
4. **AdminLoginController:** giờ cho cả `admin` và `dev` đăng nhập vào admin panel (trước chỉ `admin`).
5. **Routes API:** `routes/api.php` có route `/auto-bank-deposit` dùng middleware `admin` — giờ dev cũng vào được (theo định nghĩa mới `canAccessAdminPanel()`). Nếu cần chặt chặt hơn, hãy đổi sang `super-admin`.
6. **Tất cả edit được thực hiện bằng mắt (không có PHP/Composer trong sandbox), nên trước khi deploy hãy kiểm tra lại cú pháp PHP bằng `php -l` trên từng file đã sửa/tạo.**
7. Tài khoản `yukimoon` được tạo qua `updateOrCreate` theo username → chạy lại nhiều lần không lỗi trùng.
