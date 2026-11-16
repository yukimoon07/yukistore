# DATABASE AND SYNC PLAN — Kế hoạch Database & Đồng bộ Admin↔Website

> **Phiên bản:** 1.0 — Giai đoạn A (chỉ kế hoạch, chưa sửa code, chưa đụng dữ liệu)
> **Cảnh báo:** Mọi thao tác phá hủy (reset kho tài khoản) cần **xác nhận riêng** của chủ dự án ngay trước khi chạy, dù kế hoạch này đã được duyệt.

---

## PHẦN I — CÁC THAY ĐỔI DATABASE DỰ KIẾN (Giai đoạn B)

Tất cả qua **migration Laravel mới** (không sửa migration cũ), chạy bằng `php artisan migrate`, mỗi migration có `down()` hoàn tác. **Không seed dữ liệu mẫu** ngoài 1 seester chuyển đổi cấu hình thanh toán hiện có (mục 3.3 — có xác nhận).

### 1. Danh mục & nhóm kinh doanh

| Thay đổi | Chi tiết |
|---|---|
| Bảng mới `business_units` | 6 nhóm: accounts, boosting, link-monetization, tools, cloud-phone, other. Cột: `id, name, slug, icon, image, description, sort_order, is_active, timestamps`. |
| `game_categories` + cột | `business_unit_id (nullable FK)`, `sort_order int default 0` — map dữ liệu cũ về `accounts` mặc định. |
| `game_services` + cột | `business_unit_id` default `boosting`. |
| `random_categories` + cột | `business_unit_id` default `accounts`. |
| Bảng mới `products` | Cho Tools / Cloud Phone / Other: `id, business_unit_id, category_id nullable, name, slug, image, gallery JSON, description, price, stock int, is_visible, is_active, sort_order, timestamps` + `product_logs` tương tự `game_account_logs`. |
| `game_categories` | Cho phép quản lý ẩn/hiện + sắp xếp (đã có `active`, thêm `sort_order`). |

Danh mục cấp 1 (6 nhóm) không hardcode trong view — website đọc `business_units` theo `sort_order`, chỉ hiện `is_active=1`.

### 2. Account Inventory

| Thay đổi | Chi tiết |
|---|---|
| `game_accounts` + cột | `stock_status enum('available','reserved','sold','disabled') default 'available'` — migration map giá trị cũ (`available→available`, `sold→sold`, `installment→reserved`); `is_visible bool default 1`; `reserved_until datetime null`. |
| `random_category_accounts` + cột | `stock_status` tương tự (map từ enum `available|sold` cũ); `is_visible bool default 1`. |
| Bảng mới `game_account_logs` | `id, game_account_id FK, admin_id nullable FK users, action, old_values JSON, new_values JSON, created_at`. |
| Bảng mới `random_account_logs` | Tương tự. |
| Bảng mới `admin_activity_logs` | `id, admin_id, action, target_type, target_id, old_values JSON, new_values JSON, ip, user_agent, created_at`. |
| Command mới | `accounts:release-expired-reservations` — đưa `reserved` quá `reserved_until` về `available` (chạy cron). |

### 3. Thanh toán

| Thay đổi | Chi tiết |
|---|---|
| Bảng mới `payment_methods` | `id, code unique, name, logo, instructions longtext, config longtext (encrypted JSON), sort_order int, is_enabled bool, timestamps`. |
| `payment_methods.config` | **Encrypted at rest** (Laravel Crypt), chỉ server giải mã khi gọi cổng. |
| Seeder chuyển đổi (chạy 1 lần, có xác nhận) | Đọc `configs` hiện có: `payment.card.*`, `payment.bank.active`, `payment.momo.active`, `payment.usdt.*` → tạo 4 dòng `payment_methods` tương ứng, giữ nguyên trạng thái bật/tắt. **Không** tạo method mẫu ngoài 4 kênh đang có. |

### 4. Những gì **KHÔNG** thay đổi

Cấu trúc `users`, `money_transactions`, `card_deposits`, `bank_deposits`, `usdt_*`, `withdrawal_*`, `lucky_*`, `missions`, `service_histories`, `installments`, `configs` (trừ mục chuyển đổi 3.3), toàn bộ API callback hiện có. Không đổi `users.role` enum. Không xóa bảng nào.

---

## PHẦN II — RESET DỮ LIỆU KHO TÀI KHOẢN (Phần 4 đề bài)

> ⚠️ **Thao tác này KHÔNG chạy khi duyệt tài liệu.** Chỉ chạy khi chủ dự án xác nhận riêng, sau khi xem đúng số liệu dưới đây.

### 1. Số liệu thật hiện tại (từ dump `shopgame.sql` 03/10/2026 — cần chạy COUNT lại trên DB live trước khi thực hiện, số có thể khác nếu DB đã đổi)

| Bảng | Bản ghi | Sẽ ảnh hưởng |
|---|---|---|
| `game_accounts` | 1 (status available) | Xóa hết → 0. **Ảnh hưởng FK từ `installments.game_account_id` và `purchase_history.game_account_id`** (2 bảng này hiện 0 bản ghi → an toàn) |
| `game_categories` | 2 (Liên Quân 100k, Free Fire VIP) | Xóa hết → 0 (cùng ảnh hưởng FK từ `game_accounts` — đã 0 sau bước trên) |
| `random_category_accounts` | 1.679 (toàn bộ `available`) | Xóa hết → 0 |
| `random_categories` | 1 | Xóa hết → 0 |
| `game_groups` | 2 (Liên Quân, Free Fire) | **Đề xuất: giữ lại** (chỉ là nhóm game, không phải kho) — chờ chủ dự án quyết |
| `attributes` | 0 | Không ảnh hưởng |
| `purchase_history`, `installments` | 0 | Không ảnh hưởng |

**Bảng KHÔNG bị đụng đến:** `users` (chưa có bản ghi trong dump nhưng tuyệt đối không DELETE/TRUNCATE), `money_transactions` (4), `configs` (6), `card_deposits`, `bank_deposits`, `usdt_*`, `withdrawal_*`, `lucky_*`, `news`, `notifications`, `languages`, `discount_codes`, `migrations`, toàn bộ bảng khác.

### 2. Quy trình 6 bước (thực hiện khi có xác nhận)

1. **Kiểm tra lại live:** chạy `SELECT COUNT(*)` từng bảng trên DB thật, in ra bảng so sánh với số liệu trên. Nếu lệch → dừng, báo cáo lại.
2. **Backup:** `mysqldump` riêng các bảng bị ảnh hưởng + full dump → lưu `storage/backups/reset-YYYYMMDD-HHMM/…sql`. Xác nhận file backup đọc mở được trước bước 3.
3. **Transaction:** trong 1 transaction: `DELETE FROM game_accounts; DELETE FROM game_categories; DELETE FROM random_category_accounts; DELETE FROM random_categories;` — FK `ON DELETE CASCADE` sẽ dọn dữ liệu phụ. Không dùng `TRUNCATE` (vượt FK). Đếm số dòng bị xóa, ghi vào log.
4. **Reset auto-increment** về 1 cho 4 bảng (ALTER TABLE).
5. **Ghi Activity Log** thao tác reset (ai, lúc nào, xóa bao nhiêu dòng/bảng).
6. **Báo cáo kết quả** cho chủ dự án: số dòng xóa từng bảng, link file backup.

### 3. Cam kết sau reset

- Không tự tạo lại dữ liệu mẫu/khôi phục từ dump cũ.
- Website hiển thị trạng thái trống đúng (empty state "Chưa có sản phẩm").
- Chỉ hiện tài khoản Admin thêm mới và lưu thành công qua Admin Panel.
- Nếu cần phục hồi: chỉ dùng file backup bước 2, do chủ dự án yêu cầu.

---

## PHẦN III — CƠ CHẾ ĐỒNG BỘ ADMIN ↔ WEBSITE

### 1. Nguyên tắc (giữ kiến trúc hiện tại)

Website và Admin đã dùng chung MySQL và Eloquent — **một nguồn dữ liệu duy nhất**. Hiện trạng đã đúng: Admin lưu xong, request tiếp theo của website thấy dữ liệu mới. Việc nâng cấp chỉ siết để không còn cache cũ và không còn dữ liệu 2 bản.

### 2. Các điểm phải siết

| Vấn đề hiện có | Giải pháp |
|---|---|
| `config_get()` cache 24h (file cache) — Admin đổi cấu hình thanh toán nhưng website có thể thấy cũ | `config_set` đã `Cache::put` lại — kiểm tra mọi path ghi đều qua `config_set`; thêm `Cache::forget` cho nhóm key liên quan khi lưu Payment Method |
| Cache danh sách (nếu thêm cache trang chủ) | Key cache có version `v{max(updated_at)}` của bảng liên quan; hoặc invalidate đúng key khi Observer bắn sự kiện |
| Ghi trực tiếp `DB::table()` rải rác (mua hàng, purchase) | Giữ nguyên (đã có transaction + lock) nhưng chuyển dần vào Model + Observer để log tự động |
| Double-write | Không có — chỉ 1 DB. Cấm dùng localStorage làm nguồn dữ liệu (theo đề bài) |

### 3. Luồng chuẩn sau nâng cấp (đã đúng, tài liệu hóa để kiểm thử)

```
Admin POST /admin/accounts → FormRequest validate → DB::transaction → Model save
→ Observer ghi game_account_logs + admin_activity_logs → Cache::forget(các key liên quan)
→ Redirect + toast thành công
→ Website (request kế tiếp) query DB → thấy dữ liệu mới ngay
```

### 4. API bổ sung (cho UI danh sách live, không bắt buộc thay kiến trúc)

| Endpoint | Method | Mục đích |
|---|---|---|
| `/admin/api/accounts/stats` | GET | Đếm tồn theo trạng thái cho trang Inventory (có auth+admin) |
| `/admin/api/payment-methods/reorder` | POST | Sắp xếp kéo thả |
| `/api/payment-methods` | GET | Website lấy danh sách method `is_enabled=1 order by sort_order` (public, không lộ config) |

Mọi endpoint mới: middleware phù hợp, validate, trả JSON chuẩn `{success, message, data}`, có error handler + throttle.

### 5. Xử lý lỗi & trạng thái (đề bài Phần 7)

- Frontend validate song song Backend (FormRequest là chuẩn chốt); hiển thị lỗi từng field.
- Mọi action Admin có loading state (disable nút + spinner), toast thành công/thất bại, không tự reload cả trang trừ khi cần.
- Race condition: giữ `lockForUpdate` luồng mua; bổ sung unique constraint/`firstOrCreate` nơi phù hợp; sắp xếp payment methods dùng `sort_order` int, cập nhật theo batch trong 1 transaction.
- Log lỗi: `Log::error` hiện có giữ nguyên; không log dữ liệu nhạy cảm (password, token, số thẻ).

---

## PHẦN IV — BẢO TOÀN DỮ LIỆU (checklist trước từng bước Giai đoạn B)

1. Trước migration đầu tiên: `mysqldump` toàn bộ DB → lưu ngoài public.
2. Mỗi migration phải có `down()`; chạy thử trên bản sao DB dump trong sandbox trước khi hướng dẫn chạy trên máy chủ.
3. Không seed ngoài danh mục 6 nhóm kinh doanh (rỗng dữ liệu sản phẩm) + 4 payment method từ config thật.
4. Sau mỗi cụm migration: đối chiếu `COUNT(*)` các bảng chính trước/sau.
5. Chỉ chạy migration trên máy chủ khi chủ dự án xác nhận (vì DB chứa dữ liệu kinh doanh thật).
