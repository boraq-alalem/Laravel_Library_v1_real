import http from 'k6/http';
import { check, sleep } from 'k6';

export let options = {
  vus: 100,         // عدد المستخدمين المتزامنين (Virtual Users)
  duration: '30s',  // مدة الاختبار
};

export default function () {
  const urls = [
    'https://alalem.c-library.org/api/theses/latest',
    'https://alalem.c-library.org/api/theses/search?title=المشروع',
    'https://alalem.c-library.org/api/theses/search?degree_id=3',
  ];

  for (const url of urls) {
    const res = http.get(url);

    check(res, {
      'status هو 200': (r) => r.status === 200,
      'الاستجابة أقل من 500ms': (r) => r.timings.duration < 500,
    });

    sleep(1); // مهلة بين كل طلب
  }
}