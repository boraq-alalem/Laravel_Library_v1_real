# Laravel Library API Documentation

جميع المسارات تبدأ بـ `/api/`

---

## 1. الإحصائيات العامة

### جلب الإحصائيات
**GET** `/api/stats`
- إرجاع إحصائيات عامة (إجمالي الرسائل، الماجستير، الدكتوراه، الباحثين، الجامعات، التخصصات)

**مثال في Postman:**
- اختر GET
- الرابط: `http://127.0.0.1:8000/api/stats`

---

## 2. الرسائل

### جلب آخر 10 رسائل
**GET** `/api/theses/latest`
- إرجاع آخر 10 رسائل مع جميع العلاقات (الباحث، الجامعة، التخصص، الدرجة)

**مثال في Postman:**
- اختر GET
- الرابط: `http://127.0.0.1:8000/api/theses/latest`

### البحث في الرسائل
**GET** `/api/theses/search`
- الفلاتر (Query Params):
  - `author` (بحث باسم الباحث)
  - `title` (بحث بعنوان الرسالة)
  - `specialization_id` (تخصص)
  - `university_id` (جامعة)
  - `degree_id` (درجة علمية)
  - `year` (سنة)

**مثال في Postman:**
- اختر GET
- الرابط: `http://127.0.0.1:8000/api/theses/search?author=أحمد&year=2025`

### إضافة رسالة جديدة
**POST** `/api/theses`
- **Body:** (form-data)
  - `title` (نص، إجباري)
  - `year` (نص، إجباري)
  - `university_id` (رقم، إجباري)
  - `specialization_id` (رقم، إجباري)
  - `degree_id` (رقم، إجباري)
  - `author_name` (نص، إجباري)
  - `pdf` (ملف PDF، إجباري)

**مثال في Postman:**
- اختر POST
- الرابط: `http://127.0.0.1:8000/api/theses`
- اختر Body > form-data وأضف الحقول:
  - title: الذكاء الاصطناعي في التعليم
  - year: 2025
  - university_id: 1
  - specialization_id: 2
  - degree_id: 1
  - author_name: أحمد سالم
  - pdf: (اختر ملف PDF من جهازك)

**الرد المتوقع:**
```json
{
  "message": "تمت إضافة الرسالة بنجاح",
  "thesis": { ... },
  "author_name": "..."
