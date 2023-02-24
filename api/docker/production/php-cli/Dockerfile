FROM php:8.2-cli-alpine

RUN apk add unzip

RUN mv $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini

COPY ./docker/common/php/conf.d /usr/local/etc/php/conf.d

ENV COMPOSER_ALLOW_SUPERUSER 1

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"\
    && php -r "if (hash_file('sha384', 'composer-setup.php') === '55ce33d7678c5a611085589f1f3ddf8b3c52d662cd01d4ba75c0ee0459970c2200a51f492d557530c71c15d8dba01eae') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"\
    && php composer-setup.php --install-dir=bin --filename=composer --quite\
    && php -r "unlink('composer-setup.php');"

WORKDIR /app

COPY ./composer.json ./composer.lock ./

RUN composer install --no-dev --prefer-dist --no-progress --no-suggest --no-scripts --optimize-autoload\
    && rm -rf /root/.composer/cache

COPY ./ ./