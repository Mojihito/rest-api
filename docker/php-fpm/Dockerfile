FROM php:7.0-fpm

RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libmcrypt-dev \
    libpng12-dev \
    libicu-dev \
    libxml2-dev \

RUN docker-php-ext-install -j$(nproc) iconv intl xml soap mcrypt opcache pdo pdo_mysql mysqli mbstring \
RUN docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
RUN docker-php-ext-install -j$(nproc) gd
RUN docker-php-ext-install -j$(nproc) xml cli mysql curl sqlite intl \

RUN a2enmod rewrite

RUN useradd -ms /bin/bash symfony
 
USER symfony
 
WORKDIR /app

COPY php-fpm.conf /etc/php-fpm.conf
