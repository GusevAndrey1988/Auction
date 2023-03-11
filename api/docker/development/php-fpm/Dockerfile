FROM php:8.2-fpm-alpine

RUN apk add postgresql-dev\
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql\
    && docker-php-ext-install pdo_pgsql

RUN mv $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini

COPY ./common/php/conf.d /usr/local/etc/php/conf.d
COPY ./development/php/conf.d /usr/local/etc/php/conf.d

WORKDIR /app