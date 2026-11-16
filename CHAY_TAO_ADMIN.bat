@echo off
chcp 65001 >nul
title SHOPGAME - Tao tai khoan Admin an toan
cd /d "%~dp0"

set PHP="C:\laragon\bin\php\php-8.3.36-Win32-vs16-x64\php.exe"
if not exist %PHP% (
    echo [LOI] Khong tim thay PHP tai %PHP%
    echo Hay sua duong dan PHP trong file nay cho dung voi may ban.
    pause
    exit /b 1
)

echo ========================================================
echo   SHOPGAME - TAO TAI KHOAN ADMIN (AN TOAN)
echo ========================================================
echo.
echo Che do an toan moi:
echo   - Khong co mat khau mac dinh cong khai trong ma nguon.
echo   - Neu de trong mat khau, he thong se tu sinh mat khau manh
echo     va chi hien thi DUY NHAT mot lan tren man hinh.
echo   - Mat khau ban nhap se bi an khi go (khong luu vao history).
echo.
echo Vao Admin Panel tai: /admin/login
echo Sau khi dang nhap: doi mat khau va bat 2FA ngay.
echo.
pause

%PHP% artisan admin:create-admin %*
if errorlevel 1 (
    echo.
    echo [LOI] Chua tao duoc tai khoan admin. Xem thong bao phia tren.
    echo Goi y: da chay CHAY_MIGRATE.bat chua?
    pause
    exit /b 1
)

%PHP% artisan config:clear >nul 2>&1
%PHP% artisan cache:clear >nul 2>&1

echo.
echo Hoan tat. Hay bao mat mat khau vua tao o noi an toan.
pause
