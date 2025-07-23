#!/bin/bash

# Laravel optimization commands
echo "تحسين Laravel..."

# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Composer optimization
composer install --optimize-autoloader --no-dev
composer dump-autoload --optimize --classmap-authoritative

# Database optimization
php artisan migrate --force
php artisan db:seed --force

# Queue worker
php artisan queue:work redis --sleep=3 --tries=3 --max-time=3600 &

# Laravel Octane
php artisan octane:start --server=swoole --host=0.0.0.0 --port=8000 --workers=4

echo "تم تحسين Laravel بنجاح!"