# UI/UX REDESIGN — Kế hoạch thiết kế lại giao diện

> **Phiên bản:** 1.0 — Giai đoạn A (chỉ kế hoạch, chưa sửa code)
> **Nguyên tắc nền tảng:** Bảo toàn chức năng cũ, không viết lại dự án, không tạo cảm giác "AI slop". Thiết kế phục vụ dữ liệu thật từ Backend.

---

## 1. Chẩn đoán thiết kế hiện tại

### 1.1. Frontend (user)
- Đã có nền "Minimalist Dark Slate" từ đợt nâng cấp trước nhưng **không có design token thống nhất**: mỗi blade có style inline riêng, 3 file CSS chồng nhau (`style.css` 5.851 dòng + `modern-gaming.css` 1.241 + `legacy-compat.css` 1.029), nhiều class dead.
- Icon dùng song song 4 nguồn: Iconify, FontAwesome 6, font Phosphor/Tabler/Feather/Material (admin), Ant Design CSS → không đồng bộ, nặng trang.
- Còn đậm hiệu ứng: gradient neon ở nút, glow box-shadow, glassmorphism nhiều nơi (ngược yêu cầu tối giản).
- Hero 30/70 dùng cho "Top nạp" — hợp lệ nhưng thu thập dữ liệu N+1; bố cục chưa có hierarchy rõ cho 6 nhóm kinh doanh mới.

### 1.2. Admin
- Template Berry đầy đủ nhưng nặng, nhiều font kit, không khớp ngôn ngữ thiết kế frontend, bảng dữ liệu DataTables thiếu chuẩn: không có empty state, filter thiếu, một số trang chưa phân trang server-side.

## 2. Định hướng thiết kế (Design Direction)

**Phong cách:** Minimalist Premium Marketplace — nền tối slate trung tính, một màu chủ đạo + một màu nhấn, khoảng trắng hào phóng, typography làm chủ đạo, icon ít và chỉ dùng khi tăng khả năng hiểu.

### 2.1. Design tokens (tập trung 1 file `public/css/tokens.css`)

| Token | Giá trị | Dùng cho |
|---|---|---|
| `--bg` | `#0B0F16` | Nền chính |
| `--surface` | `#111826` | Card, panel |
| `--surface-2` | `#161F30` | Card hover, row |
| `--border` | `#1F2937` | Đường viền |
| `--text` | `#E5E9F0` | Chữ chính |
| `--text-muted` | `#8A94A6` | Chữ phụ |
| `--primary` | `#2563EB` (indigo) | Nút chính, link, focus |
| `--primary-hover` | `#1D4ED8` | Hover |
| `--accent` | `#22C55E` | Trạng thái thành công, "còn hàng", giá bán |
| `--warning` / `--danger` | `#F59E0B` / `#EF4444` | Chờ xử lý / lỗi, hết hàng |
| Radius | `12px` card, `8px` nút, `6px` input | |
| Spacing scale | 4/8/12/16/24/32/48/64 | Bắt buộc dùng theo bậc |
| Font | Inter (VI/EN đủ glyph) — giảm từ 2 font Inter+Roboto xuống 1 | |
| Shadow | chỉ 1 mức `0 1px 2px rgba(0,0,0,.4)` | Bỏ glow |

Quy tắc cứng: tối đa 2 màu thương hiệu + 3 màu trạng thái; **cấm** gradient động, glow, neon; animation chỉ `transition 150ms` cho hover/focus, skeleton pulse — không parallax, không hiệu ứng chuyển trang.

### 2.2. Typography (an toàn tiếng Việt)
- Inter với subset `latin-ext` (đủ dấu tiếng Việt) qua Google Fonts `display=swap`.
- Scale: H1 28–32/semibold · H2 22/semibold · H3 18/semibold · body 15/regular · caption 13. Line-height 1.6 cho body. Không dùng chữ cách quãng (letter-spacing) với chữ hoa tiếng Việt có dấu.

## 3. Thiết kế từng khu vực — Frontend

### 3.1. Header & Navigation
- 1 hàng: logo chữ (wordmark) · menu ngang: **Trang chủ / Tài khoản / Cày thuê / Vượt link / Tools / Cloud Phone / Khác** (render **từ DB danh mục mới**, không hard-code) · ô tìm kiếm thu gọn · giỏ/nút Nạp tiền · avatar.
- Submenu danh mục con: dropdown 1 cấp tối giản (không flyout 2 cấp + thumbnail như hiện tại — giảm phức tạp).
- Mobile: bottom-safe header + drawer menu; giữ nguyên bản `menu-mobile.blade.php` nhưng viết lại theo token mới.

### 3.2. Hero
- Bỏ slider banner cảm giác "game private server"; thay bằng hero tĩnh tối giản: tiêu đề 1 dòng + 1 đoạn mô tả + 1 CTA chính + 1 CTA phụ, bên phải là khối "Trạng thái kho" (số tài khoản đang bán theo nhóm — đếm từ DB, có skeleton + empty state).
- Top nạp & banner quảng cáo (config admin) chuyển xuống section riêng dưới hero — vẫn giữ chức năng cũ, chỉ đổi vị trí/định dạng.

### 3.3. Homepage
Cấu trúc mới (mỗi section một vai trò rõ, không lặp card):
1. Hero (mục 3.2).
2. **Nhóm kinh doanh** — 6 card lớn (Accounts, Boosting, Link Monetization, Tools, Cloud Phone, Other) từ bảng danh mục cấp 1, mỗi card: icon 1 màu, tên, mô tả ngắn, số sản phẩm đang bán. Chỉ 6 — thỏa mãn "không lạm dụng card giống nhau".
3. Flash sale đang chạy (chỉ hiện khi có campaign active trong DB).
4. Sản phẩm mới nhất (grid 8, uniform nhưng có meta trạng thái tồn).
5. Dịch vụ cày thuê nổi bật (từ `game_services`).
6. Tin tức (3 bài) + Footer.

### 3.4. Category / Listing / Detail
- **Category listing:** thanh filter dọc trái (desktop) / drawer (mobile): giá, trạng thái, thuộc tính động (dùng bảng `attributes` hiện có), sắp xếp. Phân trang server-side bắt buộc.
- **Product card:** ảnh 4:3, tên 1 dòng truncate, thuộc tính chính tối đa 2 chip, giá 1 dòng, badge trạng thái (Còn / Hết). Không 3 nút bấm lặp.
- **Product detail:** layout 2 cột — ảnh gallery trái, thông tin + thuộc tính bảng + giá + nút Mua phải; dưới là mô tả dài. Ẩn tuyệt đối `account_name`/`password` đến khi mua xong (hiện trạng đã đúng, giữ nguyên logic, refactor markup).

### 3.5. Search & Filter, Cart, Modal
- Search: query server-side trên `game_accounts.account_name`, `details`, category name; debounce 300ms khi gõ; empty state có gợi ý.
- Giỏ hàng hiện không tồn tại (mua trực tiếp từ số dư) — **không phát minh giỏ hàng mới** để tránh thay đổi kiến trúc thanh toán; chỉ cải thiện modal xác nhận mua (confirm 1 bước, hiển thị số dư, giá, mã giảm).
- Modal/Popup: 1 component chuẩn (dùng lại SweetAlert2 đã có) cho: mua hàng, nạp tiền, chi tiết giao dịch.

### 3.6. Footer
- 4 cột gọn: Giới thiệu · Danh mục (DB) · Hỗ trợ (config social) · Pháp lý (Terms/Privacy hiện có). Bản quyền + cảnh báo "cần đăng nhập để mua".

## 4. Thiết kế từng khu vực — Admin Panel

Chi tiết chức năng nằm ở `ADMIN_PANEL_PLAN.md`; phần này chỉ về mặt giao diện:

- **Shell:** giữ template Berry (ổn định, có sẵn dark/light) nhưng chuẩn hóa: 1 sidebar 2 cấp theo nhóm mới (Dashboard / Kho sản phẩm / Kho tài khoản / Danh mục / Đơn hàng & Lịch sử / Thanh toán / Người dùng / Nội dung / Cài đặt / Nhật ký).
- **DataTable chuẩn 1 pattern:** toolbar (search + filter + bulk), bảng, phân trang server-side, empty state, loading skeleton; viền mỏng, không striped màu.
- **Form chuẩn 1 pattern:** label trên, helper text, validate inline, sticky footer nút Lưu; thông báo thành công/thất bại qua toast có sẵn.
- **Dashboard mới:** KPI hàng đầu (4 thẻ), biểu đồ doanh thu 30 ngày (ApexCharts area, 1 màu), bảng đơn mới nhất, hoạt động quản trị gần đây (từ Activity Log).

## 5. Responsive breakpoints

`≤480` mobile · `481–768` lớn (drawer filter, 2 cột grid) · `769–1024` tablet (sidebar thu gọn) · `≥1025` desktop. Mọi bảng admin trên mobile chuyển sang dạng stacked card.

## 6. Danh sách file UI sẽ tạo/sửa (dự kiến)

**Tạo mới:** `public/css/tokens.css`, `public/css/components.css` (btn, card, badge, table, modal, skeleton), `resources/views/components/` (blade component: card, badge, empty, pagination).
**Sửa chính:** `layouts/user/{head,header,footer,menu-mobile}.blade.php`, `user/home.blade.php`, `user/category/*`, `user/account/detail.blade.php`, `user/service/*`, `user/random/*`, `user/login|register`, `user/profile/*` (chỉnh token, giữ logic), `layouts/admin/*`, `admin/dashboard.blade.php`, toàn bộ `admin/*/index|create|edit` theo pattern mới.
**Xóa cuối kỳ (sau khi rà không còn tham chiếu):** `legacy-compat.css`, các font-kit trùng lặp trong `cmsbvq/template/frontend/fonts/` không dùng.

## 7. Rủi ro UI & cách phòng

| Rủi ro | Phòng tránh |
|---|---|
| Sửa layout làm hỏng trang đang chạy | Thay trang theo cụm (header → homepage → listing → detail), mỗi cụm chạy thử trên cả 4 breakpoint trước khi sang cụm kế |
| Mất chức năng JS cũ (tab, slider, copy) | Giữ nguyên script, chỉ thay markup + class; kiểm tra từng handler sau thay |
| Font tiếng Việt lỗi | Test bộ ký tự "ạ ế ễ ự ữ ọ ộ" trên Inter latin-ext trước khi áp |
| Admin & user lệch thiết kế | Dùng chung `tokens.css`, admin map biến sang biến Berry |
