#!/bin/bash

echo "اختبار سريع للأداء..."

# تحسين سريع
php artisan cache:clear && php artisan config:cache

# اختبار واحد فقط
echo "تشغيل الاختبار المحسن..."
/home/c-library-alalem/htdocs/alalem.c-library.org/k6 run k6-optimized.js

echo "انتهى الاختبار السريع"