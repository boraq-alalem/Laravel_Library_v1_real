# ملخص وتوثيق API لمشروع مكتبة الرسائل الجامعية

## 1. الإحصائيات العامة
- **GET /api/stats**
  - إرجاع إحصائيات عامة: إجمالي الرسائل، رسائل الماجستير، رسائل الدكتوراه، إجمالي الباحثين، الجامعات، التخصصات.

---

## 2. الرسائل (Theses)
- **GET /api/theses/latest**
  - جلب آخر 10 رسائل مع جميع العلاقات.
- **GET /api/theses/search**
  - بحث متقدم في الرسائل مع دعم الفلاتر (author, title, specialization_id, university_id, degree_id, year).
- **POST /api/theses**
  - إضافة رسالة جديدة (جميع الحقول مطلوبة، author_name نصي، pdf ملف PDF).
- **PUT /api/theses/{id}**
  - تعديل رسالة.
- **DELETE /api/theses/{id}**
  - حذف رسالة.
- **GET /api/theses/years**
  - جلب جميع السنوات الموجودة في قاعدة البيانات.

---

## 3. الفلاتر (Dropdowns)
- **GET /api/specializations**
  - جميع التخصصات.
- **GET /api/universities**
  - جميع الجامعات.
- **GET /api/degrees**
  - جميع الدرجات العلمية.

---

## 4. الجامعات والتخصصات
- **GET /api/universities-with-specializations**
  - جلب الجامعات مع التخصصات الخاصة بكل جامعة.
- **GET /api/universities/search?name=...**
  - البحث عن جامعة بالاسم (مع التخصصات).
- **POST /api/universities/{university}/specializations**
  - إضافة تخصص لجامعة (specialization_id مطلوب).

---

## ملاحظات هامة
- جميع الردود بصيغة JSON.
- عند رفع ملف PDF يجب أن يكون body من نوع form-data.
- author_name إجباري عند إضافة رسالة جديدة، وسيتم إنشاء الباحث تلقائياً إذا لم يكن موجوداً.
- جميع المسارات مرتبة في ملف api.php بنفس هذا الترتيب.

---

## مثال عملي لإضافة رسالة جديدة (Postman)
- نوع الطلب: POST
- الرابط: `http://127.0.0.1:8000/api/theses`
- Body > form-data:
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
}
```

---

لأي استفسار أو إضافة توثيق لمسارات جديدة، يمكنك طلب ذلك في أي وقت.
