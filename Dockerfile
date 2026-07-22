FROM composer:lts AS composer_deps
WORKDIR /app

COPY composer.json composer.lock ./
RUN COMPOSER_MEMORY_LIMIT=-1 composer install --no-dev --no-scripts --no-progress --prefer-dist

FROM node:20 AS node_build
WORKDIR /app

COPY package.json package-lock.json ./
RUN npm install

COPY . .
RUN npm run build

FROM php:8.2-fpm
WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
        zip unzip git curl libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
        libonig-dev libxml2-dev libzip-dev build-essential \
        sqlite3 libsqlite3-dev default-mysql-client \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_sqlite pdo_mysql zip mbstring gd \
    && rm -rf /var/lib/apt/lists/*

COPY . .
COPY --from=composer_deps /app/vendor ./vendor
COPY --from=node_build /app/public ./public
COPY php.ini /usr/local/etc/php/conf.d/uploads.ini

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap /var/www/html/database /var/www/html/public

EXPOSE 9000
CMD ["php-fpm"]