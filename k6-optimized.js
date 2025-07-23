import http from 'k6/http';
import { check, sleep } from 'k6';

export let options = {
  stages: [
    { duration: '10s', target: 20 },   // إحماء
    { duration: '20s', target: 100 },  // الحمل الكامل
    { duration: '10s', target: 0 },    // تهدئة
  ],
  thresholds: {
    http_req_duration: ['p(95)<1000'], // 95% أقل من ثانية
    http_req_failed: ['rate<0.1'],     // أقل من 10% فشل
  },
};

export default function () {
  const baseUrl = 'https://alalem.c-library.org/api';
  
  // اختبار API الرئيسي
  let res = http.get(`${baseUrl}/theses/latest`);
  check(res, {
    'Latest API status 200': (r) => r.status === 200,
    'Latest API < 500ms': (r) => r.timings.duration < 500,
  });

  sleep(0.5);

  // اختبار البحث البسيط
  res = http.get(`${baseUrl}/theses/search?degree_id=1`);
  check(res, {
    'Search API status 200': (r) => r.status === 200,
    'Search API < 800ms': (r) => r.timings.duration < 800,
  });

  sleep(0.5);

  // اختبار الإحصائيات
  res = http.get(`${baseUrl}/stats`);
  check(res, {
    'Stats API status 200': (r) => r.status === 200,
    'Stats API < 300ms': (r) => r.timings.duration < 300,
  });

  sleep(1);
}