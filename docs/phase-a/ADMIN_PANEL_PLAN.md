# ADMIN PANEL PLAN — Kế hoạch nâng cấp hệ thống quản trị

> **Phiên bản:** 1.0 — Giai đoạn A (chỉ kế hoạch, chưa sửa code)
> **Nguyên tắc:** Mọi dữ liệu lấy từ Backend/Database thật. Không số liệu hardcode, không chức năng "chỉ trên giao diện". Phân quyền kiểm tra ở Backend.

---

## 1. Trạng thái hiện tại (tóm tắt từ PROJECT_AUDIT)

- `routes/admin.php` + middleware `['auth','admin']` (kiểm tra `role === 'admin'` phía Backend) — giữ nguyên cơ chế.
- 24 Admin Controllers, 79 views, template Berry, dashboard ApexCharts.
- Thiếu: Activity Log, trang đăng nhập riêng, dashboard KPI đúng, Account Inventory chuyên biệt, Payment Method Manager, 2FA.

## 2. Kiến trúc mục tiêu

```
admin/
├── Dashboard          → KPI + biểu đồ + hoạt động gần đây (từ DB)
├── Kho sản phẩm        → Accounts / Boosting(services) / Tools&Khác (products)
├── Account Inventory   → kho tài khoản game chi tiết (mục 4)
├── Danh mục            → Category tree 2 cấp + 6 nhóm kinh doanh (mục 5)
├── Đơn hàng & Lịch sử   → 7 trang history hiện có + đơn tổng hợp
├── Thanh toán          → Payment Methods (mục 6) + config kênh hiện có
├── Người dùng          → users, affiliates (giữ nguyên, làm mới UI)
├── Nội dung            → news, notifications, flash sales, missions, wheels
├── Cài đặt             → general/social/email/login/terms (giữ nguyên)
└── Nhật ký             → Activity Log (mục 7)
```

Route mới đặt trong `routes/admin.php` (không đổi prefix/middleware), controller mới trong `app/Http/Controllers/Admin/`, dùng Form Request classes cho validate (hiện validate rải rác trong controller).

## 3. Dashboard

**Dữ liệu — truy vấn thật, không hardcode:**

| KPI | Nguồn |
|---|---|
| Tổng sản phẩm | `game_accounts` + `random_categories` (đếm nhóm) + `game_services` + sản phẩm Tools/Other (bảng mới) |
| Tổng tài khoản game | `GameAccount::count()` |
| Còn hàng / Đã bán | `where('status','available')` / `('sold')` — **bỏ** query status `locked`/`pending` không tồn tại trong enum |
| Đơn hàng (tổng + đang xử lý) | `purchase_history` + `service_histories (pending|processing)` + `random purchases` (qua `money_transactions`) |
| Doanh thu | `money_transactions where type='purchase' amount<0` theo ngày/tháng; không trừ bừa `LuckyWheelHistory` như hiện tại |
| Thống kê theo danh mục | group by `game_category_id` (+ nhóm kinh doanh mới qua relation) |
| Hoạt động gần đây | 10 dòng cuối bảng `admin_activity_logs` (mục 7) |

**Hiệu năng:** gom các truy vấn lặp 7 ngày hiện tại thành 1 query `groupBy(date)`; cache kết quả dashboard 60s (`Cache::remember`) và xóa cache khi có giao dịch mới.

## 4. Quản lý sản phẩm (thống nhất 3 hệ hiện có thành 1 giao diện quản lý)

**Hiện trạng:** 3 hệ riêng (game_accounts, random_categories+accounts, game_services+packages). Kế hoạch **không gộp bảng** (tránh phá dữ liệu & API cũ) mà thống nhất ở tầng quản lý:

- Trang **Sản phẩm** dạng tổng hợp có filter "Loại: Tài khoản / Random / Dịch vụ / Tool / Khác", mỗi loại vẫn CRUD qua controller hiện có.
- **Tài khoản chính (`game_accounts`):** giữ CRUD hiện có, bổ sung: trạng thái hiển thị/ẩn riêng khỏi trạng thái bán (`is_visible`), quick-edit giá + tồn, export CSV.
- **Tài khoản random:** giữ nguyên controller; bổ sung nhập số lượng nhiều bản ghi 1 lần (mục 4.1).
- **Dịch vụ cày thuê:** giữ nguyên; thêm trạng thái `active` toggle nhanh.
- **Tool / Khác:** bảng `products` mới (mục 5, danh mục cấp 2 Tools & Other trỏ vào) — CRUD đầy đủ, không dùng localStorage.

**Đồng bộ về frontend (bắt buộc theo đề bài):** mọi thao tác ghi qua Backend + `DB::transaction`; frontend đọc trực tiếp DB mỗi request (Laravel Blade hiện trạng đã đúng mô hình này) — **không cần cơ chế push riêng**; thêm `Cache::forget` cho các key cache danh sách để thay đổi hiển thị ngay. Trạng thái loading/toast lỗi có sẵn (SweetAlert + fui-toast) — chuẩn hóa message.

### 4.1. Nhập nhiều tài khoản cùng lúc
- Textarea paste dạng `username|password|giá|server|ghi chú` mỗi dòng 1 bản → preview → lưu hàng loạt trong 1 transaction; báo cáo số dòng thành công/lỗi.
- Import CSV: cột chuẩn hóa, validate từng dòng, bỏ dòng lỗi, hiển thị danh sách lỗi.

## 5. Account Inventory (ưu tiên cao nhất)

### 5.1. Bổ sung vào bảng `game_accounts` (migration, không phá dữ liệu)

| Cột mới | Kiểu | Ý nghĩa |
|---|---|---|
| `stock_status` | enum `available, reserved, sold, disabled` default `available` | Trạng thái 4 mức theo yêu cầu. Migration map: `available→available`, `sold→sold`, `installment→reserved` |
| `is_visible` | bool default true | Ẩn/hiện trên website |
| `reserved_until` | datetime null | Hạn giữ khi Reserved (tự nhả về available khi hết hạn — command lịch) |

`random_category_accounts` thêm `stock_status` tương tự (enum hiện chỉ `available|sold` → thêm `reserved, disabled`).

### 5.2. Chức năng trang Inventory
- Bảng: ảnh, tên, game (category → group), giá, **trạng thái 4 màu**, người mua, cập nhật lúc; filter theo trạng thái/game/khoảng giá/từ khóa; search cả `details` JSON.
- **Lịch sử thay đổi:** bảng mới `game_account_logs` (`game_account_id, admin_id, action, old_values JSON, new_values JSON, created_at`) — ghi tự động qua Model Observer (`created, updated, deleted`) cho cả giá, trạng thái, thông tin đăng nhập (log **không** lưu password plain — chỉ ghi "đã đổi mật khẩu").
- **Tài khoản đã bán:** tab riêng lọc `stock_status=sold`, xem buyer + thời gian; không cho sửa account_name/password sau khi sold (chỉ admin riêng mới mở khóa qua log).
- **Bảo mật hiển thị:** cột password **mask** trong danh sách (`•••••`), chỉ xem chi tiết từng bản qua nút có kiểm tra quyền admin (đã có) + ghi log xem.

## 6. Quản lý phương thức thanh toán (Payment Methods)

Chi tiết nghiệp vụ trong `PAYMENT_CONFIGURATION_PLAN.md` — phần admin gồm:
- Bảng `payment_methods` CRUD: tên, logo, hướng dẫn (rich text), thứ tự `sort_order`, bật/tắt `is_enabled`, cấu hình JSON `config` (server-side only, không render ra frontend).
- Trang admin: danh sách kéo-thả sắp xếp, modal thêm/sửa; API key/secret hiển thị mask, chỉ ghi mới.
- Kênh hiện có (thẻ cào, ATM, USDT, MoMo flag trong config) sẽ được **di chuyển dữ liệu** vào `payment_methods` qua seeder một lần (không mất cấu hình cũ), route nạp tiền cũ giữ nguyên URL để không hỏng callback.

## 7. Admin Activity Log

- Bảng `admin_activity_logs`: `id, admin_id, action, target_type, target_id, old_values JSON, new_values JSON, ip, user_agent, created_at`.
- Ghi qua `LogAdminAction` middleware/service cho các sự kiện: **đăng nhập admin, đăng xuất, thêm/sửa/xóa sản phẩm, đổi giá, thay đổi kho tài khoản (thêm/sửa/xóa/import/reset), thay đổi danh mục, thay đổi phương thức thanh toán, duyệt/từ chối nạp-rút, đổi quyền user**.
- Trang xem: filter theo admin/action/thời gian, export CSV; không lưu mật khẩu/API key trong log (mask).

## 8. Đăng nhập & bảo mật admin

Chi tiết kỹ thuật trong `SECURITY_PLAN.md`. Tóm tắt phạm vi admin:
- Route đăng nhập riêng `/admin/login` (view riêng, vẫn dùng guard `web`, middleware `guest` + throttle riêng 5/phút), sau đăng nhập đúng role `admin` mới vào dashboard; user thường đăng nhập vào `/admin/login` → từ chối, không lộ thông tin.
- Tất cả API quản trị (route POST/PUT/DELETE admin) đã nằm sau middleware `auth+admin` — rà soát bổ sung: các route `/api/*` hiện có thêm middleware phù hợp, đặc biệt `/api/auto-bank-deposit`.
- 2FA TOTP (đề xuất, cần phê duyệt riêng trước khi làm): `pragmarx/google2fa-laravel`, bắt buộc bật với role admin, backup codes 1 lần.

## 9. UX Admin (giao diện)

Theo `UI_UX_REDESIGN.md` mục 4: DataTable pattern, Form pattern, sidebar nhóm mới, dashboard KPI, empty/loading state, bảng → card trên mobile.

## 10. Kiểm thử giai đoạn B (admin)

1. User thường truy cập `/admin*` → redirect home + flash lỗi (đã đúng, test lại).
2. Mọi POST/PUT/DELETE admin thiếu CSRF → 419; thiếu quyền → redirect.
3. Thêm/sửa/ẩn sản phẩm → website phản ánh ngay (test cả khi cache bật).
4. Inventory đổi trạng thái 4 mức → frontend chỉ hiện `available` + `is_visible=true`.
5. Import 100 tài khoản giả lập → đếm đúng trong DB, log ghi đủ.
6. Bật/tắt payment method → website hiện/ẩn tương ứng.
7. Activity log có đúng event, không chứa password/token.
