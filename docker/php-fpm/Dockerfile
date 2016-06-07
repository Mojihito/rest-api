FROM php:7.0-fpm

RUN sudo apt-get update && apt-get install -y \
        php7.0-cli \
        php7.0-mysql \
        php7.0-curl \
        php7.0-sqlite \
        php7.0-intl \
        php7.0-gd \
    && docker-php-ext-install -j$(nproc) iconv mcrypt \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd

RUN useradd -ms /bin/bash symfony
 
USER symfony
 
WORKDIR /app

COPY php-fpm.conf /etc/php-fpm.conf
