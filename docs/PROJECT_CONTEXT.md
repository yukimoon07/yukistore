# PROJECT_CONTEXT.md — ShopGame Production Upgrade

*Cập nhật: 13/11/2026. Đọc file này + TASKS.md đầu mỗi phiên làm việc.*

## 1. Dự án là gì

Website **ShopGame** — Laravel 11 (PHP 8.2), Blade + Tailwind + Bootstrap template (cmsbvq), MySQL, session file, Breeze auth, Sanctum. Bán tài khoản game, dịch vụ cày thuê, vượt link, tool, cloud phone. Đang chạy local (Laragon, Windows), chuẩn bị đưa lên production public.

- Workspace: `C:\Users\Le Dat\Downloads\shopgame`
- DB dump gốc: `shopgame.sql` (root). Dump 11/2026: game_accounts 1, game_categories 2, game_groups 2, random_category_accounts ~1678, configs 6, random_categories 1.
- Admin Panel đã có sẵn đầy đủ (không xây mới): Dashboard, GameAccounts, Categories, RandomAccounts, Services, Packages, Users, Transactions, BankAccounts, DiscountCodes, FlashSale, News, LuckyWheel, Affiliate, Missions, Config, ActivityLogs, Roles.
- Roles 3 cấp: member / dev / admin (đã làm ở sprint trước).

## 2. Nhiệm vụ hiện tại

Chạy nâng cấp theo Master Prompt (8 phase, output docs/phase-b/). Mục tiêu cuối: release package sạch deploy được lên domain thật — không localhost, không secret, không dữ liệu demo, UI Premium Dark.

## 3. Hạn chế môi trường làm việc

- Sandbox Linux KHÔNG có PHP, KHÔNG cài được (proxy chặn). → Kiểm tra cú pháp bằng script Python (`/tmp/phpsyntax.py`, `/tmp/bladecheck.py`) + đọc code.
- User tự chạy migrate/seed/serve trên máy qua các file `CHAY_*.bat`.
- Xóa file trong workspace phải bật quyền qua tool `allow_cowork_file_delete` (đã bật cho folder shopgame).
- `rm -rf __MACOSX` treo nếu xóa cả cây — xóa từng file/con-dir trước, sau đó rmdir.
- `du`/`find` trên thư mục lớn (vendor 68M, public 58M) có thể timeout 120s — giới hạn phạm vi find.

## 4. Trạng thái DB/migrations (quan trọng)

- Sprint trước đã tạo 9 migrations mới (business_units, products, product_logs, inventory cols, account logs, admin_activity_logs, payment_methods, 2FA cols, dev role) — **CHƯA chạy migrate**. User cần chạy `CHAY_MIGRATE.bat`.
- `accounts:reset-inventory` — command reset 4 bảng kho có dry-run + backup.
- `CHAY_TAO_ADMIN.bat` giờ gọi `admin:create-admin` (mới, an toàn — xem DECISIONS.md D5).

## 5. File cần đọc đầu tiên khi tiếp tục

1. `docs/PROJECT_CONTEXT.md` (file này)
2. `docs/TASKS.md`
3. `docs/DECISIONS.md`
4. Báo cáo phase: `docs/phase-b/PHASE_1_3_REPORT.md`
5. Code: kiểm tra trực tiếp trước khi sửa — không tin mô tả.
