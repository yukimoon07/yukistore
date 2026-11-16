# CHIẾN LƯỢC ĐA NGÔN NGỮ SONG NGỮ VI - EN (I18N PLAN)
**Dự án:** Hệ Thống Thương Mại Điện Tử Shop Game  
**Hai ngôn ngữ hỗ trợ:** Tiếng Việt (Mặc định - `vi`) & Tiếng Anh (`en`)  
**Mục tiêu:** Chuyển đổi ngôn ngữ chính xác, không dịch sai thuật ngữ game, mượt mà, không giật layout, không phụ thuộc Google Translate.

---

## 1. PHÂN TÍCH HIỆN TRẠNG & NGUYÊN DO CẦN THAY THẾ GOOGLE TRANSLATE

### 1.1. Vấn đề của phương án Google Translate hiện tại
Hiện tại, trang web đang nhúng đoạn script:
`<script src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>`
kèm theo logic can thiệp cookie `googtrans`.
* **Hậu quả tiêu cực:**
  1. **Làm vỡ DOM và gián đoạn JavaScript:** Google Translate tự động chèn các thẻ `<font>` và iframe `goog-te-banner-frame` làm vỡ cấu trúc CSS Flexbox/Grid và giật màn hình (Layout Shift).
  2. **Dịch máy thô thiển, sai lệch chuyên ngành game:**
     * "Blox Fruits" bị dịch thành "Trái Cây Khối".
     * "Cày Thuê" bị dịch thành "Plowing for hire" hoặc "Farm rental".
     * "Túi Mù" bị dịch thành "Blind Bag" thay vì thuật ngữ game chuẩn "Mystery Box" / "Lucky Crate".
     * "Trắng Thông Tin" bị dịch thành "White Information" thay vì chuẩn quốc tế "Clean / Unlinked Account".
  3. **Thời gian tải trang chậm (Page Speed Regression):** Phải tải thêm tài nguyên bên thứ ba từ máy chủ Google, làm tăng thời gian phản hồi trang thêm 1.2s - 2.0s.

### 1.2. Giải pháp Kiến trúc chuẩn (Native Laravel Localization + Lightweight Client Cache)
Dự án đã có sẵn **`LanguageController.php`** và **`SetLocale` middleware** nằm trong Kernel `web` middleware group. Hệ thống này lưu trữ `session('locale')` hoàn toàn hợp lệ.
* **Chiến lược:**
  1. Loại bỏ triệt để đoạn nhúng script Google Translate trong `head.blade.php`.
  2. Xây dựng 2 file từ điển chuẩn của Laravel trong `resources/lang/vi/shop.php` và `resources/lang/en/shop.php`.
  3. Sử dụng helper chuẩn Blade `{{ __('shop.key_name') }}` cho toàn bộ text trên giao diện.
  4. Cung cấp bộ từ điển JSON `window.__I18N__` cho các thông báo Toast/Alert JavaScript.
  5. Khi người dùng bấm chuyển cờ (VI/EN) trên Header, hàm gọi nhẹ nhàng tới route chuyển ngôn ngữ hoặc cập nhật session locale và reload trang trong 0.2s mà không hề bị vỡ DOM hay xuất hiện thanh bar khó chịu.

---

## 2. CẤU TRÚC TỆP DỊCH THUẬT (TRANSLATION DIRECTORY STRUCTURE)

```
shopgame/
└── resources/
    └── lang/
        ├── vi/
        │   ├── auth.php
        │   ├── validation.php
        │   └── shop.php        <-- (Tệp từ điển Tiếng Việt toàn diện)
        └── en/
            ├── auth.php
            ├── validation.php
            └── shop.php        <-- (Tệp từ điển Tiếng Anh toàn diện)
```

---

## 3. BẢNG THUẬT NGỮ CHUYÊN NGÀNH GAME (GAMING GLOSSARY)

Để đảm bảo tính chuyên nghiệp và tiêu chuẩn quốc tế, toàn bộ thuật ngữ được chuẩn hóa theo bảng sau:

| Thuật ngữ Tiếng Việt | Chuẩn dịch Tiếng Anh | Ngữ cảnh sử dụng |
| :--- | :--- | :--- |
| **Trang chủ** | Home | Menu chính |
| **Kho tài khoản** | Account Shop / Game Accounts | Menu danh mục |
| **Cày thuê** | Boosting / Power Leveling | Dịch vụ cày cấp, nhiệm vụ |
| **Dịch vụ nổi bật** | Featured Services | Section nổi bật trang chủ |
| **Túi mù** | Mystery Box / Lucky Crate | Thử vận may trúng thưởng |
| **Trắng thông tin** | Clean Info / Unlinked Account | Thuộc tính tài khoản an toàn |
| **Đăng ký (Acc Reg)** | Registered Account | Tài khoản đã đăng ký |
| **Nick trải nghiệm skin tự chọn** | Skin Trial Account | Danh mục tài khoản Liên Quân |
| **Còn: X • Đã bán: Y** | Stock: X • Sold: Y | Thông số tồn kho thẻ sản phẩm |
| **Mua ngay** | Buy Now | Nút hành động chính |
| **Thuê ngay** | Order Boosting | Nút hành động dịch vụ |
| **Xem chi tiết** | View Details | Nút phụ trên thẻ card |
| **Nhiệm vụ kiếm tiền** | Earn Rewards / Missions | Dịch vụ vượt link nhận thưởng |
| **Nạp tiền** | Deposit / Top Up | Nạp số dư tài khoản |
| **Nạp thẻ cào** | Card Deposit | Hình thức nạp thẻ cào viễn thông |
| **Nạp ATM / Momo** | Bank / E-Wallet Deposit | Chuyển khoản ngân hàng |
| **Lịch sử giao dịch** | Transaction History | Quản lý đơn hàng |
| **Tin tức** | News & Updates | Blog thông báo |
| **Top nạp tháng** | Top Depositors of [Month] | Bảng vinh danh |
| **Phần thưởng** | Monthly Rewards | Tab giải thưởng |
| **Vòng quay may mắn** | Lucky Spin / Wheel of Fortune | Trò chơi may mắn |
| **Đã quay: X lượt** | Spins: X times | Thông số vòng quay |
| **Đổi mã thưởng** | Redeem Giftcode | Trang nhập mã kiếm tiền |
| **Bảo hành 100%** | 100% Warranty / Safe 2FA | Cam kết bảo mật tài khoản |
| **Không có dữ liệu** | No items available | Trạng thái rỗng (Empty state) |

---

## 4. CHI TIẾT NỘI DUNG TỆP TỪ ĐIỂN MẪU (`shop.php`)

### 4.1. Tệp Tiếng Việt (`resources/lang/vi/shop.php`)
```php
<?php

return [
    // Header & Nav
    'nav_home' => 'Trang Chủ',
    'nav_accounts' => 'Kho Tài Khoản',
    'nav_services' => 'Cày Thuê',
    'nav_missions' => 'Kiếm Tiền',
    'nav_deposit' => 'Nạp Tiền',
    'nav_history' => 'Lịch Sử',
    'nav_news' => 'Tin Tức',
    'nav_affiliate' => 'Tiếp Thị Liên Kết',
    'nav_login' => 'Đăng Nhập',
    'nav_register' => 'Đăng Ký',
    'nav_profile' => 'Tài Khoản',

    // Hero Section
    'hero_top_deposit' => 'Top Nạp Tháng :month',
    'hero_rewards' => 'Phần Thưởng',
    'hero_deposit_now' => 'Nạp Ngay',
    'hero_empty_deposit' => 'Chưa có dữ liệu nạp thẻ tháng này.',

    // Spotlight & Services
    'spotlight_title' => 'DANH MỤC DỊCH VỤ NỔI BẬT',
    'spotlight_hot_badge' => 'DỊCH VỤ HOT',
    'spotlight_fast_badge' => 'SIÊU NHANH',
    'spotlight_vip_badge' => 'TÚI MÙ VIP',
    'spotlight_from_price' => 'Từ :price đ',
    'btn_order_now' => 'Thuê Ngay',
    'btn_open_box' => 'Mở Túi Ngay',
    'btn_buy_now' => 'Mua Ngay',
    'btn_view_detail' => 'Xem Chi Tiết',

    // Product Card
    'card_stock' => 'Còn',
    'card_sold' => 'Đã bán',
    'card_vip_badge' => 'ACC VIP',
    'card_view_all' => 'Xem tất cả',

    // Product Detail
    'detail_id' => 'Mã số',
    'detail_account_info' => 'Thông tin tài khoản',
    'detail_market_best' => 'Rẻ vô đối, giá tốt nhất thị trường',
    'detail_buy_balance' => 'Mua Bằng Số Dư',
    'detail_buy_atm' => 'Mua Bằng ATM, Momo',
    'detail_installment' => 'Mua Trả Góp',
    'detail_installment_sub' => 'Trả trước từ :amount đ',
    'detail_sold_out' => 'TÀI KHOẢN NÀY ĐÃ ĐƯỢC BÁN',
    'detail_related' => 'Tài khoản liên quan',

    // Missions
    'mission_page_title' => 'Nhiệm Vụ Kiếm Tiền',
    'mission_page_subtitle' => 'Hoàn thành các nhiệm vụ vượt link đơn giản để nhận số dư tài khoản hoàn toàn miễn phí.',
    'mission_reward_text' => 'Thưởng',
    'mission_start_btn' => 'Bắt Đầu Nhiệm Vụ',
    'mission_redeem_btn' => 'Đổi Mã Nhận Thưởng',

    // Filter & Search
    'filter_code' => 'Mã số...',
    'filter_price_from' => 'Giá từ...',
    'filter_price_to' => 'Giá đến...',
    'filter_status' => 'Trạng Thái',
    'filter_status_all' => 'Tất cả trạng thái',
    'filter_status_available' => 'Chưa bán',
    'filter_status_sold' => 'Đã bán',
    'filter_search_btn' => 'Tìm kiếm',
    'filter_reset_btn' => 'Làm mới',

    // Footer
    'footer_quick_links' => 'Truy Cập Nhanh',
    'footer_support' => 'Hỗ Trợ Khách Hàng',
    'footer_contact' => 'Thông Tin Liên Hệ',
    'footer_rights' => 'Tất cả quyền được bảo lưu.',
];
```

### 4.2. Tệp Tiếng Anh (`resources/lang/en/shop.php`)
```php
<?php

return [
    // Header & Nav
    'nav_home' => 'Home',
    'nav_accounts' => 'Game Accounts',
    'nav_services' => 'Boosting',
    'nav_missions' => 'Earn Rewards',
    'nav_deposit' => 'Deposit',
    'nav_history' => 'History',
    'nav_news' => 'News',
    'nav_affiliate' => 'Affiliate Program',
    'nav_login' => 'Login',
    'nav_register' => 'Register',
    'nav_profile' => 'Profile',

    // Hero Section
    'hero_top_deposit' => 'Top Depositors - Month :month',
    'hero_rewards' => 'Rewards',
    'hero_deposit_now' => 'Top Up Now',
    'hero_empty_deposit' => 'No deposit data recorded this month.',

    // Spotlight & Services
    'spotlight_title' => 'FEATURED SERVICES & SPOTLIGHT',
    'spotlight_hot_badge' => 'HOT SERVICE',
    'spotlight_fast_badge' => 'SUPER FAST',
    'spotlight_vip_badge' => 'VIP CRATE',
    'spotlight_from_price' => 'From :price VND',
    'btn_order_now' => 'Order Boosting',
    'btn_open_box' => 'Open Box Now',
    'btn_buy_now' => 'Buy Now',
    'btn_view_detail' => 'View Details',

    // Product Card
    'card_stock' => 'In Stock',
    'card_sold' => 'Sold',
    'card_vip_badge' => 'VIP ACCOUNT',
    'card_view_all' => 'View All',

    // Product Detail
    'detail_id' => 'Account ID',
    'detail_account_info' => 'Account Specifications',
    'detail_market_best' => 'Best value guarantee, instant delivery',
    'detail_buy_balance' => 'Pay With Balance',
    'detail_buy_atm' => 'Pay via Bank / E-Wallet',
    'detail_installment' => 'Installment Plan',
    'detail_installment_sub' => 'Down payment from :amount VND',
    'detail_sold_out' => 'THIS ACCOUNT HAS BEEN SOLD',
    'detail_related' => 'Related Accounts',

    // Missions
    'mission_page_title' => 'Monetization Missions',
    'mission_page_subtitle' => 'Complete simple link navigation tasks to earn free wallet balance effortlessly.',
    'mission_reward_text' => 'Reward',
    'mission_start_btn' => 'Start Mission',
    'mission_redeem_btn' => 'Redeem Giftcode',

    // Filter & Search
    'filter_code' => 'Account ID...',
    'filter_price_from' => 'Price from...',
    'filter_price_to' => 'Price to...',
    'filter_status' => 'Status',
    'filter_status_all' => 'All Statuses',
    'filter_status_available' => 'Available',
    'filter_status_sold' => 'Sold',
    'filter_search_btn' => 'Search',
    'filter_reset_btn' => 'Reset',

    // Footer
    'footer_quick_links' => 'Quick Links',
    'footer_support' => 'Customer Support',
    'footer_contact' => 'Contact Information',
    'footer_rights' => 'All rights reserved.',
];
```

---

## 5. CƠ CHẾ CHUYỂN ĐỔI NGÔN NGỮ TRÊN GIAO DIỆN

* **Component Dropdown trên Header:**
  * Hiển thị lá cờ và mã ISO hiện tại (🇻🇳 `VI` hoặc 🇺🇸 `EN`).
  * Khi bấm vào, mở dropdown gồm 2 lựa chọn:
    1. **Tiếng Việt** (Cờ Việt Nam) -> gọi `switchLocale('vi')`.
    2. **English** (Cờ Hoa Kỳ) -> gọi `switchLocale('en')`.
* **Cơ chế lưu giữ:**
  * Lưu vào `session(['locale' => $locale])` thông qua route Laravel hiện có, đồng thời lưu `localStorage.setItem('user_locale', locale)`.
  * Không làm tải lại toàn bộ tài nguyên nặng (cache các ảnh và CSS đã load), đảm bảo phản hồi tức thì dưới 250ms.