#!/bin/bash

echo "🚀 تطبيق التحسينات المتاحة للمستخدم..."

# 1. تحسين Laravel
echo "⚡ تحسين Laravel..."
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# 2. تحسين Composer
echo "📦 تحسين Composer..."
composer dump-autoload --optimize --classmap-authoritative

# 3. تحسين ملفات JavaScript/CSS
echo "🎨 تحسين الأصول..."
npm run build 2>/dev/null || echo "NPM build تم تخطيه"

# 4. تحميل الكاش المسبق
echo "💾 تحميل الكاش..."
php -r "
use App\Services\AdvancedCacheService;
require 'vendor/autoload.php';
\$app = require 'bootstrap/app.php';
\$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
AdvancedCacheService::warmCache();
echo 'Cache warmed successfully\n';
"

# 5. تحسين OPcache
echo "🔥 إعادة تشغيل OPcache..."
php -r "if (function_exists('opcache_reset')) { opcache_reset(); echo 'OPcache reset\n'; }"

echo "✅ تم تطبيق التحسينات المتاحة!"
echo "🎯 الموقع محسن للأداء العالي"