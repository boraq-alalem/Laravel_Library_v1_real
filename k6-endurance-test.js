import http from 'k6/http';
import { check, sleep } from 'k6';

export let options = {
  vus: 30,          // 30 مستخدم ثابت
  duration: '300s', // 5 دقائق متواصلة
  
  thresholds: {
    http_req_duration: ['p(95)<1500'],
    http_req_failed: ['rate<0.05'],
    http_reqs: ['rate>20'],
  },
};

export default function () {
  const apis = [
    '/api/theses/latest',
    '/api/stats',
    '/api/theses/search?degree_id=1',
    '/api/universities',
  ];
  
  for (const api of apis) {
    const res = http.get(`https://alalem.c-library.org${api}`);
    
    check(res, {
      'Endurance - Status 200': (r) => r.status === 200,
      'Endurance - Fast response': (r) => r.timings.duration < 1500,
    });
    
    sleep(2); // راحة بين الطلبات
  }
}