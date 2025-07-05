#!/bin/bash

echo "=== اختبار أداء Nginx مع k6 ==="

# اختبار الأداء مع التحسينات الحالية
echo "اختبار الأداء الحالي..."
/home/c-library-alalem/htdocs/alalem.c-library.org/k6 run --duration 30s --vus 50 - <<EOF
import http from 'k6/http';
import { check } from 'k6';

export default function () {
  const res = http.get('https://alalem.c-library.org/api/theses/latest', {
    headers: {
      'Accept-Encoding': 'gzip, deflate',
      'User-Agent': 'k6-nginx-test'
    }
  });
  
  check(res, {
    'Status 200': (r) => r.status === 200,
    'GZIP enabled': (r) => r.headers['Content-Encoding'] === 'gzip',
    'Response time < 200ms': (r) => r.timings.duration < 200,
    'Keep-Alive': (r) => r.headers['Connection'] !== 'close'
  });
}
EOF

echo "=== انتهى اختبار الأداء ==="