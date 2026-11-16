# PAYMENT CONFIGURATION PLAN — Kế hoạch hệ thống thanh toán có thể tùy chỉnh

> **Phiên bản:** 1.0 — Giai đoạn A (chỉ kế hoạch, chưa sửa code)
> **Giới hạn nghiệp vụ (theo đề bài):** Giai đoạn này chỉ xây **hệ quản lý cấu hình phương thức thanh toán + giao diện lựa chọn**. KHÔNG tích hợp cổng thanh toán mới, KHÔNG giả lập giao dịch thành công. Các kênh hiện có hoạt động nguyên trạng.

---

## 1. Hiện trạng

**4 kênh nạp tiền đang chạy (không đụng luồng xử lý tiền):**

| Kênh | Cơ chế hiện tại | Nơi cấu hình |
|---|---|---|
| Thẻ cào | Callback từ partner `khothegame.com`, sign md5, cộng `users.balance` tự động | `configs`: `payment.card.*` (partner_id, partner_key, discount %) |
| ATM / Chuyển khoản | User khai báo giao dịch → admin duyệt `bank_deposits`; QR VietQR sinh client-side từ `img.vietqr.io` + dữ liệu `bank_accounts` | CRUD `bank_accounts` + flag `payment.bank.active` |
| USDT | Tạo `usdt_deposits` chờ, command `CheckUsdtDeposits` poll Spay5s tự cộng tiền | `usdt_accounts` + flag `payment.usdt.active`, rate, wallet |
| MoMo | Chỉ có flag bật/tắt `payment.momo.active` (hiển thị), chưa có xử lý thực | `configs` |

**Vấn đề với yêu cầu mới:** phương thức "hard-code" theo key config rời rạc — không thêm/xóa/đổi tên/logo/hướng dẫn/second từ admin được; frontend điều kiện hiển thị rải rác trong từng blade.

## 2. Mô hình dữ liệu mục tiêu

Bảng mới `payment_methods`:

```
id            PK
code          varchar unique   — 'card', 'bank', 'usdt', 'momo', ... (khóa logic)
name          varchar          — tên hiển thị ("Chuyển khoản ngân hàng", "Thẻ cào"...)
logo          varchar nullable — ảnh do admin upload
instructions  longtext nullable— hướng dẫn thanh toán (rich text, an toàn qua safe_html)
config        longtext         — JSON MÃ HÓA (Crypt), chỉ server đọc: credentials, rate...
sort_order    int default 0    — thứ tự hiển thị
is_enabled    bool default 0   — bật/tắt
timestamps
```

**Nguyên tắc dữ liệu:**
- `config` chứa thông tin nhạy cảm (partner_key, wallet, token) — mã hóa at rest, **không bao giờ** gửi ra frontend; frontend chỉ nhận: id, code, name, logo, instructions, sort_order.
- Không xóa method đang có giao dịch pending: khi xóa → chỉ cho xóa method không có `config` kênh active, còn lại đề xuất "disable" thay vì xóa (bảo toàn lịch sử).

## 3. Chuyển đổi từ cấu hình hiện tại (1 lần, có xác nhận)

Seeder `PaymentMethodFromConfigSeeder` (chạy sau migration, **phải được chủ dự án xác nhận**):
1. Đọc `configs`: `payment.card.active` → tạo dòng `card` (name "Nạp thẻ cào", enabled theo giá trị cũ, config = partner_id/key/discount từ config cũ); `bank` → "Chuyển khoản ngân hàng"; `usdt` → "USDT (TRC20)" với rate + wallet; `momo` → "Ví MoMo" (mặc định disabled nếu chưa có xử lý).
2. `sort_order` theo thứ tự hiện tại trên website.
3. Giữ nguyên các key config cũ (không xóa) để code cũ chạy song song an toàn; sau khi luồng mới ổn định mới ngưng đọc key cũ (bước sau, có xác nhận).

## 4. Admin Panel — CRUD Payment Methods

- Trang `admin/payment-methods` (route prefix `admin/payment-methods`, middleware `auth+admin`):
  - Danh sách: drag-drop sắp xếp (lưu `sort_order` batch 1 transaction qua endpoint `/admin/api/payment-methods/reorder`), công tắc bật/tắt, nút sửa/xóa.
  - Form thêm/sửa: tên, logo (upload qua `UploadHelper`, ảnh vuông 1:1 khuyến nghị), hướng dẫn (editor text đơn giản), trường config động theo `code` (ví dụ chọn `usdt` hiện thêm input rate + wallet address), bật/tắt.
  - Xóa: confirm; chặn xóa method có giao dịch chưa xử lý.
- Mọi thao tác ghi Activity Log (đăng trong SECURITY/ADMIN plan).
- Hiển thị secret mask trong form; khi để trống thì giữ nguyên giá trị cũ.

## 5. Website frontend

- Modal "Nạp tiền" hiện có (`depositMethodModal`) và 3 trang deposit giữ nguyên URL, nhưng:
  - Danh sách phương thức hiển thị đọc từ `payment_methods` (`is_enabled=1 ORDER BY sort_order`) qua view-share/composer hoặc query trực tiếp trong `ProfileController@deposit*` — không hardcode.
  - Method disabled/không tồn tại → không render; truy cập URL trực tiếp của method tắt → trang thông báo "Phương thức này đang được bảo trì".
  - Trang nạp ATM: phần sinh QR VietQR client-side **giữ nguyên** (dữ liệu bank + tên chủ tk từ DB, không phải secret), chỉ refactor UI.
- Xử lý lỗi: nếu query lỗi/empty → empty state, không chết trang.

## 6. Tích hợp cổng mới (ngoài phạm vi hiện tại)

Khi muốn thêm cổng thực (VNPay/MoMo business/Casso…):
1. Chủ dự án phê duyệt cổng cụ thể + tài khoản đối tác.
2. Tạo `PaymentGateway` interface + class riêng cho cổng (Service layer, không viết trong controller).
3. Callback route mới có signature verify riêng, throttle, log IP.
4. Không dùng chung secret giữa các cổng; secret nằm trong `payment_methods.config` mã hóa.

## 7. Kiểm thử

1. Admin tạo method mới + bật → website hiện đúng vị trí sort_order, không reload tổng.
2. Tắt method → ẩn khỏi modal nạp tiền + trang deposit tương ứng chặn truy cập.
3. 4 kênh cũ sau chuyển đổi: test nạp thẻ sandbox sign đúng → tiền cộng; tạo bank_deposit → admin duyệt → tiền cộng; USDT poll vẫn chạy.
4. `GET /api/payment-methods` không trả `config` (kiểm tra payload).
5. Không thể truy cập admin payment-methods với tài khoản member (403/redirect).
6. Xóa method có pending transaction → bị chặn với thông báo.
