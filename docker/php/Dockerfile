FROM php:8.3-apache

RUN apt-get update && apt-get install -y --no-install-recommends \
    libcurl4-openssl-dev \
    libicu-dev \
    libjpeg-dev \
    libonig-dev \
    libpng-dev \
    libzip-dev \
    && docker-php-ext-configure gd --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql mysqli mbstring zip intl gd exif fileinfo curl \
    && a2enmod rewrite \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

COPY docker/php/custom-php.ini /usr/local/etc/php/conf.d/custom-php.ini

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/uploads 2>/dev/null || true

HEALTHCHECK --interval=30s --timeout=5s --start-period=15s --retries=3 \
    CMD curl -f http://localhost/health.php || exit 1
