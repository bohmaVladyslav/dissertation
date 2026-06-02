FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip gnupg

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

RUN usermod -u 1000 www-data && groupmod -g 1000 www-data

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g npm

RUN apt-get install -y libzip-dev zip \
  && docker-php-ext-install zip

ENV NPM_CONFIG_CACHE=/tmp/npm-cache

RUN apt-get update; \
    apt-get install -y libmagickwand-dev; \
    pecl install imagick; \
    docker-php-ext-enable imagick;

WORKDIR /var/www/app

COPY docker/php/custom.php.ini /usr/local/etc/php/conf.d/custom.ini