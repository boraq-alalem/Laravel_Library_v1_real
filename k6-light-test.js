import http from 'k6/http';
import { check, sleep } from 'k6';

export let options = {
  vus: 50,          // تقليل المستخدمين إلى 50
  duration: '20s',  // تقليل المدة إلى 20 ثانية
};

export default function () {
  const res = http.get('https://alalem.c-library.org/api/theses/latest');

  check(res, {
    'status هو 200': (r) => r.status === 200,
    'الاستجابة أقل من 1000ms': (r) => r.timings.duration < 1000,
  });

  sleep(0.5); // تقليل المهلة
}