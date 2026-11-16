FROM php:8.3-cli-alpine

# Thư viện hệ thống cho các extension PHP cần thiết
RUN apk add --no-cache \
        sqlite-dev \
        icu-dev \
        oniguruma-dev \
        libzip-dev \
        libpng-dev \
        libjpeg-turbo-dev \
        freetype-dev \
        zip unzip curl

# Extension PHP cho Laravel + SQLite
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        pdo \
        pdo_sqlite \
        mbstring \
        intl \
        zip \
        bcmath \
        pcntl \
        gd \
        opcache

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --no-scripts --prefer-dist --optimize-autoloader

COPY . .

RUN composer dump-autoload --optimize --no-dev --no-interaction \
    && chmod +x docker-start.sh

ENV PHP_CLI_SERVER_WORKERS=8

EXPOSE 3000

CMD ["./docker-start.sh"]
