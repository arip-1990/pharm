FROM php:8.1-cli-alpine AS builder

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && install-php-extensions gd pdo_pgsql zip opcache sockets

ENV COMPOSER_ALLOW_SUPERUSER 1

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer --quiet

WORKDIR /app

COPY ./composer.json ./composer.lock ./

RUN composer install --no-dev --no-scripts --prefer-dist --no-progress --optimize-autoloader \
    && rm -rf /root/.composer/cache

### FPM ###

FROM php:8.1-fpm-alpine

RUN apk add --no-cache fcgi

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && install-php-extensions gd pdo_pgsql zip opcache sockets

RUN mv $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini

COPY ./docker/common/php/conf.d /usr/local/etc/php/conf.d
COPY ./docker/common/php/php-fpm.d /usr/local/etc/php-fpm.d
COPY ./docker/prod/php/conf.d /usr/local/etc/php/conf.d

# Russian certificates
COPY ./docker/common/php/trusted_root_ca.crt /usr/local/share/ca-certificates/trusted_root_ca.crt
COPY ./docker/common/php/trusted_sub_ca.crt /usr/local/share/ca-certificates/trusted_sub_ca.crt

RUN update-ca-certificates

WORKDIR /app

COPY --from=builder /app ./
COPY ./ ./

RUN chown www-data:www-data ./storage ./bootstrap/cache -R \
    && php artisan key:generate --ansi && php artisan storage:link --ansi

HEALTHCHECK --interval=5s --timeout=3s --start-period=1s \
    CMD REDIRECT_STATUS=true SCRIPT_NAME=/ping SCRIPT_FILENAME=/ping REQUEST_METHOD=GET \
    cgi-fcgi -bind -connect 127.0.0.1:9000 || exit 1
