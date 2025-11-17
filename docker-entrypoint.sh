#!/bin/bash
set -e

echo "Starting Vote App..."

# Wait for MySQL to be ready
echo "Waiting for MySQL to be ready..."
until php artisan migrate:status 2>/dev/null; do
    echo "MySQL is unavailable - sleeping"
    sleep 2
done

echo "MySQL is up - executing commands"

# Run migrations
php artisan migrate --force

# Create storage link if it doesn't exist
if [ ! -L /var/www/html/public/storage ]; then
    php artisan storage:link
fi

# Clear and cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set proper permissions
chmod -R 775 storage bootstrap/cache

echo "Application is ready!"

# Start the original entrypoint
exec /usr/local/bin/docker-php-serversideup-entrypoint "$@"