#!/bin/bash

echo "=== بدء اختبارات الأداء الشاملة ==="

# تحسين ما قبل الاختبار
echo "1. تحسين النظام..."
php pre-test-optimization.php

echo ""
echo "2. اختبار الأداء العادي (40 ثانية)..."
/home/c-library-alalem/htdocs/alalem.c-library.org/k6 run k6-optimized.js

echo ""
echo "3. اختبار الضغط الشديد (60 ثانية)..."
/home/c-library-alalem/htdocs/alalem.c-library.org/k6 run k6-stress-test.js

echo ""
echo "4. اختبار الذروة المفاجئة (30 ثانية)..."
/home/c-library-alalem/htdocs/alalem.c-library.org/k6 run k6-spike-test.js

echo ""
echo "5. اختبار خفيف للتأكد (20 ثانية)..."
/home/c-library-alalem/htdocs/alalem.c-library.org/k6 run k6-light-test.js

echo ""
echo "=== انتهت جميع الاختبارات ==="