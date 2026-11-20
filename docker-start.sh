#!/bin/sh
set -e
cd /var/www/html

: ${DATA_DIR:=/data}
mkdir -p "$DATA_DIR" storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache 2>/dev/null || true
chmod -R ug+rwX storage bootstrap/cache 2>/dev/null || true

# .env runtime (APP_KEY/APP_NAME/APP_URL/ADMIN_* nằm trong biến môi trường container)
cat > .env <<ENVEOF
APP_NAME=${APP_NAME:-ShopGame}
APP_ENV=production
APP_KEY=${APP_KEY}
APP_DEBUG=${APP_DEBUG:-false}
APP_URL=${APP_URL:-http://localhost}
APP_LOCALE=vi
APP_FALLBACK_LOCALE=vi
APP_MAINTENANCE_DRIVER=file
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
SESSION_DRIVER=file
SESSION_LIFETIME=120
LOG_CHANNEL=daily
LOG_LEVEL=warning
DB_CONNECTION=sqlite
DB_DATABASE=${DATA_DIR}/database.sqlite
CACHE_DRIVER=file
FILESYSTEM_DISK=public
QUEUE_CONNECTION=sync
TRUSTED_PROXIES=*
ENVEOF

NEED_SEED=0
if [ ! -f "$DATA_DIR/database.sqlite" ]; then
  touch "$DATA_DIR/database.sqlite"
  echo 'Tao DB moi'
  NEED_SEED=1
fi

# Luôn chạy migrate (idempotent — chỉ chạy migration chưa có)
php artisan migrate --force
if [ "$NEED_SEED" = "1" ]; then
  php artisan db:seed --force
  touch /tmp/need_admin_now
fi

# Luôn cập nhật danh mục & ảnh catalog (idempotent updateOrCreate theo slug)
php artisan db:seed --class=Database\\Seeders\\CatalogImageSeeder --force || echo 'warning: seed catalog that bai'

if [ -L public/storage ] && [ ! -d public/storage ]; then
  rm -f public/storage
fi
php artisan storage:link >/dev/null 2>&1 || true
php artisan config:cache >/dev/null
php artisan route:cache >/dev/null 2>&1 || true
php artisan view:cache >/dev/null 2>&1 || true

if [ -f /tmp/need_admin_now ]; then
  php artisan admin:create-admin --username="$ADMIN_USERNAME" --email="$ADMIN_EMAIL" --password="$ADMIN_PASSWORD" --no-confirm || echo 'warning: tao admin that bai'
fi

exec php artisan serve --host=0.0.0.0 --port="$PORT"
