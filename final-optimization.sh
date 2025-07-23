#!/bin/bash

# تحسين Laravel النهائي
echo "بدء التحسين النهائي..."

# تشغيل Migration للفهارس
php artisan migrate --force

# تحسين Composer
composer install --optimize-autoloader --no-dev --classmap-authoritative

# تحسين Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# تشغيل Queue Worker
php artisan queue:work redis --daemon &

# تشغيل Laravel Octane
php artisan octane:install swoole
php artisan octane:start --server=swoole --workers=4 --port=8000

echo "تم التحسين بنجاح!"