# 📑 TÀI LIỆU TÍCH HỢP API - THEGIOIDEV

Chào mừng bạn đến với tài liệu hướng dẫn tích hợp API của **TheGioiDev** (`https://thegioidev.com`). Hệ thống cung cấp các kết nối API RESTful mạnh mẽ, an toàn và tự động hóa toàn diện các dịch vụ **Hosting cPanel**, **Reseller Hosting (WHM)**, **VPS**, **Tên miền** và **Website**.

---

## 🔑 1. HƯỚNG DẪN XÁC THỰC & CẤU HÌNH CHUNG

Tất cả các yêu cầu gửi đến hệ thống API của TheGioiDev đều yêu cầu xác thực thông qua **API Token** trong Header HTTP.

### 📌 Cài đặt Header Chuẩn (Khuyến nghị DUY NHẤT)
Mỗi yêu cầu API bắt buộc gửi Header chuẩn RESTful sau:
```http
Authorization: Bearer <API_TOKEN>
Content-Type: application/json
Accept: application/json
```

> [!CAUTION]
> ### ⚠️ CÁC LỖI NGUY HIỂM THƯỜNG GẶP KHI TÍCH HỢP (ĐỌC KỸ TRÁNH LỖI)
> 1. **TUYỆT ĐỐI KHÔNG GỬI NHIỀU HEADER XÁC THỰC CÙNG LÚC:**
>    - **Lỗi điển hình:** Gửi cả `Authorization`, `api-key`, và `auth-token` trong cùng một request.
>    - **Hậu quả:** Middleware máy chủ TheGioiDev sẽ nhận diện header `auth-token` là phiên Web Cookie/Session chứ không phải API Token, dẫn đến lỗi:  
>      👉 `{"status":"error","message":"auth-token không hợp lệ hoặc đã hết hạn"}` (Mã 401).
>    - **Khắc phục:** **CHỈ GỬI DUY NHẤT** `Authorization: Bearer <API_TOKEN>`. Không thêm `auth-token` hay `api-key`.
>
> 2. **XỬ LÝ TIỀN TỐ "Bearer " TRONG TOKEN:**
>    - Người dùng hoặc quản trị viên khi sao chép mã Token từ giao diện web TheGioiDev thường có thể sao chép nhầm kèm tiền tố `"Bearer "`.
>    - Khi gắn vào cURL `Authorization: Bearer ` sẽ bị thành `Authorization: Bearer Bearer eyJ...` gây lỗi 401 Unauthorized.
>    - **Khắc phục trong code:** Luôn làm sạch token trước khi gửi:
>      ```php
>      $token = preg_replace('/^Bearer\s+/i', '', trim($token));
>      ```
>
> 3. **BẢO MẬT API TOKEN:**
>    - API Token là khóa tối cao liên kết trực tiếp với số dư tài khoản của bạn trên TheGioiDev.
>    - Tuyệt đối chỉ lưu và gọi API ở **phía Backend (Server-side PHP/NodeJS/Python)**, **KHÔNG BAO GIỜ** để lộ Token ra Client/JavaScript/HTML.

### 🚦 Giới hạn Tốc độ (Rate Limit)
- Hệ thống áp dụng giới hạn **60 requests / phút** cho mỗi địa chỉ IP / API Token. 
- Yêu cầu vượt quá giới hạn sẽ trả về mã lỗi `429 Too Many Requests`.
- Server gốc chính thức của hệ thống là **`https://thegioidev.com`**.

---

## 🏗️ 2. KIẾN TRÚC MÔ HÌNH TÍCH HỢP ĐẠI LÝ (ADMIN & CLIENT)

Khi tích hợp API TheGioiDev vào các website bán hàng / CMS (như CMSNT, ShopClone, Laravel, WordPress,...), mã nguồn **bắt buộc phải chia tách thành 2 phân hệ độc lập**:

### 🛡️ Phân hệ Admin (`/admin/...`)
- **Quản trị API Token:** Cấu hình và lưu trữ `thegioidev_api_token` trong cơ sở dữ liệu (`settings`).
- **Nút "Kiểm tra kết nối":** Cho phép Admin kiểm tra tính hợp lệ của Token trước khi mở bán dịch vụ cho khách.
- **Cấu hình Tỷ lệ Lợi nhuận (% Markup):** Thiết lập % chênh lệch giá so với giá gốc TheGioiDev (ví dụ: cộng thêm 15% - 30% để lấy lời).
- **Quản lý đơn hàng toàn sàn:** Xem danh sách, tra cứu, kiểm tra tình trạng dịch vụ qua API và hỗ trợ khách hàng.

### 👥 Phân hệ Client / Thành viên (`/hosting`, `/cronjob`,...)
- **Hiển thị sản phẩm:** Lấy danh sách máy chủ, gói dịch vụ và hiển thị theo **Giá bán = Giá gốc + % Markup của Admin**.
- **Thanh toán bằng Số dư Ví:** Khách hàng thanh toán dịch vụ bằng tiền đã nạp trong tài khoản website của bạn.
- **Tuyệt đối ẩn Token:** Client không hề biết API Token của TheGioiDev, mọi giao tiếp đều thông qua controller Backend của website.

### 🔄 Quy trình Mua / Kích hoạt Dịch vụ Chuẩn (Safe Transaction Flow)
Khi khách hàng bấm mua dịch vụ:
1. **Bước 1 (Validate):** Kiểm tra trạng thái dịch vụ (có bật không), kiểm tra số dư ví người dùng (`$user['money'] >= $price`).
2. **Bước 2 (Trừ tiền tạm ứng):** Trừ tiền trong ví người dùng (`RemoveCredits`), ghi lại nhật ký giao dịch (`logs`).
3. **Bước 3 (Gọi API TheGioiDev):** Gửi cURL lên TheGioiDev bằng API Token của Admin.
4. **Bước 4 (Xử lý kết quả):**
   - **Nếu Thành công:** Lưu thông tin đơn hàng / Job ID vào bảng cơ sở dữ liệu (`cronjobs` hoặc `hostings`). Trả thông báo thành công cho khách.
   - **Nếu Thất bại:** Tự động **Hoàn tiền ngay lập tức** lại ví người dùng (`RefundCredits`) kèm lý do lỗi. Không để khách hàng bị mất tiền oan.

---

## 🏬 3. HOSTING & RESELLER HOSTING API

**Base URL:** `https://thegioidev.com/api/v1/hosting`

---

### 📦 3.1. Lấy danh sách gói Hosting / Reseller Hosting
Lấy danh sách các gói Hosting cPanel hoặc Reseller Hosting WHM đang mở bán, bảng giá, dung lượng, băng thông và giới hạn số cPanel con (Có hỗ trợ lọc theo loại gói `type` và máy chủ `server_id`).

- **HTTP Method:** `GET`
- **Endpoint:** `/packages`
- **Query Params:**
  - `type`: `reseller` hoặc `hosting` (Mặc định: `reseller`)
  - `server_id`: ID máy chủ cần lọc (Tùy chọn)
- **Header:** `Authorization: Bearer <API_TOKEN>`

#### 📝 Ví dụ cURL
```bash
curl -X GET "https://thegioidev.com/api/v1/hosting/packages?type=reseller&server_id=1" \
  -H "Authorization: Bearer YOUR_API_TOKEN" \
  -H "Accept: application/json"
```

####  Response thành công (200 OK)
```json
{
  "status": "success",
  "data": [
    {
      "server_id": 1,
      "server_name": "Máy Chủ Việt Nam VIP",
      "packages": [
        {
          "id": 67,
          "name": "RSL HOSTING 1",
          "custom_name": "Reseller Hosting Khởi Nghiệp",
          "price": 150000,
          "disk_quota": 10240,
          "disk_label": "10 GB NVMe SSD",
          "bandwidth_limit": "unlimited",
          "bandwidth_label": "Không giới hạn",
          "max_subdomains": "unlimited",
          "max_addon_domains": "unlimited",
          "max_parked_domains": "unlimited",
          "account_limit": 10,
          "extra_features": [
            "WHM Control Panel riêng",
            "Miễn phí SSL Let's Encrypt",
            "Hỗ trợ PHP 7.4 - 8.3"
          ]
        }
      ]
    }
  ]
}
```

---

### 🛒 3.2. Mua Hosting / Reseller Hosting
Tự động mua gói Hosting cPanel hoặc Reseller Hosting WHM tức thì. Hệ thống sẽ trừ tiền trực tiếp từ số dư tài khoản của bạn trên TheGioiDev và tự động khởi tạo tài khoản cPanel/WHM trên máy chủ.

- **HTTP Method:** `POST`
- **Endpoint:** `/buy`
- **Header:** `Authorization: Bearer <API_TOKEN>`, `Content-Type: application/json`

#### 📥 Request Body (JSON)
| Tham số | Kiểu | Bắt buộc | Mô tả |
| :--- | :--- | :--- | :--- |
| `package_id` | Integer | **Có** | ID của gói Hosting hoặc Reseller cần mua (Ví dụ: `67`) |
| `domain` | String | **Có** | Tên miền chính khởi tạo hosting (Ví dụ: `dailyshop.com`) |
| `month` | Integer | **Có** | Số tháng đăng ký (`1`, `3`, `6`, `12`, `24`, `36`) |
| `coupon` | String | Không | Mã giảm giá áp dụng (nếu có) |

#### 📝 Ví dụ cURL
```bash
curl -X POST "https://thegioidev.com/api/v1/hosting/buy" \
  -H "Authorization: Bearer YOUR_API_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "package_id": 67,
    "domain": "dailyshop.com",
    "month": 1
  }'
```

####  Response mua thành công (200 OK)
```json
{
  "status": "success",
  "msg": "Khởi tạo dịch vụ Hosting thành công",
  "data": {
    "hosting_id": 128,
    "domain": "dailyshop.com",
    "username": "dailyshop182",
    "password": "PasswordEx#2026",
    "ip": "103.161.x.x",
    "account_limit": 10,
    "end_date": "2026-10-06 11:30:00"
  }
}
```

---

### 🔄 3.3. Gia hạn Hosting / Reseller Hosting
Gia hạn dịch vụ Hosting hoặc Reseller Hosting. Hệ thống tự động mở khóa ngay lập tức nếu gói đang ở trạng thái hết hạn / tạm khóa.

- **HTTP Method:** `POST`
- **Endpoint:** `/renew`
- **Header:** `Authorization: Bearer <API_TOKEN>`, `Content-Type: application/json`

#### 📥 Request Body (JSON)
| Tham số | Kiểu | Bắt buộc | Mô tả |
| :--- | :--- | :--- | :--- |
| `id` | Integer / String | **Có** | ID của đơn hàng Hosting cần gia hạn (Ví dụ: `128`) |
| `month` | Integer | **Có** | Số tháng gia hạn thêm (`1`, `3`, `6`, `12`, `24`, `36`) |

#### 📝 Ví dụ cURL
```bash
curl -X POST "https://thegioidev.com/api/v1/hosting/renew" \
  -H "Authorization: Bearer YOUR_API_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "id": 128,
    "month": 1
  }'
```

####  Response gia hạn thành công (200 OK)
```json
{
  "status": "success",
  "msg": "Gia hạn thành công thêm 1 tháng",
  "data": {
    "hosting_id": 128,
    "domain": "dailyshop.com",
    "renewed_months": 1,
    "end_date": "2026-11-06 11:30:00"
  }
}
```

---

### 📈 3.4. Nâng cấp gói Hosting / Reseller Hosting (Upgrade)
Nâng cấp gói Hosting cPanel hoặc Reseller Hosting hiện tại lên gói có cấu hình cao hơn. Chi phí nâng cấp sẽ được hệ thống tự động tính toán dựa trên mức chênh lệch giữa giá gói mới và giá gói cũ theo số ngày sử dụng còn lại của đơn hàng.

- **HTTP Method:** `POST`
- **Endpoint:** `/upgrade`
- **Header:** `Authorization: Bearer <API_TOKEN>`, `Content-Type: application/json`

#### 📥 Request Body (JSON)
| Tham số | Kiểu | Bắt buộc | Mô tả |
| :--- | :--- | :--- | :--- |
| `id` | Integer / String | **Có** | ID của đơn hàng Hosting cần nâng cấp (Ví dụ: `128`) |
| `package_id` | Integer | **Có** | ID của gói Hosting mới cần nâng cấp đến (Ví dụ: `68`) |

#### 📝 Ví dụ cURL
```bash
curl -X POST "https://thegioidev.com/api/v1/hosting/upgrade" \
  -H "Authorization: Bearer YOUR_API_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "id": 128,
    "package_id": 68
  }'
```

---

### 📋 3.5. Danh sách đơn hàng Hosting đã mua
Lấy danh sách các đơn hàng Hosting cPanel và Reseller Hosting đã tạo thành công của tài khoản đại lý.

- **HTTP Method:** `GET`
- **Endpoint:** `/list`
- **Query Params:**
  - `page`: Trang hiện tại (Mặc định: `1`)
  - `limit`: Số bản ghi mỗi trang (Mặc định: `50`)
  - `type`: `hosting` hoặc `reseller` (Tùy chọn)
- **Header:** `Authorization: Bearer <API_TOKEN>`

#### 📝 Ví dụ cURL
```bash
curl -X GET "https://thegioidev.com/api/v1/hosting/list?page=1&limit=20" \
  -H "Authorization: Bearer YOUR_API_TOKEN"
```

---

### ⚙️ 3.6. Quản trị & Công cụ cPanel/WHM (CPanel Actions)
Thực hiện toàn bộ 13 thao tác quản trị trực tiếp trên máy chủ cPanel và WHM thông qua duy nhất một Endpoint.

- **HTTP Method:** `POST`
- **Endpoint:** `/action`
- **Header:** `Authorization: Bearer <API_TOKEN>`, `Content-Type: application/json`

#### 📋 Bảng tổng hợp các giá trị `action_type`:

| `action_type` | Mô tả tính năng | Tham số yêu cầu thêm (JSON Body) |
| :--- | :--- | :--- |
| `changepassword` | Đổi mật khẩu Hosting | `password` (String, Mật khẩu mới) |
| `logincpanel` | Link Đăng nhập nhanh cPanel SSO 1 chạm | `app` (Tùy chọn: `FileManager_Home`, `Database_phpMyAdmin`, `Email_Accounts`, `DNS_ZoneEditor`, `Security_SSL`,...) |
| `loginwhm` | Link Đăng nhập WHM SSO 1 chạm | Không (Dành cho gói Reseller Hosting) |
| `stats` | Kiểm tra dung lượng Disk & Băng thông | Không |
| `rebuild` | Khởi tạo lại / Cài lại Hosting sạch | Không (Xóa sạch mã nguồn & database để cài lại mới) |
| `change_domain` | Đổi tên miền chính của hosting | `domain` (String, Tên miền mới) |
| `add_addon_domain` | Thêm tên miền Addon Domain | `domain` (String, Tên miền Addon mới) |
| `add_subdomain` | Thêm tên miền con Subdomain | `subdomain` (String), `rootdomain` (String) |
| `add_cron` | Tạo Cronjob tự động | `cron_url` (String URL full), `minute` (Integer từ 1-60). Tối đa 2 Cronjob/hosting |
| `block_ip` | Chặn địa chỉ IP truy cập | `ip` (String, Địa chỉ IP hợp lệ) |
| `unblock_ip` | Gỡ chặn địa chỉ IP | `ip` (String, Địa chỉ IP hợp lệ) |
| `transfer` | Chuyển quyền quản trị cho user khác | `email` (String, Email tài khoản nhận trên TheGioiDev). Phí chuyển: 100đ |
| `delete` | Xóa gói Hosting vĩnh viễn | Không (Xóa sạch hosting trên WHM server & hệ thống) |

---

## ⏱️ 4. CRONJOB API (DỊCH VỤ PING TỰ ĐỘNG THEGIOIDEV)

**Base URL:** `https://thegioidev.com/api/agency/cronjobs`

Quản lý và kích hoạt lịch gọi HTTP (Cronjob) tự động định kỳ trên cụm máy chủ chuyên dụng TheGioiDev. Hệ thống hỗ trợ phương thức GET / POST, Custom Headers (JSON), Request Body và tự động cân bằng tải.

> [!IMPORTANT]
> Tất cả các endpoint trong CRONJOB API yêu cầu gửi Header:
> ```http
> Authorization: Bearer <API_TOKEN>
> Content-Type: application/json
> Accept: application/json
> ```
> Tuyệt đối **không** gửi kèm header `auth-token`!

---

### 🖥️ 4.1. Lấy danh sách cụm máy chủ Cronjob
Lấy danh sách các cụm máy chủ Cronjob đang hoạt động, bảng giá, chu kỳ tối thiểu (`timeloop`) và số slot còn trống. Dùng để hiển thị lên trang Mua Cronjob và kiểm tra kết nối API trong trang Quản trị (Admin).

- **HTTP Method:** `GET`
- **Endpoint:** `/servers`
- **Header:** `Authorization: Bearer <API_TOKEN>`

#### 📝 Ví dụ cURL
```bash
curl -X GET "https://thegioidev.com/api/agency/cronjobs/servers" \
  -H "Authorization: Bearer YOUR_API_TOKEN" \
  -H "Accept: application/json"
```

####  Response thành công (200 OK)
```json
{
  "error": 0,
  "data": [
    {
      "id": "65f1234567890abcdef12345",
      "name": "Server VIP 1s",
      "price": 50000,
      "originalPrice": 50000,
      "discountPercent": 0,
      "usageLimit": 100,
      "currentUsage": 24,
      "timeloop": 1,
      "remaining": 76,
      "full": false
    },
    {
      "id": "65f1234567890abcdef12346",
      "name": "Server Standard 30s",
      "price": 20000,
      "originalPrice": 20000,
      "discountPercent": 10,
      "usageLimit": 200,
      "currentUsage": 85,
      "timeloop": 30,
      "remaining": 115,
      "full": false
    }
  ],
  "meta": {
    "requestId": "req_a1b2c3d4e5f6",
    "timestamp": "2026-10-01T14:30:00.000Z"
  }
}
```

---

### 🚀 4.2. Tạo Cronjob mới (Thuê slot định kỳ)
Khởi tạo và kích hoạt cronjob mới. Chi phí thuê theo tháng được trừ trực tiếp vào số dư ví của tài khoản API TheGioiDev.

- **HTTP Method:** `POST`
- **Endpoint:** `/buy`
- **Header:** `Authorization: Bearer <API_TOKEN>`, `Content-Type: application/json`

#### 📋 Bảng tham số Body (JSON)
| Trường | Kiểu dữ liệu | Bắt buộc | Mô tả |
| :--- | :--- | :--- | :--- |
| `url` | String | **Có** | Đường dẫn URL cần ping định kỳ (Bắt buộc bắt đầu bằng `http://` hoặc `https://`. Hệ thống chặn dải IP nội bộ theo chuẩn Anti-SSRF). |
| `intervalSeconds` | Integer | **Có** | Chu kỳ lặp lại tính bằng giây (Ví dụ: 1, 5, 10, 30, 60... Tối thiểu theo `timeloop` của server đã chọn, tối đa 31,536,000 giây). |
| `serverId` | String | Không | ID máy chủ (`_id` 24 ký tự). Nếu để trống, hệ thống sẽ tự động gán vào máy chủ còn slot trống có giá tối ưu nhất. |
| `method` | String | Không | Phương thức HTTP: `"GET"` hoặc `"POST"` (Mặc định: `"GET"`). |
| `month` | Integer | Không | Số tháng duy trì dịch vụ từ 1 đến 24 tháng (Mặc định: `1`). |
| `headers` | Object / String | Không | Headers tùy chỉnh kèm theo request (Chấp nhận JSON Object như `{"Authorization": "Bearer ..."}` hoặc chuỗi JSON. Tối đa 32 headers). |
| `body` | String | Không | Nội dung Body gửi kèm khi method là `"POST"` (Tối đa 16KB). |

#### 📝 Ví dụ cURL
```bash
curl -X POST "https://thegioidev.com/api/agency/cronjobs/buy" \
  -H "Authorization: Bearer YOUR_API_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "url": "https://api.example.com/cron/sync-data",
    "intervalSeconds": 60,
    "method": "POST",
    "month": 1,
    "serverId": "65f1234567890abcdef12345",
    "headers": {
      "Authorization": "Bearer secret_api_token",
      "Content-Type": "application/json"
    },
    "body": "{\"action\":\"auto_sync\"}"
  }'
```

####  Response thành công (200 OK)
```json
{
  "error": 0,
  "message": "Tạo cronjob thành công",
  "data": {
    "jobId": "65f9876543210fedcba54321",
    "id": "65f9876543210fedcba54321",
    "url": "https://api.example.com/cron/sync-data",
    "method": "POST",
    "intervalSeconds": 60,
    "month": 1,
    "payment": 30000,
    "serverId": "65f1234567890abcdef12345",
    "serverName": "Server VIP 1s",
    "status": "active",
    "expiresAt": "2026-11-01T14:30:00.000Z",
    "nextRunAt": "2026-10-01T14:30:00.000Z",
    "createdAt": "2026-10-01T14:30:00.000Z"
  },
  "meta": {
    "requestId": "req_84f937e29cb10427b5",
    "timestamp": "2026-10-01T14:30:00.000Z"
  }
}
```

---

### 📜 4.3. Lấy danh sách Cronjob của bạn
Lấy danh sách các cronjob của tài khoản đối tác đã tạo, kèm phân trang, tìm kiếm và lọc.

- **HTTP Method:** `GET`
- **Endpoint:** `/` (hoặc `/api/agency/cronjobs`)
- **Query Params:**
  - `page`: Số trang (Mặc định: `1`)
  - `limit`: Số lượng bản ghi mỗi trang (1-100, Mặc định: `20`)
  - `search`: Tìm kiếm theo URL
  - `sortField`: Sắp xếp theo `createdAt`, `url`, `intervalSeconds`, `status`
  - `sortDir`: `asc` hoặc `desc`
- **Header:** `Authorization: Bearer <API_TOKEN>`

#### 📝 Ví dụ cURL
```bash
curl -X GET "https://thegioidev.com/api/agency/cronjobs?page=1&limit=20" \
  -H "Authorization: Bearer YOUR_API_TOKEN"
```

---

### 🔍 4.4. Chi tiết Cronjob
Xem chi tiết một cronjob cụ thể, kèm trạng thái vận hành, thời điểm ping cuối cùng và mã phản hồi HTTP.

- **HTTP Method:** `GET`
- **Endpoint:** `/{id}` (hoặc `/api/agency/cronjobs/{id}`)
- **Header:** `Authorization: Bearer <API_TOKEN>`

#### 📝 Ví dụ cURL
```bash
curl -X GET "https://thegioidev.com/api/agency/cronjobs/65f9876543210fedcba54321" \
  -H "Authorization: Bearer YOUR_API_TOKEN"
```

---

### ⏸️ 4.5. Tạm dừng / Tiếp tục Cronjob (Toggle Status)
Chuyển đổi trạng thái cronjob giữa **Tạm dừng (`paused`)** và **Đang chạy (`active`)**. Khi tạm dừng, server ngừng ping. Khi bật lại, lịch ping được kích hoạt chạy ngay lập tức.

- **HTTP Method:** `POST`
- **Endpoint:** `/{id}/toggle` (hoặc `POST /api/agency/cronjobs/toggle` với `{ "id": "..." }` trong Body)
- **Header:** `Authorization: Bearer <API_TOKEN>`

#### 📝 Ví dụ cURL
```bash
curl -X POST "https://thegioidev.com/api/agency/cronjobs/65f9876543210fedcba54321/toggle" \
  -H "Authorization: Bearer YOUR_API_TOKEN"
```

####  Response thành công (200 OK)
```json
{
  "error": 0,
  "message": "Đã tạm dừng cronjob thành công",
  "data": {
    "id": "65f9876543210fedcba54321",
    "status": "paused"
  }
}
```

---

### ✏️ 4.6. Chỉnh sửa cấu hình Cronjob
Cập nhật đường dẫn URL, phương thức HTTP, chu kỳ lặp lại (`intervalSeconds`), Custom Headers hoặc Request Body mà không cần tạo mới.

- **HTTP Method:** `PATCH` (hoặc `PUT`)
- **Endpoint:** `/{id}` (hoặc `/api/agency/cronjobs/{id}`)
- **Header:**
  - `Authorization: Bearer <API_TOKEN>`
  - `Content-Type: application/json`

#### 📝 Ví dụ cURL
```bash
curl -X PATCH "https://thegioidev.com/api/agency/cronjobs/65f9876543210fedcba54321" \
  -H "Authorization: Bearer YOUR_API_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "url": "https://api.example.com/cron/v2/sync",
    "method": "POST",
    "intervalSeconds": 120,
    "headers": {
      "Authorization": "Bearer new_token",
      "Content-Type": "application/json"
    },
    "body": "{\"v\":2}"
  }'
```

---

### ⏳ 4.7. Gia hạn Cronjob
Gia hạn thêm số tháng sử dụng dịch vụ cronjob. Tiền gia hạn được trừ trực tiếp vào số dư ví của tài khoản API và tự động cộng dồn vào thời hạn hết hạn (`expiresAt`).

- **HTTP Method:** `POST`
- **Endpoint:** `/{id}/extend` (hoặc `POST /api/agency/cronjobs/extend` với `{ "id": "...", "month": 1 }` trong Body)
- **Header:**
  - `Authorization: Bearer <API_TOKEN>`
  - `Content-Type: application/json`

#### 📝 Ví dụ cURL
```bash
curl -X POST "https://thegioidev.com/api/agency/cronjobs/65f9876543210fedcba54321/extend" \
  -H "Authorization: Bearer YOUR_API_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "month": 1
  }'
```

####  Response thành công (200 OK)
```json
{
  "error": 0,
  "message": "Gia hạn cronjob thành công thêm 1 tháng",
  "data": {
    "id": "65f9876543210fedcba54321",
    "additionalMonths": 1,
    "payment": 30000,
    "expiresAt": "2026-12-01T14:30:00.000Z",
    "status": "active"
  }
}
```

---

### 🗑️ 4.8. Xóa Cronjob
Xóa vĩnh viễn cronjob khỏi hệ thống. Chỉ được phép xóa khi cronjob đang ở trạng thái Tạm dừng (`paused`) hoặc Đã hết hạn (`expired`).

- **HTTP Method:** `DELETE`
- **Endpoint:** `/{id}`
- **Header:** `Authorization: Bearer <API_TOKEN>`

#### 📝 Ví dụ cURL
```bash
curl -X DELETE "https://thegioidev.com/api/agency/cronjobs/65f9876543210fedcba54321" \
  -H "Authorization: Bearer YOUR_API_TOKEN"
```

---

## 🌐 5. WEBSITE MARKETPLACE API (DỊCH VỤ THUÊ WEBSITE THEGIOIDEV)

**Base URL:** `https://thegioidev.com/api/agency/websites`

TheGioiDev cung cấp giải pháp thuê website mẫu chuyên nghiệp trọn gói theo tháng (1 đến 36 tháng). Hệ thống hỗ trợ 2 chế độ triển khai:
- **Tự động (Auto-Deploy):** Tự động khởi tạo Cloudflare Zone, cấp cặp Nameserver cố định, cấu hình Vhost máy chủ aaPanel, tạo MySQL Database ngẫu nhiên, giải nén bộ mã nguồn và import dữ liệu mẫu `.sql`.
- **Thủ công (Manual):** Dành cho các mẫu đặc thù, quản trị viên tiếp nhận và cấu hình riêng biệt.

> [!IMPORTANT]
> Tất cả các endpoint trong WEBSITE MARKETPLACE API yêu cầu gửi Header chuẩn:
> ```http
> Authorization: Bearer <API_TOKEN>
> Content-Type: application/json
> Accept: application/json
> ```

---

### 📦 5.1. Lấy danh sách mẫu website mở bán
Lấy danh sách các mẫu website đang hoạt động trên hệ thống. Hỗ trợ tìm kiếm từ khóa, phân trang, lọc theo danh mục và lấy kèm siêu dữ liệu (`categories`, `tlds`).

> [!WARNING]
> **ĐẶC BIỆT LƯU Ý VỀ CẤU TRÚC GIÁ (TRÁNH LỖI 0đ HOẶC GIÁ CỨNG 150.000đ):**
> 1. Endpoint danh sách `/` **KHÔNG** trả về mảng `pricing`! Thay vào đó, API trả về trường **`minPricePerMonth`** (giá khởi điểm thấp nhất mỗi tháng của mẫu) và **`durationOptions`** (mảng các mốc tháng cho phép mua, ví dụ `[1, 3, 6, 12]`).
> 2. Khi hiển thị danh sách mẫu (thẻ card bên ngoài), dùng trường **`minPricePerMonth`** để tính giá hiển thị:
>    `Giá hiển thị = minPricePerMonth * (1 + % markup / 100)`.
> 3. Để lấy bảng giá chi tiết từng mốc tháng và giá gia hạn `priceExtendPerMonth`, hệ thống phải gọi endpoint chi tiết **`GET /{slug}`** (xem Mục 5.2).

- **HTTP Method:** `GET`
- **Endpoint:** `/`
- **Query Params:**
  - `page`: Trang hiện tại (Mặc định: `1`)
  - `limit`: Số bản ghi mỗi trang (Mặc định: `20`, tối đa: `50`)
  - `search`: Từ khóa tìm kiếm theo tên mẫu (Tùy chọn)
  - `categoryId`: Lọc theo ID danh mục (Tùy chọn)
  - `sortField`: Trường sắp xếp (`stt`, `salesCount`, `view`, `createdAt`, mặc định: `stt`)
  - `sortDir`: Thứ tự (`asc` hoặc `desc`, mặc định: `asc`)
  - `withMeta`: `true` nếu muốn lấy kèm danh sách categories và tlds (Tùy chọn)
- **Header:** `Authorization: Bearer <API_TOKEN>`

#### 📝 Ví dụ cURL
```bash
curl -X GET "https://thegioidev.com/api/agency/websites?page=1&limit=20&withMeta=true" \
  -H "Authorization: Bearer YOUR_API_TOKEN" \
  -H "Accept: application/json"
```

####  Response thành công thực tế (200 OK)
```json
{
  "error": 0,
  "data": {
    "items": [
      {
        "id": "6ac068fe3bffca8674153eb3",
        "serviceName": "Web cho thuê cronjob qua api",
        "slug": "web-cho-thue-cronjob-qua-api",
        "image": "https://pub-dfb2d41f5cb24bcb9c7a53428832a65a.r2.dev/websites/2782c4aa6900efdc9db439b98cfd475b.png",
        "salesCount": 12,
        "view": 150,
        "minPricePerMonth": 20000,
        "durationOptions": [1, 3, 6, 12],
        "category": {
          "id": "6a87f85b5d27313821967b57",
          "name": "Code khác",
          "slug": "code-khac"
        },
        "rank": 1
      }
    ],
    "pagination": {
      "page": 1,
      "limit": 20,
      "total": 35,
      "totalPages": 2
    }
  },
  "meta": {
    "requestId": "req_0ea0cacd1f94487097d55b3f",
    "timestamp": "2026-10-03T04:46:33.279Z"
  }
}
```

---

### 🔍 5.2. Lấy chi tiết mẫu website theo Slug
Lấy toàn bộ thông tin chi tiết của một mẫu website cụ thể bằng `slug`, bao gồm mô tả chi tiết, hình ảnh chụp thực tế (`listImage`), tính năng, chế độ triển khai (`deployMode`: auto/manual) và bảng giá chi tiết theo từng mốc tháng (**`pricings`**).

> [!IMPORTANT]
> **TÊN TRƯỜNG BẢNG GIÁ LÀ `pricings` (CÓ CHỮ 's' Ở CUỐI):**
> Trong response chi tiết, trường bảng giá là **`pricings`** (Array). Tuyệt đối không viết là `pricing` (không có 's') sẽ bị lỗi `undefined` hoặc `null`.
> Mỗi phần tử trong `pricings` gồm:
> - `durationMonths`: Số tháng của gói (Ví dụ: `1`, `3`, `6`, `12`...)
> - `pricePerMonth`: Giá thuê mỗi tháng
> - `priceExtendPerMonth`: Giá gia hạn mỗi tháng của gói này (Chính là mức phí gia hạn `priceExtend`)
> - `totalPrice`: Tổng chi phí gốc của mốc thời hạn này

- **HTTP Method:** `GET`
- **Endpoint:** `/{slug}`
- **Header:** `Authorization: Bearer <API_TOKEN>`

#### 📝 Ví dụ cURL
```bash
curl -X GET "https://thegioidev.com/api/agency/websites/web-cho-thue-cronjob-qua-api" \
  -H "Authorization: Bearer YOUR_API_TOKEN" \
  -H "Accept: application/json"
```

####  Response chi tiết thực tế (200 OK)
```json
{
  "error": 0,
  "data": {
    "id": "6ac068fe3bffca8674153eb3",
    "serviceName": "Web cho thuê cronjob qua api",
    "slug": "web-cho-thue-cronjob-qua-api",
    "image": "https://pub-dfb2d41f5cb24bcb9c7a53428832a65a.r2.dev/websites/2782c4aa6900efdc9db439b98cfd475b.png",
    "listImage": [
      "https://pub-dfb2d41f5cb24bcb9c7a53428832a65a.r2.dev/websites/e0345919153e33356fda777ed5620d10.png"
    ],
    "description": "Mã nguồn website cho thuê cronjob tự động qua API, tích hợp nạp tiền tự động...",
    "demoUrl": "https://cronjob.vn",
    "adminDemoUrl": null,
    "featuresList": [
      "Tự động tạo cronjob qua API",
      "Quản lý lịch trình giây/phút",
      "Giao diện chuẩn Responsive"
    ],
    "salesCount": 12,
    "view": 150,
    "category": {
      "id": "6a87f85b5d27313821967b57",
      "name": "Code khác",
      "slug": "code-khac"
    },
    "deployMode": "auto",
    "pricings": [
      {
        "durationMonths": 1,
        "pricePerMonth": 20000,
        "priceExtendPerMonth": 15000,
        "totalPrice": 20000
      },
      {
        "durationMonths": 3,
        "pricePerMonth": 18000,
        "priceExtendPerMonth": 15000,
        "totalPrice": 54000
      },
      {
        "durationMonths": 12,
        "pricePerMonth": 15000,
        "priceExtendPerMonth": 12000,
        "totalPrice": 180000
      }
    ],
    "createdAt": "2026-10-03T02:31:26.446Z"
  },
  "meta": {
    "requestId": "req_249738e8aac345d1be2ef650",
    "timestamp": "2026-10-03T04:46:42.680Z"
  }
}
```

---

### 🛒 5.3. Đặt thuê website mới
Tạo đơn thuê website và trừ tiền trực tiếp vào số dư ví tài khoản đại lý. Hệ thống hỗ trợ cả việc sử dụng tên miền có sẵn lẫn mua tên miền mới tự động.

- **HTTP Method:** `POST`
- **Endpoint:** `/buy`
- **Header:** `Authorization: Bearer <API_TOKEN>`, `Content-Type: application/json`

#### 📥 Request Body (JSON)
| Tham số | Kiểu | Bắt buộc | Mô tả |
| :--- | :--- | :--- | :--- |
| `serviceId` | String | **Có** | ID (ObjectId) hoặc `slug` của mẫu website cần thuê |
| `domainName` | String | **Có** | Tên miền khởi tạo website (Ví dụ: `myshop.com`, chấp nhận alias `domain`) |
| `durationMonths` | Integer | Không | Số tháng thuê từ `1` đến `36` (Mặc định: `1`, chấp nhận alias `month`) |
| `domainNew` | Boolean | Không | `true` nếu mua kèm tên miền mới tự động, `false` nếu dùng tên miền có sẵn (Mặc định: `false`) |
| `domainTldId` | String | Tùy chọn | ID đuôi tên miền (Bắt buộc nếu `domainNew: true`) |
| `shopAccount` | String | Không | Tài khoản admin shop mong muốn (chấp nhận alias `username`) |
| `shopPassword` | String | Không | Mật khẩu admin shop mong muốn (chấp nhận alias `password`) |
| `couponCode` | String | Không | Mã giảm giá áp dụng (nếu có, chấp nhận alias `coupon`) |

#### 📝 Ví dụ cURL
```bash
curl -X POST "https://thegioidev.com/api/agency/websites/buy" \
  -H "Authorization: Bearer YOUR_API_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "serviceId": "shop-pro",
    "domainName": "myshop.com",
    "durationMonths": 1,
    "shopAccount": "admin",
    "shopPassword": "SecurePassword#2026"
  }'
```

####  Response mua thành công (200 OK)
```json
{
  "error": 0,
  "message": "Đặt thuê website thành công",
  "data": {
    "orderId": "65f1234567890abcdef12345",
    "serviceName": "Mẫu Bán Hàng E-Commerce Pro",
    "domainName": "myshop.com",
    "domainTldName": null,
    "durationMonths": 1,
    "total": 150000,
    "status": "pending"
  }
}
```

> [!TIP]
> **QUY TRÌNH TIẾP THEO SAU KHI BUY THÀNH CÔNG:**
> 1. Lưu `orderId` vào bảng cơ sở dữ liệu của bạn.
> 2. Gọi ngay `GET /orders/{orderId}` (Mục 5.6) để lấy chi tiết đơn hàng, bao gồm cặp Nameserver Cloudflare (`nameServers`) và mức phí gia hạn (`priceExtend`).
> 3. Hiển thị cặp Nameserver cho khách hàng cập nhật tại nhà quản lý tên miền (hoặc tự động cập nhật qua API nhà cung cấp tên miền nếu mua kèm tên miền mới).

---

### 🔄 5.4. Gia hạn website
Gia hạn thời gian sử dụng của đơn hàng website.

> [!IMPORTANT]
> **QUY TẮC TÍNH PHÍ GIA HẠN WEBSITE CHUẨN XÁC:**
> - Chi phí gia hạn được tính theo mức phí **`priceExtend`** đã lưu trong chi tiết đơn hàng (`data.priceExtend`), hoặc theo `priceExtendPerMonth` từ bảng giá gói thuê, nhân với số tháng gia hạn thêm:  
>   `Tổng tiền gia hạn = (priceExtend * (1 + % markup / 100)) * số tháng`.
> - **CẢNH BÁO NGUY HIỂM:** Nếu đơn hàng ban đầu có mua kèm tên miền mới (ví dụ 250k tiền miền + 24k tiền web = 274k), **TUYỆT ĐỐI KHÔNG** chia tổng tiền đơn hàng ban đầu cho số tháng để tính phí gia hạn website! Vì như vậy khách sẽ bị thu lại tiền đăng ký tên miền (274k thay vì 24k). Luôn luôn tách riêng chi phí website và chi phí tên miền!

- **HTTP Method:** `POST`
- **Endpoint:** `/renew` *(hoặc `/orders/{id}/renew`)*
- **Header:** `Authorization: Bearer <API_TOKEN>`, `Content-Type: application/json`

#### 📥 Request Body (JSON)
| Tham số | Kiểu | Bắt buộc | Mô tả |
| :--- | :--- | :--- | :--- |
| `id` | String | **Có** | ID đơn hàng website cần gia hạn (chấp nhận alias `orderId` hoặc truyền qua URL `/orders/{id}/renew`) |
| `month` | Integer | Không | Số tháng gia hạn thêm từ `1` đến `36` (Mặc định: `1`, chấp nhận alias `months`) |

#### 📝 Ví dụ cURL
```bash
curl -X POST "https://thegioidev.com/api/agency/websites/renew" \
  -H "Authorization: Bearer YOUR_API_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "id": "6a918b803bf9f6f522f7b28e",
    "orderId": "6a918b803bf9f6f522f7b28e",
    "month": 1,
    "months": 1
  }'
```

####  Response gia hạn thành công thực tế (200 OK)
```json
{
  "error": 0,
  "message": "Gia hạn website thành công thêm 1 tháng",
  "data": {
    "orderId": "6a918b803bf9f6f522f7b28e",
    "serviceName": "Website bán key game",
    "domainName": "soikeonro.com",
    "additionalMonths": 1,
    "payment": 50000,
    "newExpired": 1795000000,
    "expiredDate": "2026-11-06T12:00:00.000Z",
    "status": "active"
  }
}
```

---

### 📋 5.5. Danh sách đơn hàng website đã thuê
Lấy danh sách các đơn hàng website thuộc tài khoản đại lý. Dữ liệu trả về đầy đủ phí gia hạn `priceExtend`, ngày hết hạn `expired` (timestamp), thông tin tài khoản đăng nhập admin và cặp Nameserver Cloudflare để khách hàng trỏ domain.

- **HTTP Method:** `GET`
- **Endpoint:** `/orders`
- **Query Params:**
  - `page`: Trang hiện tại (Mặc định: `1`)
  - `limit`: Số bản ghi mỗi trang (Mặc định: `20`, tối đa: `100`)
  - `search`: Tìm theo tên mẫu hoặc tên miền (Tùy chọn)
  - `status`: Lọc theo trạng thái (`pending`, `cf_pending`, `domain_buying`, `ns_pending`, `processing`, `active`, `failed`, `expired`, `suspended`)
- **Header:** `Authorization: Bearer <API_TOKEN>`

#### 📝 Ví dụ cURL
```bash
curl -X GET "https://thegioidev.com/api/agency/websites/orders?page=1&limit=20" \
  -H "Authorization: Bearer YOUR_API_TOKEN"
```

####  Response danh sách đơn hàng thực tế (200 OK)
```json
{
  "error": 0,
  "data": {
    "items": [
      {
        "id": "6a918b803bf9f6f522f7b28e",
        "serviceId": "6a257376ee4c0da5e0c9303f",
        "serviceName": "Website bán key game",
        "serviceSlug": "website-ban-key-game",
        "serviceImage": "https://pub-dfb2d41f5cb24bcb9c7a53428832a65a.r2.dev/websites/ec401d23304fc549b5bebb0621108e2a.png",
        "domainName": "soikeonro.com",
        "domainNew": false,
        "domainTldName": null,
        "domainTldPrice": 0,
        "couponCode": null,
        "couponDiscount": 0,
        "couponDiscountAmount": 0,
        "month": 1,
        "total": 50000,
        "amount": 50000,
        "priceExtend": 50000,
        "status": "expired",
        "expired": 1790515357,
        "adminNote": "Website đã hết hạn và tự động tạm dừng",
        "shopAccount": "admin",
        "shopPassword": "SecurePassword#2026",
        "nameServers": [
          "aldo.ns.cloudflare.com",
          "anahi.ns.cloudflare.com"
        ],
        "createdAt": "2026-08-28T13:22:08.994Z"
      }
    ],
    "pagination": {
      "page": 1,
      "limit": 20,
      "total": 6,
      "totalPages": 1
    }
  },
  "meta": {
    "requestId": "req_c50f21bcddc4429a8bad9fc9",
    "timestamp": "2026-10-03T04:34:33.764Z"
  }
}
```

---

### 📌 5.6. Chi tiết đơn hàng website
Lấy thông tin chi tiết một đơn hàng website, bao gồm mức phí gia hạn `priceExtend`, ngày hết hạn `expired` (timestamp), thông tin tài khoản đăng nhập admin, cặp Nameserver Cloudflare và nhật ký trạng thái deploy từng bước.

- **HTTP Method:** `GET`
- **Endpoint:** `/orders/{id}`
- **Path Params:** `id` (ID đơn hàng)
- **Header:** `Authorization: Bearer <API_TOKEN>`

#### 📝 Ví dụ cURL
```bash
curl -X GET "https://thegioidev.com/api/agency/websites/orders/6a918b803bf9f6f522f7b28e" \
  -H "Authorization: Bearer YOUR_API_TOKEN"
```

####  Response chi tiết đơn hàng thực tế (200 OK)
```json
{
  "error": 0,
  "data": {
    "id": "6a918b803bf9f6f522f7b28e",
    "serviceName": "Website bán key game",
    "serviceSlug": "website-ban-key-game",
    "serviceImage": "https://pub-dfb2d41f5cb24bcb9c7a53428832a65a.r2.dev/websites/ec401d23304fc549b5bebb0621108e2a.png",
    "domainName": "soikeonro.com",
    "month": 1,
    "total": 50000,
    "amount": 50000,
    "priceExtend": 50000,
    "status": "expired",
    "expired": 1790515357,
    "adminNote": "Website đã hết hạn và tự động tạm dừng",
    "shopAccount": "admin",
    "shopPassword": "SecurePassword#2026",
    "nameServers": [
      "aldo.ns.cloudflare.com",
      "anahi.ns.cloudflare.com"
    ],
    "createdAt": "2026-08-28T13:22:08.994Z",
    "updatedAt": "2026-09-27T13:24:24.344Z",
    "provisioning": {
      "orderId": "6a918b803bf9f6f522f7b28e",
      "domain": "soikeonro.com",
      "status": "expired",
      "nameServers": [
        "aldo.ns.cloudflare.com",
        "anahi.ns.cloudflare.com"
      ],
      "cloudflareNameServers": [
        "aldo.ns.cloudflare.com",
        "anahi.ns.cloudflare.com"
      ],
      "zoneId": "efb5b45aeac3ca4be017e890db5f7003",
      "stepLog": [
        { "step": "init", "status": "ok", "at": "2026-08-28T13:22:10.659Z", "note": "Worker bắt đầu xử lý" },
        { "step": "aapanel_site", "status": "ok", "at": "2026-08-28T13:22:12.741Z", "note": "site soikeonro.com + db wsoikeon_fa69" },
        { "step": "cloudflare", "status": "ok", "at": "2026-08-28T13:22:17.284Z", "note": "A record @ → 163.61.110.21" },
        { "step": "source", "status": "ok", "at": "2026-08-28T13:22:28.532Z", "note": "Giải nén source hoàn tất" },
        { "step": "config_db", "status": "ok", "at": "2026-08-28T13:22:28.573Z", "note": "Cấu hình DB thành công" },
        { "step": "sql", "status": "ok", "at": "2026-08-28T13:22:35.687Z", "note": "Import SQL mẫu thành công" },
        { "step": "ssl", "status": "ok", "at": "2026-08-28T13:22:37.178Z", "note": "Cài đặt chứng chỉ SSL" },
        { "step": "done", "status": "ok", "at": "2026-08-28T13:22:37.336Z", "note": "Website đã sẵn sàng" }
      ]
    }
  },
  "meta": {
    "requestId": "req_065de1b4a9e14fa48d3e7b09",
    "timestamp": "2026-10-03T04:37:56.266Z"
  }
}
```

---

### 🌐 5.7. Kích hoạt kiểm tra NS & Deploy (Check NS)
Sau khi khách hàng đã cập nhật Nameserver của tên miền về cặp Nameserver Cloudflare được cấp, gọi endpoint này để hệ thống kiểm tra và tự động triển khai mã nguồn trên máy chủ. Endpoint có tính idempotent — có thể gọi lại an toàn.

- **HTTP Method:** `POST`
- **Endpoint:** `/orders/{id}/check-ns`
- **Path Params:** `id` (ID đơn hàng)
- **Header:** `Authorization: Bearer <API_TOKEN>`

#### 📝 Ví dụ cURL
```bash
curl -X POST "https://thegioidev.com/api/agency/websites/orders/65f1234567890abcdef12345/check-ns" \
  -H "Authorization: Bearer YOUR_API_TOKEN"
```

---

### 🔁 5.8. Thử lại triển khai website (Retry Deploy)
Trong trường hợp quá trình tự động tạo website bị gián đoạn, kẹt hoặc báo lỗi (`failed`), gọi endpoint này để đưa đơn hàng trở lại hàng đợi background worker. **Tuyệt đối không trừ tiền lần hai.**

- **HTTP Method:** `POST`
- **Endpoint:** `/orders/{id}/retry` *(hoặc `/retry` với body `{"id": "..."}`)*
- **Path Params:** `id` (ID đơn hàng)
- **Header:** `Authorization: Bearer <API_TOKEN>`

#### 📝 Ví dụ cURL
```bash
curl -X POST "https://thegioidev.com/api/agency/websites/orders/65f1234567890abcdef12345/retry" \
  -H "Authorization: Bearer YOUR_API_TOKEN"
```

---

## 🛠️ 6. CODE MẪU PHP CHUẨN (CLASS WRAPPER SẴN SÀNG COPY & PASTE)

Dưới đây là mã nguồn Class PHP hoàn chỉnh đã được kiểm tra thực tế, tích hợp toàn bộ các dịch vụ **Hosting**, **Cronjob** và **Website**:

```php
<?php
/**
 * Class TheGioiDevApi - Chuẩn kết nối API TheGioiDev 2026
 */
class TheGioiDevApi
{
    private $cronBaseUrl = 'https://thegioidev.com/api/agency/cronjobs';
    private $hostingBaseUrl = 'https://thegioidev.com/api/v1/hosting';
    private $websiteBaseUrl = 'https://thegioidev.com/api/agency/websites';
    private $apiToken;

    public function __construct($apiToken = '')
    {
        $this->setToken($apiToken);
    }

    public function setToken($token)
    {
        // Tự động strip bỏ khoảng trắng và tiền tố Bearer thừa
        $this->apiToken = preg_replace('/^Bearer\s+/i', '', trim((string)$token));
    }

    public function getToken()
    {
        return $this->apiToken;
    }

    /**
     * Gửi HTTP Request tới TheGioiDev qua cURL
     */
    public function request($url, $method = 'GET', $data = [], $customToken = null)
    {
        $token = $customToken ? preg_replace('/^Bearer\s+/i', '', trim((string)$customToken)) : $this->apiToken;

        if (empty($token)) {
            return [
                'error'  => 1,
                'status' => 'error',
                'msg'    => 'Chưa cấu hình API Token TheGioiDev!',
                'code'   => 401
            ];
        }

        $ch = curl_init();
        $requestUrl = $url;

        if ($method === 'GET' && !empty($data)) {
            $queryString = http_build_query($data);
            $requestUrl .= (strpos($requestUrl, '?') === false ? '?' : '&') . $queryString;
        }

        // CHỈ GỬI DUY NHẤT Authorization: Bearer <token>
        // TUYỆT ĐỐI KHÔNG thêm auth-token hay api-key để tránh lỗi 401 session
        $headers = [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json',
            'Accept: application/json',
            'User-Agent: CMSNT-TheGioiDev-Client/2.0'
        ];

        curl_setopt($ch, CURLOPT_URL, $requestUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, is_string($data) ? $data : json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        } elseif ($method === 'PATCH' || $method === 'PUT' || $method === 'DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            if (!empty($data)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, is_string($data) ? $data : json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            }
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr  = curl_error($ch);
        curl_close($ch);

        if ($curlErr) {
            return [
                'error'  => 1,
                'status' => 'error',
                'msg'    => 'Không thể kết nối đến TheGioiDev: ' . $curlErr,
                'code'   => 500
            ];
        }

        $result = json_decode($response, true);
        if ($result === null) {
            return [
                'error'  => 1,
                'status' => 'error',
                'msg'    => 'Phản hồi không đúng định dạng JSON (Mã HTTP: ' . $httpCode . ')',
                'code'   => $httpCode,
                'raw'    => $response
            ];
        }

        return $result;
    }

    // ==================== CÁC PHƯƠNG THỨC WEBSITE ====================

    public function getWebsites($params = [], $token = null)
    {
        return $this->request($this->websiteBaseUrl, 'GET', $params, $token);
    }

    public function getWebsiteDetail($slug, $token = null)
    {
        return $this->request($this->websiteBaseUrl . '/' . urlencode($slug), 'GET', [], $token);
    }

    public function buyWebsite($data, $token = null)
    {
        return $this->request($this->websiteBaseUrl . '/buy', 'POST', $data, $token);
    }

    public function renewWebsite($orderId, $months = 1, $token = null)
    {
        $months = max(1, (int)$months);
        return $this->request($this->websiteBaseUrl . '/renew', 'POST', [
            'id'       => (string)$orderId,
            'orderId'  => (string)$orderId,
            'month'    => $months,
            'months'   => $months
        ], $token);
    }

    /**
     * Bóc tách bảng giá và giá hiển thị chuẩn xác (hỗ trợ cả danh sách và chi tiết)
     */
    public static function extractPricing($item, $markupPercent = 0)
    {
        $pricings = [];
        $displayPrice = 0;

        // 1. Nếu là chi tiết mẫu (có trường 'pricings' từ GET /{slug})
        if (!empty($item['pricings']) && is_array($item['pricings'])) {
            foreach ($item['pricings'] as $p) {
                $origPricePerMonth = (int)($p['pricePerMonth'] ?? 0);
                $origPriceExtend = (int)($p['priceExtendPerMonth'] ?? $origPricePerMonth);
                $duration = (int)($p['durationMonths'] ?? 1);

                $userPricePerMonth = (int)round($origPricePerMonth * (1 + $markupPercent / 100));
                $userPriceExtend = (int)round($origPriceExtend * (1 + $markupPercent / 100));
                $userTotalPrice = $userPricePerMonth * $duration;

                $pricings[] = [
                    'durationMonths' => $duration,
                    'pricePerMonth' => $userPricePerMonth,
                    'priceExtendPerMonth' => $userPriceExtend,
                    'totalPrice' => $userTotalPrice,
                    'originalPricePerMonth' => $origPricePerMonth,
                    'originalPriceExtend' => $origPriceExtend
                ];

                if ($duration === 1 || $displayPrice === 0) {
                    $displayPrice = $userPricePerMonth;
                }
            }
        }

        // 2. Nếu là danh sách mẫu (có 'minPricePerMonth' và 'durationOptions' từ GET /)
        if (empty($pricings)) {
            $baseMin = (int)($item['minPricePerMonth'] ?? 0);
            if ($baseMin > 0) {
                $displayPrice = (int)round($baseMin * (1 + $markupPercent / 100));
                $durations = !empty($item['durationOptions']) && is_array($item['durationOptions']) ? $item['durationOptions'] : [1];
                foreach ($durations as $d) {
                    $pricings[] = [
                        'durationMonths' => (int)$d,
                        'pricePerMonth' => $displayPrice,
                        'priceExtendPerMonth' => $displayPrice,
                        'totalPrice' => $displayPrice * (int)$d,
                        'originalPricePerMonth' => $baseMin,
                        'originalPriceExtend' => $baseMin
                    ];
                }
            }
        }

        return [
            'displayPrice' => $displayPrice,
            'pricing' => $pricings
        ];
    }

    public function getWebsiteOrders($params = [], $token = null)
    {
        return $this->request($this->websiteBaseUrl . '/orders', 'GET', $params, $token);
    }

    public function getWebsiteOrderDetail($orderId, $token = null)
    {
        return $this->request($this->websiteBaseUrl . '/orders/' . urlencode($orderId), 'GET', [], $token);
    }

    public function checkWebsiteNs($orderId, $token = null)
    {
        return $this->request($this->websiteBaseUrl . '/orders/' . urlencode($orderId) . '/check-ns', 'POST', [], $token);
    }

    public function retryWebsiteDeploy($orderId, $token = null)
    {
        return $this->request($this->websiteBaseUrl . '/orders/' . urlencode($orderId) . '/retry', 'POST', [], $token);
    }

    // ==================== CÁC PHƯƠNG THỨC CRONJOB ====================

    public function getServers($token = null)
    {
        return $this->request($this->cronBaseUrl . '/servers', 'GET', [], $token);
    }

    public function buyCronjob($data, $token = null)
    {
        return $this->request($this->cronBaseUrl . '/buy', 'POST', $data, $token);
    }

    public function getCronjobs($params = [], $token = null)
    {
        return $this->request($this->cronBaseUrl, 'GET', $params, $token);
    }

    public function getCronjobDetail($id, $token = null)
    {
        return $this->request($this->cronBaseUrl . '/' . urlencode($id), 'GET', [], $token);
    }

    public function toggleCronjob($id, $token = null)
    {
        return $this->request($this->cronBaseUrl . '/' . urlencode($id) . '/toggle', 'POST', [], $token);
    }

    public function editCronjob($id, $data, $token = null)
    {
        return $this->request($this->cronBaseUrl . '/' . urlencode($id), 'PATCH', $data, $token);
    }

    public function extendCronjob($id, $months = 1, $token = null)
    {
        return $this->request($this->cronBaseUrl . '/' . urlencode($id) . '/extend', 'POST', ['month' => $months], $token);
    }

    public function deleteCronjob($id, $token = null)
    {
        return $this->request($this->cronBaseUrl . '/' . urlencode($id), 'DELETE', [], $token);
    }

    // ==================== CÁC PHƯƠNG THỨC HOSTING ====================

    public function getHostingPackages($params = [], $token = null)
    {
        return $this->request($this->hostingBaseUrl . '/packages', 'GET', $params, $token);
    }

    public function buyHosting($data, $token = null)
    {
        return $this->request($this->hostingBaseUrl . '/buy', 'POST', $data, $token);
    }

    public function renewHosting($id, $month = 1, $token = null)
    {
        return $this->request($this->hostingBaseUrl . '/renew', 'POST', ['id' => $id, 'month' => $month], $token);
    }
}
```

---

## ❌ 7. MÃ LỖI THƯỜNG GẶP (HTTP STATUS CODES)

Khi có lỗi xảy ra, hệ thống TheGioiDev sẽ trả về mã HTTP Status Code tương ứng đi kèm nội dung JSON giải thích chi tiết:

### Bảng tra cứu mã lỗi:

| HTTP Code | Mô tả lỗi | Nguyên nhân & Hướng khắc phục |
| :--- | :--- | :--- |
| `400 Bad Request` | Yêu cầu không hợp lệ | Thiếu các tham số bắt buộc hoặc dữ liệu JSON gửi lên sai cấu trúc. |
| `401 Unauthorized` | Không có quyền truy cập / Hết hạn | 1. API Token chưa nhập hoặc không tồn tại.<br>2. **Bị lỗi do gửi kèm header `auth-token`** (chỉ dùng `Authorization: Bearer`).<br>3. Bị dính chữ `Bearer Bearer` do người dùng copy nhầm. |
| `402 Payment Required` | Số dư không đủ | Số dư trong ví tài khoản TheGioiDev nhỏ hơn chi phí thanh toán đơn hàng. Cần nạp thêm tiền tại [thegioidev.com](https://thegioidev.com). |
| `403 Forbidden` | Bị từ chối truy cập | Gói dịch vụ đã hết hạn hoặc bị tạm khóa do vi phạm chính sách sử dụng. |
| `404 Not Found` | Không tìm thấy tài nguyên | Đơn hàng, Gói dịch vụ hoặc địa chỉ API không tồn tại. |
| `405 Method Not Allowed`| Sai phương thức HTTP | Gửi yêu cầu sai method (ví dụ: dùng GET cho API yêu cầu POST). |
| `429 Too Many Requests` | Vượt giới hạn tần suất | Vượt quá 60 requests / phút. Cần giảm tần suất gửi yêu cầu hoặc gắn cache. |
| `500 Internal Error` | Lỗi máy chủ | Lỗi phát sinh từ hệ thống hoặc cổng kết nối của máy chủ máy chủ. |

#### 📝 Cấu trúc Response lỗi mẫu (402 Payment Required)
```json
{
  "status": "error",
  "msg": "Số dư của bạn không đủ để thanh toán",
  "code": 402,
  "required": 150000,
  "balance": 35000
}
```

---

💡 **Cần hỗ trợ kỹ thuật?** Vui lòng liên hệ với bộ phận hỗ trợ qua Livechat trên website [https://thegioidev.com](https://thegioidev.com) hoặc gửi Ticket hỗ trợ tại trang Quản trị để được xử lý nhanh nhất!

