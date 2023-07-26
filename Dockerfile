ARG SITE_PHP_VERSION=8.2.3
ARG RR_VERSION=2023.1.3

# build php extensions
FROM php:${SITE_PHP_VERSION}-fpm-alpine AS php

RUN apk add --update $PHPIZE_DEPS supervisor libpng-dev libjpeg-turbo-dev libwebp-dev pngquant linux-headers \
    && pecl install -o -f redis \
    && apk del $PHPIZE_DEPS \
    && rm -rf /tmp/pear
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && docker-php-ext-configure gd --with-jpeg --with-webp \
    && docker-php-ext-install pdo_mysql exif pcntl gd opcache sockets \
    && docker-php-ext-enable redis
COPY docker/php/www.conf /usr/local/etc/php-fpm.d/www.conf
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY docker/queue/supervisord.conf /etc/supervisord.conf

# add modules that are only needed for local development
FROM php AS dev
ENV PHP_OPCACHE_VALIDATE_TIMESTAMPS="1"
RUN apk add mysql-client nodejs npm

# install composer and prepare storage directories
FROM php AS composer-base
WORKDIR /app
RUN curl -s https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin/ --filename=composer
COPY composer.json composer.lock ./
RUN mkdir storage storage/framework storage/logs \
    && cd storage/framework && mkdir sessions views cache && cd ../.. \
    && chown -R www-data:www-data storage

# install all dependencies for prod
FROM composer-base AS composer-prod
RUN composer install \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --prefer-dist \
    --no-dev \
    --optimize-autoloader
COPY . .
RUN composer dump-autoload -o

# copy files from previous stages to build the final container
FROM php AS prod
ARG RR_VERSION

COPY . /var/www/html
WORKDIR /var/www/html
RUN chmod +x docker/entry.sh \
    && chmod +x docker/queue.sh \
    && chmod +x docker/scheduler.sh
COPY --from=composer-prod /app/storage/ ./storage/
COPY --from=composer-prod /app/bootstrap/cache/ ./bootstrap/cache
COPY --from=composer-prod /app/vendor/ ./vendor/
ADD https://github.com/roadrunner-server/roadrunner/releases/download/v$RR_VERSION/roadrunner-$RR_VERSION-linux-amd64.tar.gz ./rr.tar.gz
RUN mkdir rr-bin && tar -C ./rr-bin -zxvf rr.tar.gz && rm rr.tar.gz
RUN mv ./rr-bin/roadrunner-$RR_VERSION-linux-amd64/rr . && \
    rm -rf ./rr-bin && \
    php artisan octane:install --server=roadrunner && \
    chmod 764 rr