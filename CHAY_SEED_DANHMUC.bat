@echo off
chcp 65001 >nul
setlocal
cd /d "%~dp0"

echo ================================================
echo  ShopGame - Seed danh muc + anh tu catalog
echo  (Idempotent: chay lai khong tao ban ghi trung)
echo ================================================
echo.

where php >nul 2>nul
if errorlevel 1 (
  echo [LOI] Khong tim thay PHP trong PATH. Mo Laragon ^> Terminal hoac them PHP vao PATH.
  pause
  exit /b 1
)

echo [1/3] Tao storage link (neu chua co)...
php artisan storage:link
echo.

echo [2/3] Chay CatalogImageSeeder...
php artisan db:seed --class=Database\Seeders\CatalogImageSeeder
if errorlevel 1 (
  echo.
  echo [LOI] Seed that bai. Kiem tra ket noi DB va da chay migrate chua.
  pause
  exit /b 1
)

echo.
echo [3/3] Xoa cache view/config...
php artisan optimize:clear

echo.
echo HOAN TAT. Kiem tra trang chu va /category de thay danh muc moi.
echo Luu y: seeder chi tao danh muc + anh, KHONG tao tai khoan game mau.
pause
