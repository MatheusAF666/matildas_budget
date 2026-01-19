#!/usr/bin/env bash
set -e

echo "Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader

echo "Installing Node.js dependencies..."
npm ci

echo "Building frontend assets..."
npm run build

echo "Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo "Running database migrations..."
php artisan migrate --force

echo "Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Build completed successfully!"
