#!/bin/bash

echo "اختبار الأداء..."

# تشغيل Migration للفهارس الجديدة
php artisan migrate --force

# تشغيل Queue Worker في الخلفية
php artisan queue:work redis --daemon &

# اختبار سرعة الاستجابة
echo "اختبار سرعة API..."
time curl -s https://alalem.c-library.org/api/theses/latest > /dev/null

echo "اختبار البحث..."
time curl -s "https://alalem.c-library.org/api/theses/search?title=test" > /dev/null

echo "تم الانتهاء من اختبار الأداء"