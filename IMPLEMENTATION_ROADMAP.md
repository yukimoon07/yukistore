# LỘ TRÌNH TRIỂN KHAI NÂNG CẤP UI/UX (IMPLEMENTATION ROADMAP)
**Dự án:** Hệ Thống Thương Mại Điện Tử Shop Game (Tài Khoản, Cày Thuê & Nhiệm Vụ Kiếm Tiền)  
**Quy mô:** 5 Giai đoạn nâng cấp tuần tự  
**Nguyên tắc vàng:** Kiểm thử nghiệm thu sau từng giai đoạn • Không làm gián đoạn hệ thống đang chạy.

---

## 1. TỔNG QUAN CÁC GIAI ĐOẠN (PHASE OVERVIEW)

```
[ GIAI ĐOẠN 1 ] ──► [ GIAI ĐOẠN 2 ] ──► [ GIAI ĐOẠN 3 ] ──► [ GIAI ĐOẠN 4 ] ──► [ GIAI ĐOẠN 5 ]
  Hạ Tầng CSS         Header, Nav          Trang Chủ &         Chi Tiết SP &       Nhiệm Vụ, Footer
& Design Tokens        & Song Ngữ           Spotlight           Dịch Vụ Cày         & SEO Audit
```

---

## 2. CHI TIẾT TỪNG GIAI ĐOẠN TRIỂN KHAI

---

### GIAI ĐOẠN 1: THIẾT LẬP NỀN TẢNG DESIGN TOKENS, DỌN DẸP XUNG ĐỘT CSS & KHỞI TẠO TỪ ĐIỂN
* **Mục tiêu:** Tạo nền móng biến số CSS vững chắc, loại bỏ hoàn toàn các đoạn CSS xung đột, gỡ bỏ script Google Translate bên thứ ba, thiết lập 2 file từ điển Tiếng Việt & Tiếng Anh.
* **Các bước triển khai:**
  1. Khởi tạo 2 tệp dịch thuật `resources/lang/vi/shop.php` và `resources/lang/en/shop.php` với đầy đủ khóa từ điển giao diện e-commerce game.
  2. Cập nhật `public/css/modern-gaming.css`: Đưa toàn bộ biến số Design Tokens (Deep Obsidian `#0b0f19`, Frosted Slate `#151f32`, Sapphire, Cyan, Amber) vào `:root`.
  3. Dọn dẹp `resources/views/layouts/user/head.blade.php`: Gỡ bỏ script `translate.google.com` và các style inline rác; nạp font chữ `Inter` mượt mà.
  4. Chạy lệnh xóa cache view (`php artisan view:clear`) để chuẩn bị môi trường sạch.
* **Checklist kiểm tra nghiệm thu Giai đoạn 1:**
  - [ ] Mở trang chủ không còn thấy bất kỳ dấu vết nào của thanh `goog-te-banner-frame`.
  - [ ] Font chữ toàn trang chuyển sang `Inter` sắc nét, không lỗi dấu tiếng Việt.
  - [ ] Màu nền trang chủ chuyển sang gam Deep Obsidian dịu mắt, không còn viền trắng gắt chói.
  - [ ] Không có lỗi runtime nào trong Console.

---

### GIAI ĐOẠN 2: NÂNG CẤP HEADER, NAVIGATION BAR, DROPDOWN & BỘ CHUYỂN SONG NGỮ VI/EN
* **Mục tiêu:** Xây dựng thanh điều hướng đỉnh cao (Header) kính mờ đồng bộ 100% phong cách gaming sang trọng, tích hợp bộ chuyển đổi ngôn ngữ native không giật lag.
* **Các bước triển khai:**
  1. Tái cấu trúc `resources/views/layouts/user/header.blade.php`:
     * Thanh Header cao 64px cố định đỉnh màn hình với hiệu ứng kính mờ (`backdrop-filter: blur(20px)`), nền tối `rgba(11, 15, 25, 0.85)`.
     * Tinh gọn danh sách Menu: Trang Chủ, Kho Tài Khoản (Mega Dropdown mượt), Cày Thuê, Nhiệm Vụ Kiếm Tiền, Lịch Sử, Tin Tức.
     * Tích hợp Component Song Ngữ VI / EN dạng Custom Pill Dropdown (chỉ 2 tùy chọn cờ VN và US sắc nét, click chuyển đổi ngay lập tức).
     * Nút "Nạp Tiền" và nút "Tài Khoản / Đăng Nhập" bo góc 10px cao cấp.
  2. Nâng cấp `resources/views/layouts/user/menu-mobile.blade.php`: Drawer trượt mượt mà cho điện thoại, tối ưu vùng bấm ngón tay.
* **Checklist kiểm tra nghiệm thu Giai đoạn 2:**
  - [ ] Thanh Header hiển thị hoàn hảo trên cả Desktop, Tablet và Mobile.
  - [ ] Bấm chuyển ngôn ngữ sang `EN`, menu lập tức hiển thị "Home, Game Accounts, Boosting, Earn Rewards..." mà không bị vỡ bố cục.
  - [ ] Bấm chuyển ngôn ngữ về `VI`, menu quay lại tiếng Việt chuẩn xác.
  - [ ] Mega Dropdown mở ra mượt mà, hover các icon game có hiệu ứng highlight nhẹ.

---

### GIAI ĐOẠN 3: NÂNG CẤP TRANG CHỦ (HERO SPLIT 30-70, SPOTLIGHT 3 DỊCH VỤ, KHO GAME 5 CỘT & REVIEWS)
* **Mục tiêu:** Biến trang chủ thành một trung tâm thương mại game điện tử cuốn hút, bố cục chuẩn tỷ lệ vàng, tăng tối đa tỷ lệ chuyển đổi.
* **Các bước triển khai:**
  1. Cải tiến Hero Section trong `resources/views/user/home.blade.php`:
     * Cân bằng hoàn hảo chiều cao giữa Bảng Top Nạp Tiền (30%) và Banner Cinematic Slider (70%).
     * Hiệu ứng chuyển động fade mượt mà 0.5s cho Banner, touch swipe mượt trên điện thoại.
  2. Nâng cấp Spotlight 3 Dịch Vụ Tiêu Biểu:
     * Cày Thuê Blox Fruits (Banner Kitsune), Cày Thuê Genshin Impact (Banner Furina), Túi Mù Liên Quân 50K (Banner Triệu Vân).
     * Thẻ có tỷ lệ khung hình 16:9, viền ánh sáng tinh tế, tag giá nổi bật và nút CTA sắc sảo.
  3. Tối ưu Hệ thống Thẻ Danh Mục Tài Khoản Game (`gaming-card`):
     * Grid 5 cột trên Desktop, 2 cột trên Mobile.
     * Cân bằng khoảng cách, ảnh bìa hiển thị tỉ lệ 16:10 sắc nét, số lượng tồn kho hiển thị dạng Spec Tag gọn gàng.
  4. Làm mới Bảng Thông Báo Cuộn (Marquee), Ticker giao dịch thời gian thực và Bảng Đánh Giá Khách Hàng (Customer Reviews).
* **Checklist kiểm tra nghiệm thu Giai đoạn 3:**
  - [ ] Hero Section cân đối tuyệt đối 2 bên, không có khoảng trắng thừa.
  - [ ] Cả 3 thẻ dịch vụ Spotlight hiển thị sắc nét, hover nổi nhẹ 4px mượt mà.
  - [ ] Các thẻ danh mục tài khoản (Liên Quân, Blox Fruits, Genshin, FC Mobile) thẳng hàng tăm tắp, không bị thụt thò chiều cao.
  - [ ] Đánh giá khách hàng hiển thị sao vàng sắc nét, avatar bo tròn tinh tế.

---

### GIAI ĐOẠN 4: NÂNG CẤP TRANG CHI TIẾT SẢN PHẨM & TRANG CHI TIẾT DỊCH VỤ CÀY THUÊ
* **Mục tiêu:** Tối ưu hóa trải nghiệm xem tài khoản, xem ảnh chi tiết và chốt đơn mua hàng; nâng cấp trải nghiệm chọn gói cày thuê.
* **Các bước triển khai:**
  1. Cải tạo `resources/views/user/account/detail.blade.php`:
     * Bố cục E-commerce 2 cột chuẩn mực.
     * Cột trái: Thư viện ảnh tương tác mượt mà, bấm mở Lightbox toàn màn hình sắc nét.
     * Cột phải: Khối thông tin tài khoản dạng thẻ kính mờ, giá ATM niêm yết to rõ, nút **MUA NGAY BẰNG SỐ DƯ** làm chủ đạo, các nút phụ Mua ATM và Trả Góp nằm cân đối bên dưới.
  2. Cải tạo `resources/views/user/category/show.blade.php`:
     * Thanh Filter Bar trên một hàng ngang tinh tế (tìm theo mã số, khoảng giá, trạng thái).
     * Empty State lịch sự, cao cấp khi không có sản phẩm.
  3. Cải tạo `resources/views/user/service/show.blade.php`:
     * Danh sách gói cày thuê thiết kế dạng thẻ chọn trực quan.
     * Sticky Floating Calculator bên phải bám dính khi cuộn trang, hiển thị tổng tiền thời gian thực.
* **Checklist kiểm tra nghiệm thu Giai đoạn 4:**
  - [ ] Mở trang chi tiết tài khoản: Ảnh bấm chuyển đổi tức thì, Lightbox xem phóng to ảnh mượt mà.
  - [ ] Bấm thử nút Mua Ngay: Modal xác nhận mở lên êm ái, hiển thị số dư và giá tiền chuẩn xác.
  - [ ] Trang cày thuê: Chọn các gói cày khác nhau, tổng số tiền tự động cập nhật ngay lập tức.

---

### GIAI ĐOẠN 5: NÂNG CẤP TRANG NHIỆM VỤ KIẾM TIỀN, FOOTER, KIỂM TOÁN HIỆU NĂNG & ACCESSIBILITY
* **Mục tiêu:** Hoàn thiện trang nhiệm vụ kiếm tiền vượt link, đồng bộ chân trang Footer, kiểm tra tốc độ tải trang, đảm bảo chuẩn SEO và khả năng tiếp cận.
* **Các bước triển khai:**
  1. Cải tiến `resources/views/user/mission/index.blade.php` và `giftcode.blade.php`:
     * Trực quan hóa quy trình 3 bước vượt link (Bấm nhận -> Lấy mã -> Nhập mã).
     * Nút nhận nhiệm vụ và số tiền thưởng (+5.000 đ) bắt mắt, thu hút người dùng.
  2. Tinh chỉnh `resources/views/layouts/user/footer.blade.php`:
     * Nền Deep Obsidian, đường phân cách thanh mảnh, căn lề 4 cột đều đặn.
     * Mobile Bottom Bar cao 58px, tối ưu vị trí ngón tay cái, icon sắc nét.
  3. Kiểm tra toàn diện hiệu năng & khả năng tương thích:
     * Kiểm tra responsiveness trên các kích thước màn hình từ 360px đến 2560px.
     * Kiểm tra `@media (prefers-reduced-motion: reduce)`.
     * Xác nhận Google Search Schema JSON-LD hợp lệ.
* **Checklist kiểm tra nghiệm thu Giai đoạn 5:**
  - [ ] Trang nhiệm vụ kiếm tiền hoạt động hoàn hảo, người dùng dễ hiểu quy trình vượt link.
  - [ ] Footer hiển thị trang nhã, không còn màu chữ xám tối lỗi thời.
  - [ ] Điểm số Layout Shift (CLS) = 0.
  - [ ] 100% chức năng backend (mua tài khoản, đặt cọc cày thuê, làm nhiệm vụ, nạp thẻ) hoạt động trơn tru không lỗi.

---

## 3. TIÊU CHÍ HOÀN THÀNH TỔNG THỂ (DEFINITION OF DONE - DOD)

1. **Về Thẩm mỹ & Trải nghiệm (UI/UX):**
   * Đạt chuẩn Minimalist, Modern, Premium Dark Slate.
   * Hoàn toàn không còn cảm giác "AI Slop" hoặc giao diện web game lỗi thời.
   * Khoảng cách và cỡ chữ đạt tỷ lệ vàng, dễ đọc, không mỏi mắt.
2. **Về Bảo toàn Kỹ thuật (Technical Integrity):**
   * Toàn bộ Database, Controller, Model, Logic thanh toán và đặt hàng giữ nguyên 100%.
   * Không cài đặt thêm bất kỳ thư viện npm/composer bên ngoài nào.
   * Không có lỗi đỏ trong Console trình duyệt.
3. **Về Đa ngôn ngữ (i18n):**
   * Song ngữ Tiếng Việt & Tiếng Anh chuyển đổi mượt mà, chuẩn thuật ngữ gaming.
   * Đã gỡ bỏ vĩnh viễn Google Translate iframe gây giật màn hình.
4. **Về SEO & Tốc độ (Performance):**
   * Tối ưu hóa cấu trúc heading H1-H3 chuẩn SEO.
   * Tải trang nhanh, hình ảnh có lazy-loading và kích thước cố định chống giật layout.