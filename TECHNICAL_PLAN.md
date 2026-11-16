# KẾ HOẠCH TRIỂN KHAI KỸ THUẬT FRONTEND (TECHNICAL PLAN)
**Dự án:** Hệ Thống Thương Mại Điện Tử Shop Game  
**Kiến trúc:** Laravel Blade + Modular Modern CSS + Vanilla ES6  
**Nguyên tắc cốt lõi:** Bảo toàn 100% Backend/Database/APIs • Tối ưu hiệu năng • Không phát sinh phụ thuộc mới.

---

## 1. PHƯƠNG PHÁP & KIẾN TRÚC TRIỂN KHAI

### 1.1. Kiến trúc phân tầng Frontend (CSS Layering Strategy)
Để chấm dứt tình trạng xung đột CSS giữa Ant Design, Bootstrap cũ và các style tùy biến rải rác, chúng ta áp dụng chiến lược phân tầng CSS phân cấp rõ ràng theo thứ tự tải:
1. **Tầng 1 (Vendor Foundation):** Ant Design 4.24 CSS (nhúng CDN phục vụ lưới và các thành phần cốt lõi).
2. **Tầng 2 (Normalize & Base Reset):** Reset các giá trị mặc định của trình duyệt, triệt tiêu viền trắng và thanh cuộn ngang ngoài ý muốn (`overflow-x: hidden`).
3. **Tầng 3 (Design Tokens & Utility Classes):** Định nghĩa toàn bộ CSS Variables (`:root`) trong `public/css/modern-gaming.css`.
4. **Tầng 4 (Component Modules):** Các khối giao diện chuẩn hóa (Navbar, Hero, Spotlight, Gaming Card, Product Detail, Pricing Box, Mission Timeline, Footer).
5. **Tầng 5 (Scoped Overrides):** Ghi đè có chọn lọc các component của Ant Design mà không cần sử dụng `!important` tràn lan, dựa trên CSS Selector Specificity tự nhiên.

### 1.2. Chiến lược Tối ưu hóa JavaScript
* **Không cài đặt thêm thư viện JS:** Tuyệt đối không cài thêm các gói npm nặng như Lodash, MomentJS hay React/Vue runtime.
* **Tận dụng Vanilla ES6 & jQuery có sẵn:**
  * Xử lý tương tác Tabs, Modals, Drawer Menu, Accordions bằng Vanilla JS thuần nhẹ nhàng (~15KB gzipped).
  * Giữ nguyên các hàm AJAX hiện hành dùng jQuery (`$.ajax`, `$.post`) phục vụ mua tài khoản, tính giá cày thuê và nạp thẻ để đảm bảo tính ổn định tuyệt đối của logic nghiệp vụ.

---

## 2. DANH SÁCH TỆP DỰ KIẾN CAN THIỆP

Quá trình nâng cấp giao diện CHỈ can thiệp vào các tệp View (Blade Templates) và Static Assets (CSS/JS) của tầng giao diện người dùng (User Side). Không can thiệp vào tầng Admin, Backend Controllers hay Database Migrations.

### 2.1. Tệp Stylesheet & Script (Static Assets)
| Đường dẫn tệp | Trách nhiệm xử lý | Phạm vi can thiệp |
| :--- | :--- | :--- |
| `public/css/modern-gaming.css` | **Stylesheet trung tâm** của toàn bộ giao diện mới | Hợp nhất và chuẩn hóa toàn bộ Design Tokens, bố cục Grid, Hero, Cards, Details, Animations |
| `public/js/app.js` | **Script tương tác UI** chính | Tối ưu logic Slider, Tabs, Drawer, Language Switcher, loại bỏ các đoạn code thừa |
| `public/css/legacy-compat.css` | Tệp tương thích cũ | Dọn dẹp các rule xung đột với giao diện Dark Slate mới |

### 2.2. Tệp Layouts dùng chung (`resources/views/layouts/user/`)
| Đường dẫn tệp | Chức năng hiện tại | Kế hoạch nâng cấp |
| :--- | :--- | :--- |
| `head.blade.php` | Thẻ meta, CDN fonts, CSS imports | Loại bỏ script Google Translate bên thứ ba; nạp bộ font `Inter` chuẩn; dọn dẹp CSS inline |
| `header.blade.php` | Thanh điều hướng đỉnh trang (Navbar) | Cấu trúc lại giao diện kính mờ Dark Slate (cao 64px), tích hợp bộ đổi ngôn ngữ VI/EN mới, menu dropdown tinh giản |
| `menu-mobile.blade.php` | Menu trượt cho điện thoại | Đồng bộ giao diện Dark Slate, tăng kích thước touch target, hiệu ứng trượt êm ái |
| `footer.blade.php` | Chân trang & Mobile Bottom Bar | Chuyển sang nền Deep Obsidian thanh lịch, chuẩn hóa 4 cột, tối ưu Bottom Bar cho smartphone |
| `app.blade.php` | Khung Layout bao quanh nội dung | Đồng bộ màu nền nền tảng, căn lề và cấu trúc container 1240px |

### 2.3. Tệp Giao diện các trang nghiệp vụ (`resources/views/user/`)
| Đường dẫn tệp | Nghiệp vụ hiển thị | Kế hoạch nâng cấp |
| :--- | :--- | :--- |
| `home.blade.php` | **Trang chủ** | Cân đối Hero Split 30-70, làm đẹp Spotlight 3 dịch vụ, tối ưu thẻ danh mục game 5 cột, làm mới bảng vinh danh và đánh giá khách hàng |
| `category/show.blade.php` | Danh sách tài khoản trong danh mục | Thiết kế lại thanh Filter Bar (tìm kiếm, lọc giá, trạng thái), lưới tài khoản chuẩn E-commerce, Empty State sang trọng |
| `category/show-group.blade.php` | Xem nhóm game (Liên Quân, Blox Fruits...) | Đồng bộ tiêu đề nhóm, danh sách chuyên mục con |
| `account/detail.blade.php` | **Chi tiết tài khoản & Mua hàng** | Bố cục E-commerce 2 cột hiện đại: Thư viện ảnh tương tác Lightbox bên trái, Sticky Card thông số & nút Mua Ngay tập trung thị giác bên phải |
| `service/show.blade.php` | **Chi tiết cày thuê & Đặt cọc** | Danh sách gói cày dạng card có checkbox hiện đại, tích hợp Floating Sticky Calculator tổng tiền thời gian thực |
| `mission/index.blade.php` | **Nhiệm vụ vượt link kiếm tiền** | Minh họa quy trình 3 bước trực quan (Stepper), thẻ nhiệm vụ nổi bật số tiền thưởng, nút nhận nhiệm vụ bắt mắt |
| `mission/giftcode.blade.php` | Trang đổi mã nhận thưởng | Form nhập mã giftcode gọn gàng, hiệu ứng xác nhận thành công sang trọng |

---

## 3. CÁC COMPONENT TÁI SỬ DỤNG (REUSABLE BLADE COMPONENTS)

1. **Component `<x-hero-header>`:**
   * Tái sử dụng cho đầu các trang chuyên mục, dịch vụ và tin tức; cập nhật giao diện thành banner kính mờ tinh tế kèm Breadcrumb chuẩn SEO.
2. **Component Phân trang `user.pagination.custom`:**
   * Tái cấu trúc nút số trang theo style pill/square bo góc nhỏ, hỗ trợ rê chuột mượt mà, phân trang rõ ràng khi có nhiều trang.
3. **Thư viện Lightbox xem ảnh:**
   * Tái sử dụng `SimpleLightbox` trong trang chi tiết tài khoản, chỉ tùy biến lại màu viền và nền mờ đen sâu cho đồng bộ phong cách.
4. **Hệ thống Alert & Toasts:**
   * Tái sử dụng Fui Toast và SweetAlert2 hiện có; cập nhật bảng mã màu Dark Slate để hộp thoại cảnh báo hiện lên hòa hợp với tông màu website.

---

## 4. MA TRẬN RỦI RO KỸ THUẬT & PHƯƠNG ÁN XỬ LÝ (RISK MATRIX)

| # | Rủi ro tiềm ẩn | Mức độ | Nguyên nhân kỹ thuật | Phương án xử lý phòng ngừa |
| :--- | :--- | :---: | :--- | :--- |
| **1** | **Xung đột CSS Ant Design** | Trung bình | Ant Design CDN có nhiều class toàn cục (`.ant-btn`, `.ant-input`) ghi đè lên thẻ native | Dùng CSS specificity phân cấp rõ ràng (ví dụ: `.modern-app .btn-primary` hoặc class chuyên biệt `.btn-card-action`), tránh dùng selector generic |
| **2** | **Lỗi Blade Cache trên Windows** | Cao | Lỗi `Maximum execution time exceeded in Filesystem.php:86` do Windows lock file khi biên dịch Blade | Thực hiện lệnh `php artisan view:clear` và `php artisan cache:clear` trước và sau khi hoàn thiện từng phân đoạn; không mở ghi đồng thời |
| **3** | **Vỡ giao diện trên Mobile nhỏ (<375px)** | Trung bình | Grid 5 cột hoặc flexbox bị cố định chiều rộng (fixed width) | Áp dụng triệt để `minmax()` trong CSS Grid (`repeat(auto-fill, minmax(160px, 1fr))`) và `flex-wrap: wrap` linh hoạt |
| **4** | **Lỗi JavaScript Console làm ngưng trệ mua hàng** | Nghiêm trọng | Tham chiếu `document.getElementById` trúng phần tử không tồn tại trên trang khác | Luôn kiểm tra sự tồn tại của phần tử trước khi gán sự kiện (ví dụ `const el = document.getElementById('xyz'); if (el) { ... }`) |
| **5** | **Ảnh banner/thẻ bị méo tỷ lệ** | Thấp | Ảnh upload từ admin có nhiều kích thước khác nhau | Sử dụng thuộc tính chuẩn `object-fit: cover` và `aspect-ratio` cố định (16/9 cho banner, 16/10 cho thẻ game) |

---

## 5. NGUYÊN TẮC KIỂM THỬ AN TOÀN (QUALITY ASSURANCE)

Mỗi thay đổi sau khi được người dùng phê duyệt sẽ được kiểm tra chéo qua 4 bài test nghiêm ngặt:
1. **Functional Regression Test:** Thử nghiệm click mua tài khoản, thử nghiệm bấm nút nạp thẻ cào, thử nghiệm đặt đơn cày thuê để xác nhận form và modal hoạt động 100% chuẩn xác.
2. **Cross-Browser & Responsive Test:** Kiểm tra giao diện trên Desktop (1920x1080, 1366x768), Tablet (768x1024), và Smartphone (375x667, 390x844, 414x896).
3. **Console Cleanliness:** Đảm bảo tab Console trong Chrome DevTools không xuất hiện bất kỳ dòng chữ đỏ (Uncaught TypeError / SyntaxError) nào.
4. **Performance & CLS Test:** Đảm bảo điểm Layout Shift (CLS) = 0, không có hiện tượng giật cục màn hình khi tải trang.