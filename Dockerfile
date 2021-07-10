FROM composer:2.0.8 as composer

# ------------------------
# Install vendor
# ------------------------
FROM devesharp/nginx:php-8.0.1-alpine as build
ENV COMPOSER_ALLOW_SUPERUSER=1
WORKDIR /app

COPY composer.json composer.json
COPY composer.lock composer.lock

COPY --from=composer /usr/bin/composer /usr/local/bin/composer
RUN composer install --no-dev --no-scripts

FROM devesharp/nginx:php-8.0-alpine as app

# Convert .env
RUN apk add --no-cache openssl
ENV DOCKERIZE_VERSION v0.6.1
RUN wget https://github.com/jwilder/dockerize/releases/download/$DOCKERIZE_VERSION/dockerize-linux-amd64-$DOCKERIZE_VERSION.tar.gz \
    && tar -C /usr/local/bin -xzvf dockerize-linux-amd64-$DOCKERIZE_VERSION.tar.gz \
    && rm dockerize-linux-amd64-$DOCKERIZE_VERSION.tar.gz

COPY . /app
COPY --from=build /app/vendor ./vendor
RUN chown -R www-data:www-data /app

COPY ./.docker/app/start.sh /var/start.sh
RUN chmod +x /var/start.sh

CMD ["/var/start.sh"]


FROM app as test

COPY --from=composer /usr/bin/composer /usr/local/bin/composer

RUN composer install
RUN composer dump-autoload -o
