import http from 'k6/http';
import { check, sleep } from 'k6';
import { Counter } from 'k6/metrics';

// إنشاء عداد مخصص لتتبع الطلبات الناجحة
const successfulRequests = new Counter('successful_requests');

export let options = {
  // استراتيجية تدريجية لزيادة الحمل
  stages: [
    { duration: '10s', target: 20 },  // بداية تدريجية
    { duration: '30s', target: 50 },  // زيادة متوسطة
    { duration: '20s', target: 100 }, // ذروة الحمل
    { duration: '10s', target: 0 },   // تخفيف تدريجي
  ],
  thresholds: {
    http_req_duration: ['p(95)<500'], // التأكد من أن 95% من الطلبات أقل من 500ms
    'successful_requests': ['count>2000'], // التأكد من وجود أكثر من 2000 طلب ناجح
  },
  // إعدادات HTTP
  batch: 10, // عدد الطلبات المتزامنة في كل دفعة
  batchPerHost: 10, // عدد الطلبات المتزامنة لكل مضيف
};

export default function () {
  const urls = [
    'https://alalem.c-library.org/api/theses/latest',
    'https://alalem.c-library.org/api/theses/search?title=المشروع',
    'https://alalem.c-library.org/api/theses/search?degree_id=3',
  ];

  // استخدام طلبات متوازية بدلاً من التسلسلية
  const requests = urls.map(url => ({
    method: 'GET',
    url: url,
    params: {
      headers: {
        'Accept': 'application/json',
        'Cache-Control': 'no-cache',
      },
    },
  }));

  const responses = http.batch(requests);
  
  // التحقق من كل استجابة
  responses.forEach((res, index) => {
    const checkResult = check(res, {
      'status هو 200': (r) => r.status === 200,
      'الاستجابة أقل من 500ms': (r) => r.timings.duration < 500,
    });
    
    // زيادة عداد الطلبات الناجحة إذا كانت الاستجابة 200
    if (res.status === 200) {
      successfulRequests.add(1);
    }
  });

  // فترة راحة قصيرة بين كل تكرار
  sleep(0.3);
}
