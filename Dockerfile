# --- STAGE 1: COMPOSER (The Builder) ---
FROM composer:lts AS builder
WORKDIR /app

# Copy composer files first for better layer caching
COPY composer.json composer.lock ./

# Install production dependencies
RUN composer install \
    --ignore-platform-reqs \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --prefer-dist

# --- STAGE 2: PHP-FPM (The Runtime) ---
FROM php:8.4-fpm-bookworm

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y --no-install-recommends \
    libzstd-dev \
    liblz4-dev \
    libsasl2-modules \
    libmongoc-1.0-0 \
    build-essential \
    libssl-dev \
    libsasl2-dev \
    libgmp-dev \
    libonig-dev \
    libpng-dev && \
    # Install PECL extensions
    pecl install -o -f igbinary zstd && \
    docker-php-ext-enable igbinary zstd && \
    # Build Redis with custom flags
    mkdir -p /usr/src/php/ext/redis && \
    curl -fsSL https://pecl.php.net/get/redis | tar xz -C /usr/src/php/ext/redis --strip-components=1 && \
    cd /usr/src/php/ext/redis && \
    phpize && \
    ./configure --enable-redis-igbinary=yes --enable-redis-zstd=yes --enable-redis-lz4=yes --with-liblz4 && \
    make && make install && \
    docker-php-ext-enable redis && \
    # Install MongoDB and standard extensions
    pecl install mongodb && \
    docker-php-ext-enable mongodb && \
    docker-php-ext-install pdo_mysql mbstring pcntl exif bcmath gd gmp

# Cleanup build-only tools to keep the image slim
RUN apt-get purge -y \
        build-essential \
        libssl-dev \
        libsasl2-dev \
        libmongoc-dev && \
    apt-get autoremove -y && \
    apt-get clean && \
    useradd -s /bin/bash developer && \
    rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

# 1. Copy your application source code
COPY --chown=www-data:www-data . ./

# 2. Copy the 'vendor' folder from the builder stage
# This solves the "Failed to open stream" error
COPY --from=builder --chown=www-data:www-data /app/vendor ./vendor

# 3. Add the composer binary just in case you need it for manual commands
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

USER www-data
EXPOSE 9000
CMD ["php-fpm"]