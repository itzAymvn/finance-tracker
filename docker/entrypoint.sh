#!/bin/sh
set -e
cd /var/www/html

if [ ! -f .env ] && [ -f .env.docker ]; then
    cp .env.docker .env
fi

if [ ! -f database/database.sqlite ]; then
    touch database/database.sqlite
fi

if [ ! -f vendor/autoload.php ]; then
    if [ "${APP_ENV:-local}" = "production" ]; then
        composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev
    else
        composer install --no-interaction --prefer-dist --optimize-autoloader
    fi
fi

chown -R www-data:www-data storage bootstrap/cache database database/database.sqlite vendor 2>/dev/null || true

exec docker-php-entrypoint php-fpm
