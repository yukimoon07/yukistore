@echo off
chcp 65001 >nul
title SHOPGAME - Tao ban Release Production
cd /d "%~dp0"

REM ============================================================
REM  SHOPGAME - Dong goi ban release sach cho production
REM  Script: TAO_RELEASE.bat
REM  Ket qua: shopgame-release_YYYYMMDD.zip (khong chua du lieu local)
REM ============================================================

set PHP="C:\laragon\bin\php\php-8.3.36-Win32-vs16-x64\php.exe"
set RELEASE_DIR=%TEMP%\shopgame_release_tmp
set ZIP_NAME=shopgame-release_%date:~-4%%date:~3,2%%date:~0,2%.zip

echo ============================================================
echo   TAO BAN RELEASE SACH
echo ============================================================
echo.
echo Qua trinh:
echo   1. Kiem tra moi truong (composer validate)
echo   2. Copy ma nguon, LOAI BO file nhay cam va du lieu demo
echo   3. Dong goi thanh %ZIP_NAME%
echo.
pause

if exist "%RELEASE_DIR%" rmdir /s /q "%RELEASE_DIR%"
mkdir "%RELEASE_DIR%"

echo [1/4] Kiem tra composer...
if exist %PHP% (
    %PHP% composer.phar validate --no-check-publish 2>nul
    if not exist composer.phar (
        echo     Khong co composer.phar - bo qua buoc kiem tra.
    )
)

echo [2/4] Copy ma nguon...

REM Danh sach thu muc copy
robocopy app "%RELEASE_DIR%\app" /E /NFL /NDL /NJH /NJS >nul
robocopy bootstrap "%RELEASE_DIR%\bootstrap" /E /NFL /NDL /NJH /NJS >nul
robocopy config "%RELEASE_DIR%\config" /E /NFL /NDL /NJH /NJS >nul
robocopy database\migrations "%RELEASE_DIR%\database\migrations" /E /NFL /NDL /NJH /NJS >nul
robocopy database\seeders "%RELEASE_DIR%\database\seeders" /E /NFL /NDL /NJH /NJS >nul
robocopy lang "%RELEASE_DIR%\lang" /E /NFL /NDL /NJH /NJS >nul 2>nul
robocopy resources "%RELEASE_DIR%\resources" /E /NFL /NDL /NJH /NJS >nul
robocopy routes "%RELEASE_DIR%\routes" /E /NFL /NDL /NJH /NJS >nul
robocopy public "%RELEASE_DIR%\public" /E /NFL /NDL /NJH /NJS >nul
robocopy storage\app "%RELEASE_DIR%\storage\app" /E /NFL /NDL /NJH /NJS >nul
robocopy storage\backups "%RELEASE_DIR%\storage\backups" /E /NFL /NDL /NJH /NJS >nul

REM Tao thu muc runtime rong voi .gitignore
for %%d in ("storage\framework\cache\data" "storage\framework\sessions" "storage\framework\testing" "storage\framework\views" "storage\logs" "storage\app\public") do (
    if not exist "%RELEASE_DIR%\%%~d" mkdir "%RELEASE_DIR%\%%~d"
    echo * > "%RELEASE_DIR%\%%~d\.gitignore"
)

REM File goc can thiet
copy .env.example "%RELEASE_DIR%\.env.example" >nul
copy .gitignore "%RELEASE_DIR%\.gitignore" >nul
copy .gitattributes "%RELEASE_DIR%\.gitattributes" >nul
copy artisan "%RELEASE_DIR%\artisan" >nul
copy composer.json "%RELEASE_DIR%\composer.json" >nul
copy composer.lock "%RELEASE_DIR%\composer.lock" >nul
copy package.json "%RELEASE_DIR%\package.json" >nul
copy phpunit.xml "%RELEASE_DIR%\phpunit.xml" >nul
copy vite.config.js "%RELEASE_DIR%\vite.config.js" >nul
copy tailwind.config.js "%RELEASE_DIR%\tailwind.config.js" >nul
copy postcss.config.js "%RELEASE_DIR%\postcss.config.js" >nul
copy README.md "%RELEASE_DIR%\README.md" >nul 2>nul
copy DEPLOYMENT.md "%RELEASE_DIR%\DEPLOYMENT.md" >nul
copy SECURITY.md "%RELEASE_DIR%\SECURITY.md" >nul

REM .bat ho tro qua trinh deploy tren Windows
copy CHAY_MIGRATE.bat "%RELEASE_DIR%\" >nul 2>nul
copy CHAY_WEB.bat "%RELEASE_DIR%\" >nul 2>nul

echo [3/4] Kiem tra khong co file nhay cam trong ban release...

if exist "%RELEASE_DIR%\.env" (
    echo [LOI NGHIEM TRONG] .env da bi copy vao ban release! DUNG NGAY.
    pause
    exit /b 1
)
if exist "%RELEASE_DIR%\shopgame.sql" (
    echo [LOI NGHIEM TRONG] shopgame.sql da bi copy! DUNG NGAY.
    pause
    exit /b 1
)
if exist "%RELEASE_DIR%\public\terminal.php" (
    echo [LOI NGHIEM TRONG] terminal.php van con! DUNG NGAY.
    pause
    exit /b 1
)
if exist "%RELEASE_DIR%\CHAY_TAO_ADMIN.bat" (
    echo     Xoa CHAY_TAO_ADMIN.bat khoi release (chua du lieu tao admin)...
    del "%RELEASE_DIR%\CHAY_TAO_ADMIN.bat"
)
if exist "%RELEASE_DIR%\CHAY_RESET_KHO.bat" (
    echo     Xoa CHAY_RESET_KHO.bat khoi release (nguy hiem tren production)...
    del "%RELEASE_DIR%\CHAY_RESET_KHO.bat"
)
if exist "%RELEASE_DIR%\storage\backups" (
    echo     Xoa backups khoi release...
    rmdir /s /q "%RELEASE_DIR%\storage\backups"
)
if exist "%RELEASE_DIR%\dichvudark.md" del "%RELEASE_DIR%\dichvudark.md"
if exist "%RELEASE_DIR%\thegioidev.md" del "%RELEASE_DIR%\thegioidev.md"
if exist "%RELEASE_DIR%\__MACOSX" rmdir /s /q "%RELEASE_DIR%\__MACOSX"

echo [4/4] Dong goi...
powershell -Command "Compress-Archive -Path '%RELEASE_DIR%\*' -DestinationPath '%ZIP_NAME%' -Force"

if exist "%ZIP_NAME%" (
    echo.
    echo ============================================================
    echo   HOAN TAT! Ban release: %ZIP_NAME%
    echo   - Khong co .env, SQL dump, log, backup
    echo   - Khong co terminal.php, CHAY_TAO_ADMIN.bat
    echo   - Co DEPLOYMENT.md + SECURITY.md + .env.example
    echo   Danh cho may chua 7zip:
    echo   Danh sach file tam: %RELEASE_DIR%
    echo ============================================================
) else (
    echo [LOI] Khong tao duoc file zip. Kiem tra PowerShell.
)

pause
