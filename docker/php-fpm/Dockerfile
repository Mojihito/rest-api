FROM phpdockerio/php7-fpm:latest

# Install selected extensions and other stuff
RUN apt-get update \
    && apt-get -y --no-install-recommends install wget xz-utils x11-apps pdftk git php7.0-bcmath php7.0-memcached php7.0-mysql php7.0-bz2 php7.0-intl php7.0-mbstring php7.0-soap php7.0-ssh2 \
    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer --version

# Install wkhtmltopdf
WORKDIR "/tmp"
RUN wget http://download.gna.org/wkhtmltopdf/0.12/0.12.3/wkhtmltox-0.12.3_linux-generic-amd64.tar.xz
RUN tar xf wkhtmltox-0.12.3_linux-generic-amd64.tar.xz
RUN mv wkhtmltox/bin/wkhtmltopdf /usr/bin/wkhtmltopdf
RUN chmod +x /usr/bin/wkhtmltopdf

COPY php-fpm.conf /etc/php-fpm.conf

COPY php-ini-overrides.ini /etc/php/7.0/fpm/conf.d/99-overrides.ini

RUN mkdir -p /var/www/rest_api/var
RUN mkdir /var/www/rest_api/var/cache
RUN mkdir /var/www/rest_api/var/logs
RUN mkdir /var/www/rest_api/var/sessions

RUN usermod -u 1000 www-data
RUN chown -R www-data:www-data /var/www/rest_api/var/cache
RUN chown -R www-data:www-data /var/www/rest_api/var/logs
RUN chown -R www-data:www-data /var/www/rest_api/var/sessions

USER www-data

WORKDIR "/var/www/rest_api"
