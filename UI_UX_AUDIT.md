# BÁO CÁO PHÂN TÍCH & ĐÁNH GIÁ HIỆN TRẠNG GIAO DIỆN (UI/UX AUDIT)
**Dự án:** Hệ Thống Thương Mại Điện Tử Shop Game (Tài Khoản, Cày Thuê & Nhiệm Vụ Kiếm Tiền)  
**Vai trò:** Senior Frontend Architect & UI/UX Designer  
**Thời gian lập:** Tháng 11/2026  
**Mục tiêu:** Kiểm toán toàn diện hiện trạng giao diện, xác định điểm nghẽn thị giác, đánh giá mã nguồn frontend và xác lập ranh giới bảo toàn tuyệt đối logic backend.

---

## 1. TỔNG QUAN HỆ THỐNG VÀ KIẾN TRÚC HIỆN TẠI

### 1.1. Công nghệ nền tảng (Tech Stack)
* **Backend Framework:** Laravel 10.x trên nền PHP 8.3 (Laragon runtime).
* **Cơ sở dữ liệu:** MySQL 8.4 (InnoDB engine, utf8mb4 charset).
* **Template Engine:** Laravel Blade Templates (`resources/views/`).
* **UI Component Libraries:**
  * Ant Design CSS (v4.24.16) nhúng qua CDN.
  * FontAwesome Free (v6.7.2) & Iconify (v3.1.0).
  * Fui Toast JS (v1.0.1) & SweetAlert2 cho thông báo tương tác.
  * jQuery 3.7.1 & Vanilla JavaScript (`public/js/app.js`).
* **Hệ thống CSS hiện hành:**
  * `public/css/style.css`: Bản ghi đè Ant Design ban đầu (tông đỏ/trắng cũ).
  * `public/css/legacy-compat.css`: CSS tương thích các phiên bản cũ.
  * `public/css/modern-gaming.css`: Tệp stylesheet mới được bổ sung, định hình theme Dark Slate Gaming.
  * Hàng trăm dòng CSS inline rải rác trong `head.blade.php`, `header.blade.php`, `home.blade.php`, `footer.blade.php`.

### 1.2. Danh mục dịch vụ và luồng thương mại cốt lõi
1. **Kho tài khoản game (Game Accounts):**
   * Phân loại theo 4 nhóm game chính: *Liên Quân Mobile*, *Roblox / Blox Fruits*, *Genshin Impact*, *FC Mobile & FC Online*.
   * Cơ chế: Tài khoản có sẵn ID, giá niêm yết ATM vs Card, thuộc tính chi tiết (tướng, trang phục, rank, đồ họa).
2. **Kho túi mù / Thử vận may (Mystery Boxes):**
   * Sản phẩm mở túi ngẫu nhiên với các mức giá định sẵn (20k, 39k, 50k, 99k, v.v.).
3. **Dịch vụ cày thuê (Game Boosting Services):**
   * Cày cấp, săn item, mở tộc, hoàn thành nhiệm vụ trong Blox Fruits, Genshin Impact, Rise of Kingdoms.
   * Tính toán chi phí động theo gói dịch vụ (`packages`).
4. **Hệ thống làm nhiệm vụ vượt link kiếm tiền (Link Monetization):**
   * Người dùng làm nhiệm vụ vượt link, nhập mã xác thực (giftcode) để nhận số dư tài khoản.

---

## 2. PHÂN TÍCH CHI TIẾT HIỆN TRẠNG UI/UX TỪNG KHU VỰC

### 2.1. Header & Navigation Bar
* **Hiện trạng cấu trúc:**
  * Navbar fixed ở đỉnh màn hình (cao 60px) trong `header.blade.php`.
  * Gồm Logo thương hiệu, các liên kết chính: *Trang Chủ*, *Cày Thuê*, *Danh Mục (Mega Dropdown)*, *Nạp Tiền*, *Lịch Sử*, *Tin Tức*, *Tiếp Thị Liên Kết*, *Kiếm Tiền (HOT)*.
  * Phía bên phải gồm cụm chuyển đổi ngôn ngữ và nút Đăng nhập / Profile user.
* **Vấn đề tồn tại (Pain Points):**
  1. **Xung đột thẩm mỹ (Visual Inconsistency):** Thanh navbar có nền trắng mờ `rgba(255, 255, 255, 0.95)` với viền `#f0f0f0`, trong khi nội dung trang chủ lại sử dụng nền tối đen/slate của Dark Theme. Điều này tạo ra đường ranh giới chói lóa, đứt gãy thị giác ngay khi người dùng mở trang web.
  2. **Quá tải mật độ liên kết:** Quá nhiều tab trên cùng một thanh điều hướng khiến thanh menu dễ bị tràn chữ, vỡ hàng khi xem trên màn hình laptop nhỏ (1024px - 1280px).
  3. **Bộ chuyển đổi ngôn ngữ phụ thuộc Google Translate iframe:** Sử dụng script Google Translate bên thứ ba chèn `iframe.goog-te-banner-frame`. Khi tải trang tạo hiện tượng giật màn hình (Layout Shift), dịch sai các thuật ngữ game (ví dụ dịch "Blox Fruits" thành "Hoa Quả", "Acc" thành "Tài khoản"), gây giảm nghiêm trọng tính chuyên nghiệp của thương hiệu.

### 2.2. Hero Section (Khu vực đầu trang chủ)
* **Hiện trạng cấu trúc:**
  * Layout chia đôi 30% - 70%:
    * Cột trái (30%): Bảng vinh danh Top Nạp Tiền trong tháng + Tab Phần Thưởng.
    * Cột phải (70%): Banner Slider trình chiếu các hình ảnh quảng cáo game.
* **Vấn đề tồn tại:**
  1. **Mất cân đối chiều cao:** Cột Top Nạp Tiền và Banner Slider thường xuyên bị lệch chiều cao khi ảnh banner có tỷ lệ khung hình khác nhau, dẫn tới khoảng trắng thừa khó coi ở đáy cột bên trái.
  2. **Typography bảng xếp hạng:** Font size và độ tương phản màu chữ giữa username ẩn danh và số tiền nạp chưa đạt tỷ lệ vàng; nút "Nạp Ngay" ở đáy bảng top nạp có màu đỏ gắt chọi với nền tối.
  3. **Slider indicator:** Nút điều hướng mũi tên và dots indicator ở banner slider có kích thước nhỏ, vùng chạm (touch target) trên mobile chưa đạt chuẩn UX tối thiểu 44x44px.

### 2.3. Spotlight Dịch Vụ Nổi Bật (3 Thẻ Tiêu Biểu)
* **Hiện trạng:**
  * Đã được cấu hình thành công hiển thị 3 dịch vụ tiêu biểu: *Cày Thuê Blox Fruits*, *Cày Thuê Genshin Impact*, *Túi Mù Liên Quân 50K*.
* **Vấn đề tồn tại:**
  1. **Hiệu ứng hover thô:** Đổ bóng khi hover còn nặng, chưa có độ mềm mượt (easing curve) chuẩn modern design.
  2. **Giá tiền và CTA:** Badge giá tiền dạng nhãn dán góc còn đơn điệu, chưa toát lên sự cao cấp của một sản phẩm game có giá trị.

### 2.4. Danh Mục Tài Khoản Game (Grouped Categories & Cards)
* **Hiện trạng:**
  * Các nhóm game: Liên Quân, Blox Fruits, Genshin Impact, FC Mobile & FC Online được tách thành từng section độc lập.
  * Mỗi section dùng grid 5 cột (`gaming-grid-5`) hiển thị các thẻ danh mục con (Acc Trắng Thông Tin, Acc VIP, Thử Vận May, v.v.).
* **Vấn đề tồn tại:**
  1. **Card Layout & Metadata:** Thẻ sản phẩm hiện tại còn nhiều chi tiết vụn vặt: icon hộp kho, text "Còn: X • Đã bán: Y". Các thông số này nằm liền nhau thiếu phân cách rõ ràng, dễ bị rối mắt khi nhìn lướt nhanh trên màn hình di động.
  2. **Thiếu tính linh hoạt trên Tablet & Mobile:** Grid 5 cột trên desktop thu về 2 cột trên mobile nhưng chiều cao card bị kéo dài, gây hao tốn không gian cuộn màn hình.

### 2.5. Trang Chi Tiết Sản Phẩm (`account/detail.blade.php`)
* **Hiện trạng:**
  * Layout 2 cột kiểu E-commerce: Cột trái chứa thư viện ảnh (thumbnails dọc + ảnh chính) tích hợp Lightbox; cột phải chứa thông tin tài khoản, bảng giá và các nút thanh toán.
* **Vấn đề tồn tại:**
  1. **Xung đột màu sắc nút mua:** Có tới 4 nút với 4 màu khác nhau trong cụm mua hàng (Nạp thẻ: viền đỏ; Mua ngay: đỏ đặc; Mua ATM: xanh ngọc `#0bcfa5`; Mua trả góp: xanh dương `#3b82f6`). Sự phân tán màu sắc này vi phạm nguyên tắc tập trung thị giác (Visual Hierarchy), gây bối rối cho khách hàng khi đưa ra quyết định mua.
  2. **Bảng thuộc tính tài khoản (Specs Table):** Các cặp Key - Value hiển thị theo kiểu danh sách phẳng thiếu đường phân cách tinh tế hoặc thẻ badge hiện đại.

### 2.6. Trang Dịch Vụ Cày Thuê (`service/show.blade.php`)
* **Hiện trạng:**
  * Form đặt cày thuê với máy tính giá tự động khi người dùng chọn các gói cày (`packages`).
* **Vấn đề tồn tại:**
  1. **Giao diện bảng gói cày:** Danh sách gói cày xếp chồng dọc khiến trang bị kéo dài lê thê nếu game có từ 10-20 gói cày khác nhau.
  2. **Trải nghiệm tính tiền thời gian thực:** Box tổng tiền thanh toán chưa được làm nổi bật như một "Floating Checkout Summary" khi cuộn trang, khiến người dùng phải kéo lên kéo xuống để kiểm tra tổng chi phí.

### 2.7. Trang Nhiệm Vụ Kiếm Tiền (`mission/index.blade.php`)
* **Hiện trạng:**
  * Header tiêu đề, thanh điều hướng con (Nhiệm vụ, Đổi mã, Lịch sử), thống kê 3 thẻ, danh sách nhiệm vụ vượt link kiếm tiền.
* **Vấn đề tồn tại:**
  1. **Các bước thực hiện:** Hướng dẫn 3 bước vượt link (Bấm nhận -> Vượt link lấy mã -> Nhập mã nhận tiền) còn dàn trải dưới dạng văn bản tĩnh, thiếu minh họa trực quan sinh động theo dạng Timeline hoặc Stepper.

### 2.8. Footer & Mobile Bottom Navigation
* **Hiện trạng:**
  * Footer gồm 4 cột (Giới thiệu, Truy cập nhanh, Hỗ trợ, Liên hệ).
  * Thanh điều hướng đáy dành cho điện thoại (Bottom Bar: Trang chủ, Danh mục, Nạp tiền, Lịch sử, Tài khoản).
* **Vấn đề tồn tại:**
  1. **Màu sắc Footer:** Chữ xám trên nền trắng/xám nhạt `#4a4a4a` trong khi tổng thể giao diện đang hướng về Dark Slate, gây cảm giác "chắp vá".
  2. **Bottom Nav Bar:** Biểu tượng và nhãn chữ chưa có hiệu ứng Active rõ ràng, dễ bị che khuất bởi thanh điều hướng hệ thống (Home Indicator) trên các dòng điện thoại iOS/Android mới.

---

## 3. CÁC NGUY CƠ GÂY LỖI & PHẢN CẢM (AI SLOP / CLUTTER RISKS)

1. **Hiệu ứng lạm dụng (Neon/Glow Overload):** Thói quen của các website game cũ là nhồi nhét quá nhiều viền phát sáng chói lọi, gradient 7 màu và icon lửa bốc cháy. Điều này biến website thành "AI Slop" rẻ tiền, mất đi vẻ cao cấp, uy tín.
2. **Xung đột CSS ghi đè (Specificity Wars):** Việc sử dụng bừa bãi `!important` trong nhiều file CSS khác nhau khiến layout bị vỡ khi resize màn hình hoặc khi Ant Design cập nhật DOM.
3. **Typography lộn xộn:** Sử dụng đan xen cả `Roboto` và `Inter`, độ dày font (font-weight) từ 300 đến 900 không có hệ thống cấp bậc, gây mỏi mắt khi đọc lâu.

---

## 4. DANH MỤC KHU VỰC BẢO TOÀN TUYỆT ĐỐI (DO NOT TOUCH)

Tuân thủ nghiêm ngặt nguyên tắc của Senior Architect:
1. **Toàn bộ Controllers & Models:**
   * Không sửa đổi bất kỳ logic tính toán, truy vấn database hoặc xác thực người dùng trong các Controller (`HomeController`, `GameCategoryController`, `GameAccountController`, `GameServiceController`, `MissionController`, `ProfileController`, v.v.).
2. **Cấu trúc URL & Routing:**
   * Giữ nguyên 100% tên route và tham số (`route('home')`, `route('category.index')`, `route('service.show')`, `route('missions.index')`, v.v.).
3. **Logic Mua Hàng & Thanh Toán:**
   * Giữ nguyên toàn bộ form submit, mã CSRF, hàm JavaScript gọi API mua tài khoản (`buyAccount()`), nạp tiền thẻ cào, mua trả góp, đổi giftcode nhiệm vụ.
4. **Cơ sở dữ liệu & Cấu hình:**
   * Không thay đổi schema cơ sở dữ liệu MySQL, không thay đổi file cấu hình `config/` hoặc `.env`.

---

## 5. KẾT LUẬN KIỂM TOÁN
Giao diện hiện tại có nền tảng cấu trúc dữ liệu rất vững chắc, logic nghiệp vụ hoàn thiện và đầy đủ chức năng. Điểm yếu duy nhất là **sự thiếu đồng bộ về mặt thẩm mỹ thị giác**, xung đột giữa các lớp CSS cũ và mới, cùng sự phụ thuộc vào tiện ích Google Translate. Giải pháp đúng đắn là **chuẩn hóa Design System toàn diện bằng CSS thuần hiện đại và cấu trúc lại các View Blade theo tư duy E-commerce cao cấp**, tuyệt đối không đụng chạm đến phần lõi Backend.