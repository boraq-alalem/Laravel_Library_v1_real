import http from 'k6/http';
import { check, sleep } from 'k6';

export let options = {
  stages: [
    { duration: '15s', target: 50 },   
    { duration: '30s', target: 150 },  // 150 مستخدم واقعي
    { duration: '15s', target: 0 },    
  ],
  thresholds: {
    http_req_duration: ['p(95)<500'], // 95% أقل من 500ms
    http_req_failed: ['rate<0.02'],   // أقل من 2% فشل
    http_reqs: ['rate>80'],           // أكثر من 80 req/sec
  },
};

export default function () {
  const res = http.get('https://alalem.c-library.org/api/theses/latest');
  
  check(res, {
    'Realistic - Status 200': (r) => r.status === 200,
    'Realistic - Good speed': (r) => r.timings.duration < 300,
  });

  sleep(0.3); // نوم واقعي
}