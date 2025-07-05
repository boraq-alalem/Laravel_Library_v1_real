#!/bin/bash

echo "=== اختبار تحسينات الخادم ==="

# اختبار ضغط GZIP
echo "1. اختبار ضغط GZIP..."
curl -H "Accept-Encoding: gzip" -I https://alalem.c-library.org/api/theses/latest 2>/dev/null | grep -i "content-encoding"

# اختبار كاش Headers
echo "2. اختبار Cache Headers..."
curl -I https://alalem.c-library.org/api/theses/latest 2>/dev/null | grep -i "cache-control"

# اختبار أمان Headers
echo "3. اختبار Security Headers..."
curl -I https://alalem.c-library.org/ 2>/dev/null | grep -E "(X-Frame-Options|X-Content-Type-Options|X-XSS-Protection)"

# اختبار سرعة الاستجابة
echo "4. اختبار سرعة الاستجابة..."
time curl -s https://alalem.c-library.org/api/theses/latest > /dev/null

# اختبار Keep-Alive
echo "5. اختبار Keep-Alive..."
curl -I https://alalem.c-library.org/ 2>/dev/null | grep -i "connection"

# معلومات الخادم
echo "6. معلومات الخادم..."
curl -I https://alalem.c-library.org/ 2>/dev/null | grep -i "server"

echo "=== انتهى اختبار التحسينات ==="