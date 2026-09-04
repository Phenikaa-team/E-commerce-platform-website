#!/bin/sh
set -e

# Cache configurations in production
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

# Run database migrations if needed
if [ "$RUN_MIGRATIONS" = "true" ]; then
    echo "Running migrations..."
    php artisan migrate --force
fi

# Start supervisord
exec /usr/bin/supervisord -c /etc/supervisord.conf
