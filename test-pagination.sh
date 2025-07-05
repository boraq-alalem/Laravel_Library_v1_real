#!/bin/bash

echo "=== اختبار Pagination ==="

echo "1. اختبار الصفحة الأولى (20 نتيجة):"
curl -s "https://alalem.c-library.org/api/theses/latest?page=1&per_page=20" | jq '.pagination'

echo ""
echo "2. اختبار الصفحة الثانية:"
curl -s "https://alalem.c-library.org/api/theses/latest?page=2&per_page=20" | jq '.pagination'

echo ""
echo "3. اختبار البحث مع pagination:"
curl -s "https://alalem.c-library.org/api/theses/search?degree_id=1&page=1&per_page=15" | jq '.pagination'

echo ""
echo "4. عدد النتائج في الصفحة الأولى:"
curl -s "https://alalem.c-library.org/api/theses/latest?page=1&per_page=5" | jq '.data | length'

echo "=== انتهى اختبار Pagination ==="