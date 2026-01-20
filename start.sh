#!/usr/bin/env bash
set -e

echo "Starting application initialization..."

# Create necessary directories
echo "Creating directories..."
mkdir -p /app/storage/logs
mkdir -p /app/storage/framework/{sessions,views,cache}
mkdir -p /app/storage/app/public
mkdir -p /app/bootstrap/cache

# Set proper permissions
echo "Setting permissions..."
chown -R www-data:www-data /app/storage /app/bootstrap/cache
chmod -R 775 /app/storage /app/bootstrap/cache

# Wait for database to be ready
echo "Waiting for database connection..."
sleep 5

# Create log file to prevent errors
touch /app/storage/logs/laravel.log
chown www-data:www-data /app/storage/logs/laravel.log
chmod 664 /app/storage/logs/laravel.log

echo "Running database migrations..."
php artisan migrate --force

echo "Creating storage link..."
php artisan storage:link || echo "Storage link already exists"

echo "Clearing and caching configuration..."
php artisan config:clear
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Application ready!"

# Create symlink for logs
ln -sf /dev/stdout /app/storage/logs/laravel.log

# Replace PORT in nginx config
envsubst '${PORT}' < /etc/nginx/nginx.conf > /tmp/nginx.conf
mv /tmp/nginx.conf /etc/nginx/nginx.conf

# Start PHP-FPM in background
php-fpm -D

# Start Nginx in foreground
exec nginx -g 'daemon off;'
