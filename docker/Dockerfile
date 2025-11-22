FROM php:8.4-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpq-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring xml zip

# Install Composer
COPY --from=composer:2.9 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
