FROM php:8.2-cli-alpine AS composer

RUN apk add unzip

ENV COMPOSER_ALLOW_SUPERUSER 1

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"\
    && php -r "if (hash_file('sha384', 'composer-setup.php') === '55ce33d7678c5a611085589f1f3ddf8b3c52d662cd01d4ba75c0ee0459970c2200a51f492d557530c71c15d8dba01eae') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"\
    && php composer-setup.php --install-dir=bin --filename=composer --quite\
    && php -r "unlink('composer-setup.php');"

WORKDIR /app

COPY ./composer.json ./composer.lock ./

RUN composer install --no-dev --prefer-dist --no-progress --no-suggest --no-scripts --optimize-autoload\
    && rm -rf /root/.composer/cache

# CLI

FROM php:8.2-cli-alpine

RUN docker-php-ext-install opcache

RUN apk add postgresql-dev bash coreutils\
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql\
    && docker-php-ext-install pdo_pgsql
    
RUN mv $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini

COPY ./docker/common/php/conf.d /usr/local/etc/php/conf.d
COPY ./docker/production/php/conf.d /usr/local/etc/php/conf.d

COPY ./common/wait-for-it.sh /usr/local/bin/wait-for-it
RUN chmod 555 /usr/local/bin/wait-for-it

WORKDIR /app

COPY --from=composer /app ./
COPY ./ ./