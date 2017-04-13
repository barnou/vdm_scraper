FROM php:5.6-apache

RUN apt-get update
RUN apt-get -y upgrade
# Install PDO postgres driver and other stuffs
# See https://github.com/docker-library/php/issues/62
RUN apt-get install -y libcurl4-openssl-dev libssl-dev zip unzip php5-pgsql libpq-dev
RUN docker-php-ext-install pdo pdo_pgsql

COPY config/php.ini /usr/local/etc/php/


RUN php -r "readfile('https://getcomposer.org/installer');" | php -- --install-dir=/usr/local/bin --filename=composer \
    && chmod +x /usr/local/bin/composer
# Update the default apache site with the config we created.
ADD config/apache-config.conf /etc/apache2/sites-enabled/000-default.conf
# Workaround for write permission on write to MacOS X volumes
# See https://github.com/boot2docker/boot2docker/pull/534
RUN usermod -u 1000 www-data

# Enable Apache mod_rewrite
RUN a2enmod rewrite headers
RUN service apache2 restart
WORKDIR "/var/www/scripts/"