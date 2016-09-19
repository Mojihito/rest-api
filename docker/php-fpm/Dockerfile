FROM phpdockerio/php7-fpm:latest

# Install selected extensions and other stuff
RUN apt-get update \
    && apt-get -y --no-install-recommends install sudo git php7.0-bcmath php7.0-memcached php7.0-mysql php7.0-bz2 php7.0-intl php7.0-mbstring php7.0-soap php7.0-ssh2 \
    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer --version

COPY php-fpm.conf /etc/php-fpm.conf

COPY php-ini-overrides.ini /etc/php/7.0/fpm/conf.d/99-overrides.ini

RUN useradd -ms /bin/bash symfony

RUN mkdir -p /var/www/rest_api/app
RUN mkdir /var/www/rest_api/app/cache
RUN mkdir /var/www/rest_api/app/logs

RUN chown -R symfony:symfony /var/www/rest_api

USER symfony

WORKDIR "/var/www/rest_api"
