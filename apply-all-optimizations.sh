#!/bin/bash

echo "تطبيق جميع التحسينات المطلوبة..."

# 1. تطبيق تحسينات MySQL
chmod +x optimize-mysql.sh
bash optimize-mysql.sh

# 2. تطبيق الفهارس الجديدة
php artisan migrate --path=database/migrations/2024_01_01_000001_add_optimized_indexes.php --force

# 3. تحسين Redis
redis-cli CONFIG SET maxmemory 4gb
redis-cli CONFIG SET maxmemory-policy allkeys-lru
redis-cli CONFIG SET save "900 1 300 10 60 10000"

# 4. تنظيف وإعادة بناء الكاش
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear

# 5. إعادة بناء الكاش المحسن
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# 6. تحسين Composer autoload
composer dump-autoload --optimize --classmap-authoritative

# 7. تحسين OPcache
echo "opcache.enable=1
opcache.memory_consumption=1024
opcache.max_accelerated_files=20000
opcache.validate_timestamps=0
opcache.jit=1255
opcache.jit_buffer_size=512M
opcache.fast_shutdown=1" > opcache-optimized.ini

echo "تم تطبيق جميع التحسينات بنجاح!"
echo "الآن يمكنك اختبار الأداء المحسن"