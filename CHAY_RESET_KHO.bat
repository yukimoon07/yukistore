@echo off
chcp 65001 >nul
title SHOPGAME - RESET KHO TAI KHOAN (CAN XAC NHAN RIENG)
cd /d "%~dp0"

set PHP="C:\laragon\bin\php\php-8.3.36-Win32-vs16-x64\php.exe"
if not exist %PHP% (
    echo [LOI] Khong tim thay PHP tai %PHP%
    pause
    exit /b 1
)

echo ========================================================
echo   RESET KHO TAI KHOAN GAME - THAO TAC XOA DU LIEU
echo ========================================================
echo.
echo Lenh nay se xoa toan bo du lieu trong 4 bang:
echo   - game_accounts (kho tai khoan chinh)
echo   - game_categories (danh muc tai khoan chinh)
echo   - random_categories (danh muc tai khoan random)
echo   - random_category_accounts (kho tai khoan random)
echo.
echo GIU NGUYEN: users, game_groups, don hang, lich su giao dich,
echo cau hinh thanh toan, admin, payment_methods...
echo.
echo Quy trinh tu dong: xem so lieu -^> backup 4 bang -^> xac nhan -^> xoa.
echo.
echo Go dry-run de CHI XEM so lieu, chua xoa gi.
echo.
set /p MODE="Nhap dry-run (xem truoc) hoac RESET (thuc hien): "

if /i "%MODE%"=="dry-run" (
    %PHP% artisan accounts:reset-inventory --dry-run
) else if /i "%MODE%"=="RESET" (
    %PHP% artisan accounts:reset-inventory
) else (
    echo Khong hieu lenh. Thoat.
)
pause
