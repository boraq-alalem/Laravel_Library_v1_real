import http from 'k6/http';
import { check, sleep } from 'k6';

export let options = {
  stages: [
    { duration: '10s', target: 50 },   
    { duration: '20s', target: 300 },  // 300 مستخدم!
    { duration: '10s', target: 0 },    
  ],
  thresholds: {
    http_req_duration: ['p(95)<100'], // 95% أقل من 100ms
    http_req_failed: ['rate<0.05'],   // أقل من 5% فشل
    http_reqs: ['rate>100'],          // أكثر من 100 req/sec
  },
};

export default function () {
  const res = http.get('https://alalem.c-library.org/api/theses/latest');
  
  check(res, {
    'Extreme - Status 200': (r) => r.status === 200,
    'Extreme - Ultra fast': (r) => r.timings.duration < 50,
  });

  sleep(0.1); // نوم قصير جداً
}