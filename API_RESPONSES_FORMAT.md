# API Guests & Main Endpoints Response Format

## 1. الإحصائيات العامة
GET /api/stats
```json
{
  "total_theses": 0,
  "master_theses": 0,
  "phd_theses": 0,
  "total_authors": 0,
  "total_universities": 0,
  "total_specializations": 0
}
```

---

## 2. الرسائل (Theses)
GET /api/theses/latest
GET /api/theses/search
```json
[
  {
    "id": 1,
    "title": "...",
    "year": "...",
    "pdf_path": "...",
    "university": { "id": 1, "name": "..." },
    "specialization": { "id": 1, "name": "..." },
    "degree": { "id": 1, "name": "..." },
    "author": { "id": 1, "name": "..." }
  }
]
```

POST /api/theses
```json
{
  "message": "تمت إضافة الرسالة بنجاح",
  "thesis": { ... },
  "author_name": "..."
}
```

PUT /api/theses/{id}
```json
{
  "message": "تم التعديل بنجاح",
  "thesis": { ... }
}
```

DELETE /api/theses/{id}
```json
{
  "message": "تم نقل الرسالة إلى الأرشيف وحذفها من جدول الرسائل"
}
```

GET /api/theses/years
```json
[
  "2025",
  "2024"
]
```

---

## 3. الفلاتر (Dropdowns)
GET /api/specializations
GET /api/universities
GET /api/degrees
```json
[
  { "id": 1, "name": "..." }
]
```

---

## 4. الجامعات والتخصصات
GET /api/universities-with-specializations
```json
[
  {
    "id": 1,
    "name": "...",
    "specializations": [
      { "id": 1, "name": "..." }
    ]
  }
]
```

GET /api/universities-with-specializations-guests
```json
[
  {
    "id": 1,
    "name": "...",
    "specializations": ["..."]
  }
]
```

GET /api/universities/search
```json
[
  { "id": 1, "name": "..." }
]
```

POST /api/universities/{university}/add-specialization
```json
{ "message": "تمت إضافة التخصص للجامعة بنجاح" }
```

---

## 5. الأرشيف (Archived Theses)
GET /api/archived-theses
```json
[
  {
    "id": 1,
    "title": "...",
    "year": "...",
    "pdf_path": "...",
    "university": { "id": 1, "name": "..." },
    "specialization": { "id": 1, "name": "..." },
    "degree": { "id": 1, "name": "..." },
    "author": { "id": 1, "name": "..." }
  }
]
```

DELETE /api/archived-theses/{id}
```json
{ "message": "تم حذف الرسالة والمجلد نهائياً من الأرشيف" }
```

POST /api/archived-theses/{id}/restore
```json
{ "message": "تمت استعادة الرسالة إلى جدول الرسائل بنجاح" }
```

---

## 7. عناوين الرسائل المحجوزة (ReservedThesisTitles)
GET /api/reserved-thesis-titles-latest
```json
[
  {
    "id": 1,
    "title": "...",
    "person_name": "...",
    "university": "...",
    "specialization": "...",
    "degree": "...",
    "date": "..."
  }
]
```

POST /api/reserved-thesis-titles
PUT /api/reserved-thesis-titles/{id}
```json
{
  "id": 1,
  "title": "...",
  "person_name": "...",
  "university": "...",
  "specialization": "...",
  "degree": "...",
  "date": "..."
}
```

DELETE /api/reserved-thesis-titles/{id}
```json
{ "message": "تم الحذف بنجاح" }
```

GET /api/reserved-thesis-titles-search?q=...
```json
[
  {
    "id": 1,
    "title": "...",
    "person_name": "...",
    "university": "...",
    "specialization": "...",
    "degree": "...",
    "date": "..."
  }
]
```

---

## روابط الزوار (Guests)
GET /api/reserved-thesis-titles-latest-guests
GET /api/reserved-thesis-titles-search-guests?q=...
```json
[
  {
    "title": "...",
    "person_name": "...",
    "university": "..."
  }
]
```

GET /api/theses/search-guests
```json
[
  {
    "title": "...",
    "year": "...",
    "pdf_path": "...",
    "university": "...",
    "specialization": "...",
    "degree": "...",
    "author": "..."
  }
]
```
