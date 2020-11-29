FROM composer:1.9.3 as composer

FROM albertoammar/nginx:php-7.4.1-alpine as build

ENV COMPOSER_ALLOW_SUPERUSER=1

WORKDIR /app
COPY . /app
COPY ./public /app/html
COPY --from=composer /usr/bin/composer /usr/local/bin/composer
RUN composer install --no-dev
RUN composer dump-autoload -o

FROM albertoammar/nginx:php-7.4.1-alpine

RUN set -ex && apk --no-cache add postgresql-dev
RUN docker-php-ext-install pdo_pgsql

COPY --from=build /app .

RUN chown -R www-data:www-data /app
