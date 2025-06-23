FROM php:8.3-fpm

# Cài các thư viện cần thiết để build các PHP extensions
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev \
    zip unzip libzip-dev \
    git libxml2-dev libicu-dev libonig-dev && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install \
        gd \
        zip \
        pdo \
        pdo_mysql \
        xml \
        intl \
        opcache && \
    pecl install xdebug && docker-php-ext-enable xdebug && \
    pecl install redis && docker-php-ext-enable redis

# Cài Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set thư mục làm việc trong container
WORKDIR /var/www/html

# Copy toàn bộ mã nguồn vào container
COPY . .

EXPOSE 9000
