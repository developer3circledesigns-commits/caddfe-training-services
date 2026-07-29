FROM php:8.1-apache

RUN docker-php-ext-install pdo pdo_mysql && \
    a2enmod rewrite expires headers deflate && \
    echo 'ServerName localhost' >> /etc/apache2/apache2.conf

COPY custom-php.ini /usr/local/etc/php/conf.d/custom-php.ini
