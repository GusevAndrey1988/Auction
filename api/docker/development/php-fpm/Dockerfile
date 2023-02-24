FROM php:8.2-fpm

RUN pecl install xdebug\
    && docker-php-ext-enable xdebug\
    && rm -rf /tmp/pear

RUN mv $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini

COPY ./common/php/conf.d /usr/local/etc/php/conf.d
COPY ./development/php/conf.d /usr/local/etc/php/conf.d

WORKDIR /app