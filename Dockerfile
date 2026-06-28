FROM php:7.3-fpm

WORKDIR /var/www/html

# Copy application files into the PHP-FPM image so the container is self-contained
COPY . /var/www/html

RUN docker-php-ext-install pdo pdo_mysql mysqli

RUN chown -R www-data:www-data /var/www/html || true
