#!/usr/bin/env bash
set -e

echo "Starting application initialization..."

# Set proper permissions
echo "Setting permissions..."
chown -R www-data:www-data /app/storage /app/bootstrap/cache
chmod -R 775 /app/storage /app/bootstrap/cache

# Wait for database to be ready
echo "Waiting for database connection..."
sleep 5

echo "Running database migrations..."
php artisan migrate --force

echo "Clearing and caching configuration..."
php artisan config:clear
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Application ready!"

# Start PHP-FPM in background
php-fpm -D

# Start Nginx in foreground
exec nginx -g 'daemon off;'
