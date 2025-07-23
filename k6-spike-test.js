import http from 'k6/http';
import { check, sleep } from 'k6';

export let options = {
  stages: [
    { duration: '5s', target: 10 },   // بداية هادئة
    { duration: '5s', target: 500 },  // ارتفاع مفاجئ
    { duration: '10s', target: 500 }, // استمرار الضغط
    { duration: '5s', target: 10 },   // انخفاض مفاجئ
    { duration: '5s', target: 0 },    // توقف
  ],
  
  thresholds: {
    http_req_failed: ['rate<0.5'], // 50% فشل مقبول في اختبار الذروة
  },
};

export default function () {
  const res = http.get('https://alalem.c-library.org/api/theses/latest');
  
  check(res, {
    'Spike test - Status check': (r) => r.status === 200 || r.status === 500,
    'Spike test - Response received': (r) => r.body.length > 0,
  });

  sleep(0.1); // نوم قصير جداً
}