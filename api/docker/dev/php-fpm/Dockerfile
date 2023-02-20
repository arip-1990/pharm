FROM php:8.1-fpm-alpine

RUN apk add --no-cache fcgi

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && install-php-extensions gd pdo_pgsql zip sockets

RUN mv $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini

COPY ./common/php/conf.d /usr/local/etc/php/conf.d
COPY ./common/php/php-fpm.d /usr/local/etc/php-fpm.d
COPY ./dev/php-fpm/conf.d /usr/local/etc/php/conf.d

# Russian certificates
COPY ./common/php/trusted_root_ca.crt /usr/local/share/ca-certificates/trusted_root_ca.crt
COPY ./common/php/trusted_sub_ca.crt /usr/local/share/ca-certificates/trusted_sub_ca.crt

RUN update-ca-certificates

WORKDIR /app

COPY ./dev/php-fpm/entrypoint.sh /usr/local/bin/docker-php-entrypoint
RUN chmod +x /usr/local/bin/docker-php-entrypoint
