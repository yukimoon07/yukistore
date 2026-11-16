# IMPLEMENTATION ROADMAP — Lộ trình triển khai Giai đoạn B

> **Phiên bản:** 1.0 — Giai đoạn A (chỉ kế hoạch, chưa sửa code)
> **Điều kiện vào Giai đoạn B:** Chủ dự án phê duyệt bộ 7 tài liệu này.
> **Điều kiện riêng:** Reset kho tài khoản (Sprint 2) cần xác nhận riêng, KHÔNG tự động thực hiện dù roadmap được duyệt.

---

## Nguyên tắc vận hành

- Triển khai theo Sprint; mỗi Sprint kết thúc bằng: báo cáo file thay đổi → chạy kiểm tra (build/lint/test + test thủ công liệt kê) → báo cáo kết quả → chờ xác nhận nếu bước kế có rủi ro dữ liệu.
- Trước mọi thao tác DB: backup `mysqldump`, chạy migration trên bản sao dump trong sandbox trước.
- Không gỡ chức năng cũ, không đổi URL/callback đang chạy, không seed dữ liệu giả.

## Bảng Sprint

### Sprint 0 — Chuẩn bị (0.5 ngày)
1. `composer install` + `npm install` (nếu cần build), kiểm chạy `php artisan migrate:status` trên bản sao DB.
2. Backup toàn bộ DB + thư mục `storage/` uploads → `storage/backups/`.
3. Khởi tạo nhánh làm việc (nếu dùng git) hoặc thư mục bản sao `shopgame-backup-pre-phaseB/`.
4. Kiểm tra môi trường: PHP 8.3, MySQL, `.env` (giữ local cho dev).

**Sản phẩm:** môi trường sạch, có backup khôi phục được.

### Sprint 1 — Nền tảng bảo mật & Database (1–2 ngày)
1. Migration: `business_units`, `products`, cột danh mục/inventory, `game_account_logs`, `random_account_logs`, `admin_activity_logs`, `payment_methods` (chi tiết `DATABASE_AND_SYNC_PLAN.md` Phần I).
2. Migration + map dữ liệu `stock_status`, `is_visible`, `reserved_until`.
3. `/api/auto-bank-deposit` chốt auth; throttle callback; `SecurityHeaders`; `UploadHelper` MIME check.
4. Middleware `LogAdminActivity` gắn vào admin write-routes; Observer ghi log cho GameAccount/RandomCategoryAccount/Product.
5. `/admin/login` riêng + throttle; test 10 mục bảo mật trong `SECURITY_PLAN.md`.

**Sản phẩm:** nền móng dữ liệu mới + bảo mật cơ bản, không thay đổi gì trên website user. **Kiểm tra:** migrate up/down sạch trên DB sao chép, toàn bộ luồng cũ hoạt động bình thường.

### Sprint 2 — Reset kho tài khoản (0.5 ngày) ⚠️ CẦN XÁC NHẬN RIÊNG
Chỉ thực hiện theo quy trình 6 bước trong `DATABASE_AND_SYNC_PLAN.md` Phần II khi chủ dự án xác nhận bằng lời ngay trước bước chạy:
1. COUNT lại live so bảng số liệu → 2. Backup bảng ảnh hưởng → 3. Transaction DELETE → 4. Reset AUTO_INCREMENT → 5. Activity log → 6. Báo cáo.

**Sản phẩm:** kho rỗng, các bảng khác nguyên vẹn, file backup kèm theo.

### Sprint 3 — Admin Panel lõi (2–3 ngày)
1. Dashboard KPI mới (truy vấn đúng, bỏ số liệu ảo, gom query 7 ngày, cache 60s).
2. Account Inventory: trang chính với 4 trạng thái, filter, search, import hàng loạt, lịch sử thay đổi, mask password, tab đã bán.
3. CRUD `products` (Tools/Cloud Phone/Other) + quản lý `business_units` (thêm/sửa/ảnh/mô tả/sắp xếp/ẩn/xóa khi trống).
4. Quản lý danh mục hiện có: thêm `sort_order`, toggle ẩn/hiện, xóa có kiểm ràng buộc.
5. Payment Methods CRUD + reorder + seeder chuyển đổi (chạy sau xác nhận).

**Sản phẩm:** Admin quản trị trọn bộ theo đề bài Phần 3, 5, 6. **Kiểm tra:** checklist 15 luồng trong đề bài Phần 10.

### Sprint 4 — Website Frontend (2–3 ngày)
1. `tokens.css` + `components.css` + blade components.
2. Header/menu (đọc `business_units` từ DB) → Hero → Homepage mới.
3. Listing + filter + product card + product detail + search.
4. Modal mua hàng + nạp tiền (đọc payment_methods động) + các trang dịch vụ/profile refresh token.
5. Empty/loading/error state toàn trang; responsive 4 breakpoint; rà font tiếng Việt.

**Sản phẩm:** website mới hoàn chỉnh trên dữ liệu thật (lúc này kho còn trống → empty states phải đẹp). **Kiểm tra:** responsive + mỗi luồng cũ (mua/nạp/rút/wheel/mission) hoạt động bình thường trên UI mới.

### Sprint 5 — Đồng bộ, hiệu năng, hoàn thiện (1–2 ngày)
1. Cache invalidation: Observer `Cache::forget` toàn bộ key liên quan; kiểm luồng "admin thêm → website thấy ngay".
2. Tối ưu query N+1 trang chủ/dashboard (eager load), phân trang server-side listing.
3. Skeleton + error state cho mọi fetch; xử lý race khi sửa đồng thời (updated_at check form).
4. Gỡ CSS dead (`legacy-compat.css`), gộp icon lib còn dùng.
5. Viết seed chỉ gồm 6 business units (rỗng sản phẩm) cho môi trường mới.

### Sprint 6 — Kiểm thử & bàn giao (1 ngày)
1. Chạy trọn checklist Phần 10 đề bài (15 luồng) — ghi kết quả từng mục.
2. Test bảo mật (mục 4 SECURITY_PLAN), API test, responsive test.
3. 2FA admin (nếu đã phê duyệt) — cài + hướng dẫn bật.
4. Viết `FINAL_IMPLEMENTATION_REPORT.md` theo khung đề bài Phần 12.
5. Bàn giao: hướng dẫn chạy (CHAY_WEB.bat), tài khoản admin, danh sách file thay đổi.

## Ma trận rủi ro tổng hợp

| Rủi ro | Khả năng | Ảnh hưởng | Giảm thiểu |
|---|---|---|---|
| Migration phá dữ liệu | Thấp | Cao | Backup + test trên bản sao + down() |
| Reset kho xóa nhầm bảng khác | Thấp | Cao | Chỉ DELETE đúng 4 bảng, transaction, COUNT trước/sau, xác nhận riêng |
| Luồng mua hàng lỗi sau nâng cấp | Trung bình | Cao | Không đổi controller purchase trừ khi bắt buộc; test sau mỗi Sprint |
| Callback thanh toán bị hỏng | Thấp | Cao | Không đổi URL/sign; throttle thêm; test sandbox |
| Cache cũ hiển thị sai | Trung bình | Trung bình | Invalidate qua Observer; test với cache bật |
| UI mới lỗi responsive | Trung bình | Thấp | Test từng cụm trang trên 4 breakpoint |
| Thiếu người kiểm tra nội dung thật | Trung bình | Trung bình | Chuẩn bị sẵn bộ tài khoản mẫu của chủ dự án để nhập sau reset |

## Tiêu chí hoàn thành (Definition of Done)

1. 15 luồng kiểm thử đề bài Phần 10 đạt hết.
2. Build/lint/test không lỗi; không còn route công khai nguy hiểm.
3. Admin thay đổi gì → website phản ánh ngay, không số liệu giả.
4. Kho tài khoản đúng trạng thái sau reset (nếu chủ dự án yêu cầu chạy).
5. Không có password/token trong log; upload chặn file thực thi.
6. Tài liệu `FINAL_IMPLEMENTATION_REPORT.md` đầy đủ.
