# BẢN THIẾT KẾ & ĐỊNH HƯỚNG NÂNG CẤP UI/UX (REDESIGN PLAN)
**Dự án:** Hệ Thống Thương Mại Điện Tử Shop Game (Tài Khoản, Cày Thuê & Nhiệm Vụ Kiếm Tiền)  
**Triết lý thiết kế:** Minimalist • Modern • Premium • Dark Slate Cyber Identity  
**Trọng tâm:** Tinh tế, gọn gàng, tăng tỷ lệ chuyển đổi (CRO), chuẩn SEO, hoàn toàn loại bỏ "AI Slop".

---

## 1. ĐỊNH HƯỚNG THẨM MỸ CỐT LÕI (DESIGN PILLARS)

### 1.1. Phong cách "Deep Obsidian & Frosted Slate"
Thay vì sử dụng giao diện màu đen đặc (`#000000`) thô ráp hoặc đỏ rực kiểu web game truyền thống, hệ thống thiết kế mới áp dụng gam màu **Deep Obsidian** (`#0b0f19`) kết hợp các bề mặt thẻ **Frosted Slate** (`#131d31`) và viền kính mờ tinh xảo (`rgba(255, 255, 255, 0.08)`).
* **Cảm giác mang lại:** Sang trọng, hiện đại, uy tín, gợi nhớ đến các nền tảng phân phối game hàng đầu thế giới (Steam, Epic Games Store, Razer Synapse).
* **Không "AI Slop":** Không lạm dụng hiệu ứng neon chói lóa, không gradient 7 màu lòe loẹt, không icon chớp nháy gây mỏi mắt. Mọi thành phần đều có mục đích rõ ràng và khoảng thở (whitespace/padding) cân xứng.

### 1.2. Phân cấp thị giác rõ ràng (Visual Hierarchy)
* **Vị trí cấp 1 (Primary Action):** Nút CTA Mua Ngay, Thuê Ngay, Nạp Tiền sử dụng màu sắc nhấn mạnh có chủ đích, độ nổi khối vừa phải.
* **Vị trí cấp 2 (Secondary Info):** Giá tiền, tên game, mã ID tài khoản hiển thị sắc nét bằng font số Tabular/Monospace rõ ràng.
* **Vị trí cấp 3 (Meta Data):** Trạng thái tồn kho, số lượng đã bán, thời gian giao dịch hiển thị bằng tông màu Slate xám dịu, không tranh chấp sự chú ý của khách hàng.

---

## 2. HỆ THỐNG DESIGN TOKENS (HỆ MÀU & THÔNG SỐ CHUẨN)

### 2.1. Bảng màu chuẩn (Color Palette)
```css
:root {
    /* Background Surfaces */
    --bg-base: #0b0f19;          /* Nền chính sâu thẳm, dịu mắt */
    --bg-surface: #111827;       /* Nền khu vực section, header */
    --bg-card: #151f32;          /* Nền thẻ sản phẩm, panel */
    --bg-card-hover: #1c2a44;    /* Nền thẻ khi di chuột */
    --bg-input: #0e1627;         /* Nền ô nhập liệu search & filter */

    /* Borders & Glassmorphism */
    --border-subtle: rgba(255, 255, 255, 0.08); /* Viền mờ thanh lịch */
    --border-card: #1e2c45;      /* Viền card chuẩn */
    --border-card-hover: #3b82f6;/* Viền card khi tương tác */
    --border-glow: rgba(59, 130, 246, 0.25);   /* Hào quang xanh nhẹ */

    /* Typography Colors */
    --text-primary: #f8fafc;     /* Chữ trắng tinh khiết cho tiêu đề */
    --text-secondary: #cbd5e1;   /* Chữ xám sáng cho nội dung chính */
    --text-muted: #64748b;       /* Chữ xám tối cho thông tin phụ */
    --text-highlight: #38bdf8;   /* Chữ điểm nhấn công nghệ */

    /* Accent & Brand Colors */
    --primary-blue: #2563eb;     /* Xanh hoàng gia (Primary CTA) */
    --accent-cyan: #06b6d4;      /* Xanh ngọc Cyan (Dịch vụ cày thuê) */
    --accent-amber: #f59e0b;     /* Vàng hổ phách (Giá tiền, Túi mù, VIP) */
    --accent-crimson: #ef4444;   /* Đỏ Ruby (Hot deal, Nạp tiền khẩn cấp) */
    --accent-emerald: #10b981;   /* Xanh ngọc lục bảo (Thành công, Còn hàng) */

    /* Shadows & Elevations */
    --shadow-card: 0 4px 16px rgba(0, 0, 0, 0.35);
    --shadow-card-hover: 0 12px 32px rgba(0, 0, 0, 0.5), 0 0 20px rgba(59, 130, 246, 0.15);
    --shadow-modal: 0 25px 60px -15px rgba(0, 0, 0, 0.7);

    /* Rounded Corners */
    --radius-sm: 6px;
    --radius-md: 10px;
    --radius-lg: 14px;
    --radius-xl: 20px;
    --radius-pill: 9999px;
}
```

### 2.2. Hệ thống Typography chuẩn quốc tế
* **Font Family chủ đạo:** `'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;`
  * Khả năng hiển thị tiếng Việt hoàn hảo không lỗi dấu (không bị chân co chân duỗi).
  * Độ nét cao trên màn hình Retina / High-DPI.
* **Modular Typography Scale:**
  * **H1 / Hero Title:** 32px - 38px | Font-weight: 800 | Letter-spacing: -0.03em
  * **H2 / Section Title:** 22px - 26px | Font-weight: 700 | Letter-spacing: -0.02em
  * **H3 / Card Title:** 15px - 17px | Font-weight: 600 | Line-height: 1.4
  * **Body / Content:** 14px - 15px | Font-weight: 400 | Line-height: 1.6
  * **Small / Metadata:** 12px - 13px | Font-weight: 500 | Letter-spacing: 0.01em
  * **Price / Number:** Font Tabular số học đều đặn, hiển thị rõ ràng ký hiệu `đ` hoặc `VND`.

---

## 3. THIẾT KẾ CHI TIẾT CÁC PHÂN ĐOÀN GIAO DIỆN

### 3.1. Header & Navigation (Thanh điều hướng đỉnh cao)
* **Kiểu dáng:** Fixed Top, chiều cao chuẩn mực 64px, chất liệu kính mờ (`backdrop-filter: blur(20px)`), nền `rgba(11, 15, 25, 0.85)` đồng nhất tuyệt đối với thân trang.
* **Tối ưu thanh menu:**
  * Logo căn trái với kích thước tỉ lệ vàng, có fallback SVG nếu ảnh lỗi.
  * Cụm Menu chính tối ưu thành 6 mục then chốt: **Trang Chủ**, **Kho Tài Khoản** (Dropdown), **Cày Thuê Game**, **Nhiệm Vụ Kiếm Tiền**, **Tin Tức**, **Lịch Sử**.
  * Cụm Tiện ích bên phải: Nút tìm kiếm nhanh, Bộ chuyển đổi ngôn ngữ Song ngữ VI/EN (Dropdown tùy chỉnh không dùng Google iframe), Nút "Nạp Tiền" và Nút "Đăng Nhập / Profile".
* **Giao diện Mobile:** Drawer Offcanvas trượt mượt từ bên phải sang với backdrop mờ, các liên kết có touch area rộng rãi 48px.

### 3.2. Hero Section: Tỷ lệ 30% - 70% Tinh Chỉnh
* **Cột trái (30% - Top Nạp Tiền & Phần Thưởng):**
  * Tái thiết kế bảng xếp hạng dạng Thẻ Danh Dự (Honor Board) với viền mỏng 1px tinh tế.
  * Top 1 nhận huy hiệu Vàng Hoàng Gia (`#f59e0b`), Top 2 nhận Bạc Ánh Kim (`#94a3b8`), Top 3 nhận Đồng Cổ Điển (`#b45309`).
  * Tab chuyển đổi giữa "Top Nạp" và "Phần Thưởng Tháng" hoạt động mượt mà không giật cục.
* **Cột phải (70% - Banner Cinematic Slider):**
  * Bo góc cong 16px chuẩn hiện đại, tỷ lệ khung hình 16:9 cinematic.
  * Hiệu ứng chuyển slide cross-fade mượt mà trong 0.5s.
  * Nút chuyển slide ẩn mờ và chỉ hiện rõ khi người dùng rê chuột vào vùng banner.

### 3.3. Spotlight Dịch Vụ Nổi Bật (3 Thẻ Tiêu Biểu)
* **Bố cục:** Grid 3 cột cân đối tuyệt đối trên Desktop, 1 cột thẻ ngang trên Mobile.
* **3 Thẻ dịch vụ đại diện:**
  1. *Cày Thuê Blox Fruits* (Kèm banner HD Kitsune, tag "Từ 7.000 đ", nút "Thuê Ngay").
  2. *Cày Thuê Genshin Impact* (Kèm banner Furina, tag "Từ 3.000 đ", nút "Thuê Ngay").
  3. *Túi Mù Liên Quân 50K* (Kèm banner Triệu Vân, tag "50.000 đ", nút "Mở Túi Ngay").
* **Hiệu ứng:** Card nâng nhẹ 4px khi hover, viền phát sáng nhẹ theo màu chủ đạo của từng game.

### 3.4. Danh Mục Tài Khoản Game (Product Cards & Grid)
* **Bố cục Grid:** 5 cột trên Desktop rộng (>1200px), 4 cột trên Laptop (992px - 1199px), 3 cột trên Tablet (768px - 991px), 2 cột trên Smartphone (<767px).
* **Cấu trúc Thẻ Sản Phẩm (Product Card):**
  * **Hình ảnh (16:10 ratio):** Ảnh bìa HD, bo góc trên 10px, overlay nhẹ khi hover để làm nổi bật nút "Xem Chi Tiết".
  * **Huy hiệu giá (Price Tag):** Đặt góc trên bên phải ảnh, nền kính tối bóng mờ, số tiền màu hổ phách sắc nét.
  * **Tiêu đề & Thuộc tính:** Tiêu đề in hoa chữ cái đầu gọn gàng (Line-clamp: 1 hàng để tránh lệch hàng).
  * **Dòng thông số tóm tắt (Spec Badges):** Hiển thị dạng viên thuốc nhỏ (Pill badge) cho số lượng "Còn: X" và "Đã bán: Y".
  * **Nút hành động:** Nút "Mua Ngay" hoặc "Xem Chi Tiết" toàn chiều ngang thẻ, phản hồi xúc giác ngay lập tức khi nhấn.

### 3.5. Trang Chi Tiết Tài Khoản (`account/detail.blade.php`)
* **Thư viện ảnh bên trái:**
  * Thumbnail dọc xếp ngăn nắp, ảnh chính to rõ sắc nét với nút phóng to toàn màn hình (Lightbox).
* **Cột mua hàng bên phải:**
  * Tiêu đề tài khoản kèm mã số ID độc bản (ví dụ `#LQ204918`).
  * Box bảng giá: Hiển thị giá ATM ưu đãi to nổi bật, gạch ngang giá thẻ cào tham chiếu, tính toán tự động % tiết kiệm.
  * Bảng thông số tài khoản (Rank, Tướng, Trang phục, Trạng thái liên kết): Thiết kế dạng ô lưới 2 cột nền mờ sang trọng.
  * Khối CTA mua hàng:
    * Nút chính: **MUA NGAY BẰNG SỐ DƯ** (Full width, màu đỏ ruby hoặc xanh sapphire chủ đạo).
    * Nút phụ: **MUA BẰNG ATM / MOMO** và **TRẢ GÓP TỰ ĐỘNG** chia tỷ lệ 50:50 bên dưới, màu sắc nhã nhặn hài hòa.

### 3.6. Trang Chi Tiết Dịch Vụ Cày Thuê (`service/show.blade.php`)
* **Khối chọn gói dịch vụ:**
  * Thiết kế lại danh sách gói cày thành dạng thẻ chọn có checkbox tích xanh hiện đại.
  * Cột bên phải là **Sticky Summary Card (Bản tóm tắt đơn cày bám dính khi cuộn)**: Người dùng chọn đến đâu, tổng tiền và danh sách gói cập nhật tức thì đến đó mà không cần kéo chuột tìm kiếm.

### 3.7. Trang Làm Nhiệm Vụ Kiếm Tiền (`mission/index.blade.php`)
* **Gamified Timeline:** 3 bước vượt link được trực quan hóa thành quy trình bước 1 (Bấm nhận) -> bước 2 (Vượt link lấy mã xác nhận) -> bước 3 (Nhập mã nhận tiền thưởng).
* **Thẻ nhiệm vụ:** Card hiển thị rõ số tiền nhận được (ví dụ `+5.000 đ`), thời gian ước tính, nút CTA "Bắt Đầu Nhiệm Vụ" rực rỡ, hấp dẫn.

### 3.8. Footer & Mobile Bottom Bar
* **Footer:** Nền tối Deep Obsidian (`#080c14`), chia 4 cột cân đối, đường kẻ phân cách mảnh dẻ `1px solid rgba(255,255,255,0.06)`, liên kết hover chuyển màu xanh sáng, đầy đủ thông tin bản quyền và chứng nhận an toàn.
* **Mobile Bottom Bar:** Cao 58px, nền kính mờ đen sang trọng, icon căn giữa kèm nhãn chữ 10px, điểm báo trạng thái kích hoạt (Active dot indicator) màu xanh ngọc.

---

## 4. CHIẾN LƯỢC ANIMATION & MICRO-INTERACTIONS

| Thành phần | Hiệu ứng (Transition / Animation) | Thời gian & Easing | Mục đích UX |
| :--- | :--- | :--- | :--- |
| **Card Hover** | `transform: translateY(-4px);` `box-shadow` mở rộng | 0.25s `cubic-bezier(0.16, 1, 0.3, 1)` | Tạo cảm giác thẻ nổi lên đón nhận tương tác |
| **Button Click** | `transform: scale(0.97);` | 0.12s `ease-out` | Phản hồi xúc giác tức thời (Tactile feedback) |
| **Modal Open** | `opacity: 0 -> 1;` `transform: scale(0.96) -> scale(1);` | 0.2s `cubic-bezier(0.16, 1, 0.3, 1)` | Xuất hiện êm dịu, không giật màn hình |
| **Image Loading** | Shimmer skeleton gradient quét ngang | 1.5s `infinite linear` | Giảm thời gian chờ đợi nhận thức của khách hàng |
| **Toast Alert** | Trượt nhẹ từ góc phải trên màn hình | 0.3s `cubic-bezier(0.16, 1, 0.3, 1)` | Thông báo trạng thái không cản trở thao tác |

> [!IMPORTANT]
> **Hỗ trợ người dùng nhạy cảm chuyển động (Reduced Motion):**
> Toàn bộ animation phải được bọc trong `@media (prefers-reduced-motion: reduce)` để tắt bỏ `transform` và chuyển sang `opacity` thuần túy, đảm bảo chuẩn tiếp cận Web Accessibility (A11y).

---

## 5. TỐI ƯU HÓA SEO & TRẢI NGHIỆM TẢI TRANG

1. **Heading Tag Structure (Cấu trúc H1-H6):**
   * Mỗi trang chỉ có DUY NHẤT một thẻ `<h1>` chứa từ khóa mục tiêu (ví dụ trên trang chủ: `Hệ Thống Mua Bán Nick & Dịch Vụ Cày Thuê Game Uy Tín`).
   * Các danh mục game dùng `<h2>`, tên tài khoản và gói dịch vụ dùng `<h3>`.
2. **Hình ảnh & Core Web Vitals:**
   * Mọi thẻ `<img>` đều có thuộc tính `loading="lazy"`, `decoding="async"` và kích thước `width`, `height` rõ ràng để loại bỏ hoàn toàn Cumulative Layout Shift (CLS = 0).
   * Dự phòng `onerror` xử lý lỗi fallback ảnh sang placeholder SVG vector nội bộ, không phụ thuộc vào dịch vụ bên ngoài.
3. **Structured Data JSON-LD:**
   * Giữ nguyên và hoàn thiện các schema `WebSite`, `Organization`, `Service`, `Product` và `BreadcrumbList` đã có để Google Index hiển thị Rich Snippets (sao đánh giá, mức giá, tình trạng còn hàng).