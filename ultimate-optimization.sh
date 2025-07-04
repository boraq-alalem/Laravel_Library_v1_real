#!/bin/bash

echo "=== التحسين الشامل للنظام ==="

# 1. تحسين Laravel
echo "1. تحسين Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 2. تحسين قاعدة البيانات
echo "2. تحسين قاعدة البيانات..."
php artisan migrate --force

# 3. ضغط الملفات
echo "3. ضغط الملفات..."
chmod +x compress-pdfs.sh optimize-assets.sh
# ./compress-pdfs.sh
# ./optimize-assets.sh

# 4. تحسين Composer
echo "4. تحسين Composer..."
composer dump-autoload --optimize --classmap-authoritative

# 5. تنظيف الكاش
echo "5. إعادة تعيين الكاش..."
php artisan cache:clear
redis-cli FLUSHALL

# 6. إحماء الكاش
echo "6. إحماء الكاش..."
curl -s https://alalem.c-library.org/api/theses/latest > /dev/null
curl -s https://alalem.c-library.org/api/stats > /dev/null

# 7. تشغيل Queue Workers
echo "7. تشغيل Queue Workers..."
php artisan queue:restart

echo "=== انتهى التحسين الشامل ==="