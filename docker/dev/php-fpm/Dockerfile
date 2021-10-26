FROM php:8.0-fpm-alpine

RUN apk add --no-cache autoconf g++ make libmcrypt-dev mariadb-client \
    && pecl install mcrypt \
    && rm -rf /tmp/pear \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-enable mcrypt

RUN mv $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini

COPY ./common/php/conf.d /usr/local/etc/php/conf.d
COPY ./dev/php-fpm/conf.d /usr/local/etc/php/conf.d

WORKDIR /app

COPY ./dev/php-fpm/entrypoint.sh /usr/local/bin/docker-php-entrypoint
RUN chmod +x /usr/local/bin/docker-php-entrypoint
