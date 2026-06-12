# Stage 1 — Install Composer dependencies in isolation
FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-scripts \
    --no-interaction

COPY . .
RUN composer run-script post-autoload-dump

# Stage 2 — Production image (PHP-FPM + Nginx bundled)
FROM serversideup/php:8.4-fpm-nginx

USER root

COPY --chown=www-data:www-data --from=vendor /app /var/www/html

RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

USER www-data
