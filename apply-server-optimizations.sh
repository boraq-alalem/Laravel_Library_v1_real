#!/bin/bash

echo "🚀 تطبيق تحسينات الخادم للأداء العالي..."

# تحسين Laravel
echo "📦 تحسين Laravel..."
php artisan config:cache
php artisan route:cache  
php artisan view:cache
php artisan event:cache

# تحسين Composer
echo "🎼 تحسين Composer..."
composer dump-autoload --optimize --classmap-authoritative

# تحسين Redis
echo "🔴 تحسين Redis..."
redis-cli CONFIG SET maxmemory-policy allkeys-lru
redis-cli CONFIG SET save ""
redis-cli CONFIG SET stop-writes-on-bgsave-error no

# تنظيف الكاش
echo "🧹 تنظيف الكاش..."
php artisan cache:clear
redis-cli FLUSHALL

# إعادة تشغيل الخدمات
echo "🔄 إعادة تشغيل الخدمات..."
sudo systemctl reload nginx
sudo systemctl reload php8.2-fpm
sudo systemctl restart redis-server

echo "✅ تم تطبيق جميع التحسينات بنجاح!"
echo "🎯 الآن يمكنك تشغيل اختبار الأداء مرة أخرى"