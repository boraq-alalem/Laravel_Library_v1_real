import http from 'k6/http';
import { check, sleep } from 'k6';

export let options = {
  stages: [
    { duration: '30s', target: 20 },   // Warm-up
    { duration: '1m', target: 50 },    // Ramp up
    { duration: '2m', target: 100 },   // Peak load
    { duration: '30s', target: 0 },    // Cool down
  ],
  thresholds: {
    http_req_duration: ['p(95)<500'],
    http_req_failed: ['rate<0.02'],
  },
};

export default function() {
  // Pre-warm connections
  let response = http.get('https://alalem.c-library.org/api/theses/latest?page=1&per_page=14', {
    headers: {
      'Connection': 'keep-alive',
      'Accept': 'application/json',
    },
  });
  
  check(response, {
    'status هو 200': (r) => r.status === 200,
    'الاستجابة أقل من 500ms': (r) => r.timings.duration < 500,
    'البيانات موجودة': (r) => r.json('data') !== undefined,
  });
  
  // Random sleep to simulate real user behavior
  sleep(Math.random() * 2 + 1);
}