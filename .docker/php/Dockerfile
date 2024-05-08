FROM php:8.1-fpm

RUN docker-php-ext-install mysqli

COPY backend /var/www/html
WORKDIR  /var/www/html