FROM php:8.2-cli

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip git curl libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql zip

COPY . .

RUN curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer

RUN composer install

CMD php artisan serve --host=0.0.0.0 --port=8000
