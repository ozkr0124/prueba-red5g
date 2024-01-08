FROM php:8.2.10-fpm
WORKDIR "/app"

ENV COMPOSER_ALLOW_SUPERUSER=1

# Update
RUN apt-get update

# Install ZIP
RUN apt-get install -y libzip-dev \
    zip \
    zlib1g-dev \
    libicu-dev \
    g++ \
    libpq-dev \
    libpng-dev \
    libwebp-dev \
    libfreetype-dev \
    libjpeg62-turbo-dev

# Install PHP extensions
RUN docker-php-ext-install zip pdo pdo_mysql mysqli

RUN docker-php-ext-configure intl \
&& docker-php-ext-install intl

RUN docker-php-ext-configure gd --with-webp --with-freetype --with-jpeg \
&& docker-php-ext-install -j$(nproc) gd

# Install Redis
RUN pecl install -o -f redis && docker-php-ext-enable redis

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install xdebug
RUN pecl install xdebug-3.2.2 && docker-php-ext-enable xdebug

# Cleanup
RUN apt-get clean
RUN rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

# Composer exec
# RUN composer install
