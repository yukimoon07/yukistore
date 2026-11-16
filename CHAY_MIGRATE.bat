@echo off
chcp 65001 >nul
title SHOPGAME - Chay Migration (Giai doan B)
cd /d "%~dp0"

set PHP="C:\laragon\bin\php\php-8.3.36-Win32-vs16-x64\php.exe"
if not exist %PHP% (
    echo [LOI] Khong tim thay PHP tai %PHP%
    echo Hay sua duong dan PHP trong file nay cho dung voi may ban.
    pause
    exit /b 1
)

echo ========================================================
echo   SHOPGAME - CHAY MIGRATION GIAI DOAN B
echo ========================================================
echo.
echo Cac migration se chay:
echo   - business_units (6 nhom kinh doanh)
echo   - products + product_logs
echo   - inventory (stock_status, is_visible, reserved_until)
echo   - game_account_logs + random_account_logs
echo   - admin_activity_logs
echo   - payment_methods
echo   - two_factor (cot 2FA cho users)
echo.
echo LUU Y: Day la buoc CHI THEM bang/cot moi.
echo Khong xoa du lieu cu, khong reset kho.
echo.
pause

echo [1/3] Backup database truoc khi migrate...
if exist "C:\laragon\bin\mysql" (
    for /d %%i in ("C:\laragon\bin\mysql\*") do set MYSQLDIR=%%i
    call "%MYSQLDIR%\bin\mysqldump.exe" --no-tablespaces -u root shopgame > "storage\backups\backup_before_migrate_%date:~-4%%date:~3,2%%date:~0,2%_%time:~0,2%%time:~3,2%.sql" 2>nul
    echo     Da thu backup vao storage\backups
) else (
    echo     [CANH BAO] Khong tim thay mysqldump - hay tu backup bang HeidiSQL truoc khi tiep tuc!
    pause
)

echo.
echo [2/3] Dang chay migration...
%PHP% artisan migrate --force
if errorlevel 1 (
    echo.
    echo [LOI] Migration that bai. Xem thong bao phia tren.
    pause
    exit /b 1
)

echo.
echo [3/3] Xoa cache cau hinh...
%PHP% artisan config:clear
%PHP% artisan cache:clear
%PHP% artisan view:clear

echo.
echo ========================================================
echo   HOAN TAT! Migration da chay xong.
echo   Buoc tiep theo: chay CHAY_RESET_KHO.bat (neu ban muon reset kho)
echo   hoac bo qua va them tai khoan moi tu Admin Panel.
echo ========================================================
pause
