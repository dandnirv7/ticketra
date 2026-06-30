# syntax=docker/dockerfile:1.6

# ---------- Stage 1: build frontend assets ----------
FROM node:22-alpine AS frontend

WORKDIR /var/www/html

COPY package.json package-lock.json ./
RUN npm ci --ignore-scripts

COPY resources/ resources/
COPY public/ public/
COPY vite.config.js tailwind.config.js postcss.config.js ./

RUN npm run build


# ---------- Stage 2: install PHP dependencies ----------
FROM composer:2 AS vendor

WORKDIR /app

# Install required PHP extensions before composer
RUN apk add --no-cache \
        icu-dev \
        libpng-dev \
        libjpeg-turbo-dev \
        freetype-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) intl gd

COPY composer.json composer.lock ./
RUN composer install \
        --no-dev \
        --no-interaction \
        --no-scripts \
        --no-autoloader \
        --prefer-dist

COPY . .
RUN composer dump-autoload --optimize --no-dev


# ---------- Stage 3: production runtime ----------
FROM php:8.3-fpm-alpine AS production

# System deps + PHP extensions
RUN apk add --no-cache \
        nginx \
        supervisor \
        bash \
        curl \
        libpng-dev \
        libzip-dev \
        oniguruma-dev \
        icu-dev \
        mysql-client \
        $PHPIZE_DEPS \
    && docker-php-ext-install \
        bcmath \
        gd \
        intl \
        mbstring \
        opcache \
        pcntl \
        pdo \
        pdo_mysql \
        zip \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del $PHPIZE_DEPS

# PHP runtime config
RUN { \
        echo 'memory_limit=512M'; \
        echo 'upload_max_filesize=64M'; \
        echo 'post_max_size=64M'; \
        echo 'max_execution_time=120'; \
        echo 'date.timezone=Asia/Jakarta'; \
    } > /usr/local/etc/php/conf.d/zz-app.ini

COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/zz-opcache.ini

WORKDIR /var/www/html

# Application + deps
COPY --chown=www-data:www-data . /var/www/html
COPY --chown=www-data:www-data --from=vendor /app/vendor /var/www/html/vendor
COPY --chown=www-data:www-data --from=frontend /var/www/html/public/build /var/www/html/public/build

# Writable dirs
RUN mkdir -p \
        storage/framework/cache/data \
        storage/framework/sessions \
        storage/framework/testing \
        storage/framework/views \
        storage/logs \
        bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Entrypoint (clears Laravel & Filament caches on every container start)
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Nginx + supervisor configs
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf
COPY docker/supervisor/supervisord.conf /etc/supervisord.conf

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
