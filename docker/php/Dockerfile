FROM php:8.3-apache

RUN apt-get update && apt-get install -y \
    libcurl4-openssl-dev \
    libicu-dev \
    libjpeg-dev \
    libonig-dev \
    libpng-dev \
    libzip-dev \
    && docker-php-ext-configure gd --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql mysqli mbstring zip intl gd exif fileinfo curl \
    && a2enmod rewrite

COPY custom-php.ini /usr/local/etc/php/conf.d/custom-php.ini

HEALTHCHECK --interval=30s --timeout=5s --start-period=10s --retries=3 \
    CMD curl -f http://localhost:80/ || exit 1
