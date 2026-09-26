#!/bin/sh

set -e

echo "Checking Laravel dependencies..."

if [ ! -f /var/www/vendor/autoload.php ]; then
    echo "Installing Composer dependencies..."
    composer install --no-interaction
fi

echo "Checking Laravel application key..."

if [ -z "$APP_KEY" ]; then
    if [ -f /var/www/.env ]; then
        echo "Generating Laravel application key..."
        php artisan key:generate --force
        php artisan migrate
    else
        echo "WARNING: .env file not found."
    fi
fi

echo "Starting Laravel..."

exec "$@"