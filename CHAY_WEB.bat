@echo off
chcp 65001 >nul
title SHOPGAME - Khoi dong Website
cd /d "%~dp0"

set PHP="C:\laragon\bin\php\php-8.3.36-Win32-vs16-x64\php.exe"
if not exist %PHP% (
    echo [LOI] Khong tim thay PHP tai %PHP%
    pause
    exit /b 1
)

echo ========================================================
echo   SHOPGAME (LARAVEL 11) - http://localhost:8080
echo   - Admin:  http://localhost:8080/admin/login
echo   - Cron reservation: php artisan accounts:release-expired-reservations
echo ========================================================
%PHP% artisan serve --port=8080
pause
