FROM php:8.1-cli-alpine AS builder

RUN apk add --no-cache libzip-dev postgresql-dev libpng-dev libwebp-dev libjpeg-turbo-dev freetype-dev \
    && docker-php-ext-configure gd --enable-gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install gd pdo_pgsql zip

ENV COMPOSER_ALLOW_SUPERUSER 1

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/bin --filename=composer --quiet

WORKDIR /app

COPY ./composer.json ./composer.lock ./

RUN composer install --no-dev --no-scripts --prefer-dist --no-progress --optimize-autoloader \
    && rm -rf /root/.composer/cache

### FPM ###

FROM php:8.1-fpm-alpine

RUN apk add --no-cache libzip-dev postgresql-dev libpng-dev libwebp-dev libjpeg-turbo-dev freetype-dev \
    && docker-php-ext-configure gd --enable-gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install gd pdo_pgsql zip opcache sockets

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

RUN chown www-data:www-data -R ./storage ./bootstrap/cache \
    && php artisan key:generate --ansi && php artisan storage:link --ansi

HEALTHCHECK --interval=5s --timeout=3s --start-period=1s \
    CMD REDIRECT_STATUS=true SCRIPT_NAME=/ping SCRIPT_FILENAME=/ping REQUEST_METHOD=GET \
    cgi-fcgi -bind -connect 127.0.0.1:9000 || exit 1
