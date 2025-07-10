#!/bin/bash

# تحسينات للحمولة المضاعفة (200 مستخدم)
echo "تطبيق تحسينات للحمولة المضاعفة..."

# زيادة PHP-FPM processes
echo "pm.max_children = 100" > php-fpm-200users.conf
echo "pm.start_servers = 20" >> php-fpm-200users.conf
echo "pm.min_spare_servers = 10" >> php-fpm-200users.conf
echo "pm.max_spare_servers = 30" >> php-fpm-200users.conf

# زيادة وقت التخزين المؤقت
php artisan tinker --execute="Cache::flush();"

# تحسين Redis للحمولة العالية
redis-cli CONFIG SET maxmemory 1gb
redis-cli CONFIG SET maxclients 1000

# زيادة connection limits
echo "worker_connections 8192;" > nginx-200users.conf

echo "تم تطبيق تحسينات الحمولة المضاعفة!"