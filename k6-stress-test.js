import http from 'k6/http';
import { check, sleep } from 'k6';

export let options = {
  vus: 200,        // 200 مستخدم متزامن
  duration: '60s', // لمدة دقيقة كاملة
  
  thresholds: {
    http_req_duration: ['p(90)<2000', 'p(95)<3000'],
    http_req_failed: ['rate<0.2'],
    http_reqs: ['rate>50'], // أكثر من 50 طلب/ثانية
  },
};

const scenarios = [
  '/api/theses/latest',
  '/api/stats', 
  '/api/universities',
  '/api/specializations',
  '/api/degrees',
];

export default function () {
  const url = 'https://alalem.c-library.org' + scenarios[Math.floor(Math.random() * scenarios.length)];
  
  const res = http.get(url, {
    headers: {
      'Accept': 'application/json',
      'User-Agent': 'k6-stress-test',
    },
  });

  check(res, {
    'Status 200': (r) => r.status === 200,
    'Response time OK': (r) => r.timings.duration < 2000,
    'Content type JSON': (r) => r.headers['Content-Type'] && r.headers['Content-Type'].includes('application/json'),
  });

  sleep(Math.random() * 2); // نوم عشوائي 0-2 ثانية
}