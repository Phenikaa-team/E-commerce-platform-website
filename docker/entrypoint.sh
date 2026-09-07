#!/bin/sh
set -e

# Create database file & directories
mkdir -p /var/www/html/database
if [ ! -f /var/www/html/database/database.sqlite ]; then
    touch /var/www/html/database/database.sqlite
fi

mkdir -p /var/www/html/storage/framework/cache/data \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/logs \
         /var/www/html/bootstrap/cache

# Generate APP_KEY if not already set
if [ -z "$APP_KEY" ]; then
    echo "APP_KEY is empty, generating application key..."
    php artisan key:generate --force || true
fi

# Run database migrations and seed if empty or requested
if [ "$RUN_MIGRATIONS" = "true" ] || [ ! -s /var/www/html/database/database.sqlite ]; then
    echo "Running database migrations..."
    php artisan migrate --force || true
    echo "Seeding database with sample products..."
    php artisan db:seed --force || true
fi

# Ensure full permissions for www-data after creating sqlite and storage files
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database
chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

# Clear cache
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

# Start supervisord
exec /usr/bin/supervisord -c /etc/supervisord.conf
