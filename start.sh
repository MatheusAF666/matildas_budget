#!/usr/bin/env bash
set -e

echo "Starting application initialization..."

# Wait for database to be ready
echo "Waiting for database connection..."
sleep 5

echo "Running database migrations..."
php artisan migrate --force

echo "Clearing and caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Application ready!"

# Start the application
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
