#!/bin/sh
set -e

# Ensure SQLite file and directories exist with proper permissions
mkdir -p /var/www/html/database
if [ ! -f /var/www/html/database/database.sqlite ]; then
    touch /var/www/html/database/database.sqlite
fi

mkdir -p /var/www/html/storage/framework/cache/data \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/logs \
         /var/www/html/bootstrap/cache

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

# Run database migrations if requested
if [ "$RUN_MIGRATIONS" = "true" ]; then
    echo "Running migrations..."
    php artisan migrate --force || true
fi

# Clear and optimize cache
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

# Start supervisord
exec /usr/bin/supervisord -c /etc/supervisord.conf
