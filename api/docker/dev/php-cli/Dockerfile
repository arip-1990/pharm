FROM php:8.1-cli-alpine

RUN apk add --no-cache $PHPIZE_DEPS postgresql-dev bash coreutils unzip icu-dev gnu-libiconv

ENV LD_PRELOAD /usr/lib/preloadable_libiconv.so

RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install -j$(nproc) intl pdo_pgsql \
    && pecl install apcu-5.1.21 redis && pecl clear-cache \
    && docker-php-ext-enable apcu opcache redis

RUN mv $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini

COPY ./common/php/conf.d /usr/local/etc/php/conf.d

COPY ./common/wait-for-it.sh /usr/local/bin/wait-for-it
RUN chmod 555 /usr/local/bin/wait-for-it

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/bin --filename=composer --quiet

RUN addgroup -g 1000 app && adduser -u 1000 -G app -s /bin/sh -D app

WORKDIR /app

USER app
