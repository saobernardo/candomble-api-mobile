FROM composer:2.10.1
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl
RUN apt-get clean && rm -rf /var/lib/apt/lists/*
RUN docker-php-ext-install pdo_mysql mbstring pcntl exif bcmath gd gmp
WORKDIR /var/www/html
COPY --chown=www-data:www-data . ./
RUN ["rm", "-rf", "./vendor"]

FROM php:8.4-fpm-bookworm
RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-configure pcntl --enable-pcntl \
  && docker-php-ext-install \
    pcntl
WORKDIR /var/www/html
COPY --chown=www-data:www-data . ./
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
USER www-data
EXPOSE 9000
CMD ["php-fpm"]