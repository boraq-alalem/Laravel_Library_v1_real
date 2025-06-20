**وصف مشروع Laravel لإدارة الرسائل العلمية والأدوار والصلاحيات**

هذا الوصف يمثل مشروع Laravel لإدارة الرسائل العلمية، الجامعات، التخصصات، المؤلفين، الدرجات العلمية، بالإضافة إلى نظام متكامل لإدارة الأدوار والصلاحيات للمستخدمين. يهدف المشروع إلى توفير واجهات برمجة تطبيقات (APIs) للتعامل مع هذه البيانات بشكل آمن ومنظم.

**1. بنية قاعدة البيانات (Migrations)**

المشروع يستخدم Laravel Migrations لإنشاء الجداول التالية في قاعدة البيانات:

*   **`users`**:
    *   `id`: مفتاح أساسي.
    *   `name`: اسم المستخدم (سلسلة نصية).
    *   `email`: البريد الإلكتروني للمستخدم (سلسلة نصية، فريد).
    *   `email_verified_at`: تاريخ ووقت التحقق من البريد الإلكتروني (nullable timestamp).
    *   `password`: كلمة المرور المشفرة (سلسلة نصية).
    *   `remember_token`: رمز "تذكرني" (nullable string).
    *   `timestamps`: `created_at` و `updated_at`.
*   **`password_reset_tokens`**:
    *   `email`: البريد الإلكتروني (مفتاح أساسي).
    *   `token`: رمز إعادة تعيين كلمة المرور (سلسلة نصية).
    *   `created_at`: تاريخ ووقت الإنشاء (nullable timestamp).
*   **`sessions`**:
    *   `id`: مفتاح أساسي (سلسلة نصية).
    *   `user_id`: مفتاح خارجي لجدول `users` (nullable foreignId, index).
    *   `ip_address`: عنوان IP (سلسلة نصية بطول 45).
    *   `user_agent`: وكيل المستخدم (نص طويل).
    *   `payload`: حمولة الجلسة (نص طويل جداً).
    *   `last_activity`: آخر نشاط (integer, index).
*   **`cache`**:
    *   `key`: مفتاح التخزين المؤقت (مفتاح أساسي).
    *   `value`: قيمة التخزين المؤقت (نص متوسط).
    *   `expiration`: تاريخ انتهاء الصلاحية (integer).
*   **`cache_locks`**:
    *   `key`: مفتاح القفل (مفتاح أساسي).
    *   `owner`: مالك القفل (سلسلة نصية).
    *   `expiration`: تاريخ انتهاء الصلاحية (integer).
*   **`jobs`**:
    *   `id`: مفتاح أساسي.
    *   `queue`: قائمة الانتظار (سلسلة نصية، index).
    *   `payload`: حمولة المهمة (نص طويل جداً).
    *   `attempts`: عدد المحاولات (unsignedTinyInteger).
    *   `reserved_at`: وقت الحجز (nullable unsignedInteger).
    *   `available_at`: وقت الإتاحة (unsignedInteger).
    *   `created_at`: وقت الإنشاء (unsignedInteger).
*   **`job_batches`**:
    *   `id`: مفتاح أساسي (سلسلة نصية).
    *   `name`: اسم الدفعة (سلسلة نصية).
    *   `total_jobs`: إجمالي المهام (integer).
    *   `pending_jobs`: المهام المعلقة (integer).
    *   `failed_jobs`: المهام الفاشلة (integer).
    *   `failed_job_ids`: معرفات المهام الفاشلة (نص طويل جداً).
    *   `options`: خيارات (nullable mediumText).
    *   `cancelled_at`: وقت الإلغاء (nullable integer).
    *   `created_at`: وقت الإنشاء (integer).
    *   `finished_at`: وقت الانتهاء (nullable integer).
*   **`failed_jobs`**:
    *   `id`: مفتاح أساسي.
    *   `uuid`: معرف فريد عالمي (سلسلة نصية، فريد).
    *   `connection`: الاتصال (نص).
    *   `queue`: قائمة الانتظار (نص).
    *   `payload`: الحمولة (نص طويل جداً).
    *   `exception`: الاستثناء (نص طويل جداً).
    *   `failed_at`: وقت الفشل (timestamp، يستخدم الوقت الحالي).
*   **`universities`**:
    *   `id`: مفتاح أساسي.
    *   `name`: اسم الجامعة (سلسلة نصية، index).
    *   `timestamps`: `created_at` و `updated_at`.
*   **`specializations`**:
    *   `id`: مفتاح أساسي.
    *   `name`: اسم التخصص (سلسلة نصية، index).
    *   `timestamps`: `created_at` و `updated_at`.
*   **`degrees`**:
    *   `id`: مفتاح أساسي.
    *   `name`: اسم الدرجة العلمية (سلسلة نصية، index).
    *   `timestamps`: `created_at` و `updated_at`.
*   **`authors`**:
    *   `id`: مفتاح أساسي.
    *   `name`: اسم المؤلف (سلسلة نصية، index).
    *   `timestamps`: `created_at` و `updated_at`.
*   **`specialization_university` (جدول وسيط Many-to-Many)**:
    *   `id`: مفتاح أساسي.
    *   `university_id`: مفتاح خارجي لجدول `universities` (constrained, onDelete('cascade')).
    *   `specialization_id`: مفتاح خارجي لجدول `specializations` (constrained, onDelete('cascade')).
    *   `unique(['university_id', 'specialization_id'])`: لضمان عدم تكرار العلاقة.
    *   `timestamps`: `created_at` و `updated_at`.
*   **`theses`**:
    *   `id`: مفتاح أساسي.
    *   `title`: عنوان الرسالة (سلسلة نصية بطول 1024).
    *   `year`: سنة الرسالة (سلسلة نصية، index).
    *   `pdf_path`: مسار ملف PDF (nullable string).
    *   `university_id`: مفتاح خارجي لجدول `universities` (constrained, onDelete('restrict')).
    *   `specialization_id`: مفتاح خارجي لجدول `specializations` (constrained, onDelete('restrict')).
    *   `degree_id`: مفتاح خارجي لجدول `degrees` (constrained, onDelete('restrict')).
    *   `author_id`: مفتاح خارجي لجدول `authors` (constrained, onDelete('restrict')).
    *   `timestamps`: `created_at` و `updated_at`.
    *   `index(['university_id', 'specialization_id', 'degree_id'])`.
*   **`reserved_thesis_titles`**:
    *   `id`: مفتاح أساسي.
    *   `title`: عنوان الرسالة المحجوزة (سلسلة نصية بطول 1024).
    *   `person_name`: اسم الشخص الذي حجز العنوان (سلسلة نصية بطول 255).
    *   `university`: الجامعة (سلسلة نصية بطول 255).
    *   `specialization`: التخصص (سلسلة نصية بطول 255).
    *   `degree`: الدرجة العلمية (سلسلة نصية بطول 255).
    *   `date`: التاريخ (سلسلة نصية بطول 255).
    *   `timestamps`: `created_at` و `updated_at`.
*   **`thesis_titles_simple`**:
    *   `id`: مفتاح أساسي.
    *   `title`: عنوان الرسالة البسيط (سلسلة نصية بطول 512، فريد).
    *   `person_name`: اسم الشخص (سلسلة نصية).
    *   `university`: الجامعة (سلسلة نصية).
    *   `timestamps`: `created_at` و `updated_at`.
*   **`archive_theses`**:
    *   `id`: مفتاح أساسي.
    *   `title`: عنوان الرسالة المؤرشفة (سلسلة نصية بطول 1024).
    *   `year`: سنة الرسالة (سلسلة نصية، index).
    *   `pdf_path`: مسار ملف PDF (nullable string).
    *   `university_id`: مفتاح خارجي لجدول `universities` (constrained, onDelete('restrict')).
    *   `specialization_id`: مفتاح خارجي لجدول `specializations` (constrained, onDelete('restrict')).
    *   `degree_id`: مفتاح خارجي لجدول `degrees` (constrained, onDelete('restrict')).
    *   `author_id`: مفتاح خارجي لجدول `authors` (constrained, onDelete('restrict')).
    *   `timestamps`: `created_at` و `updated_at`.
    *   `index(['university_id', 'specialization_id', 'degree_id'])`.
*   **`roles`**:
    *   `id`: مفتاح أساسي.
    *   `name`: اسم الدور (سلسلة نصية، فريد).
    *   `timestamps`: `created_at` و `updated_at`.
*   **`permissions`**:
    *   `id`: مفتاح أساسي.
    *   `name`: اسم الصلاحية (سلسلة نصية، فريد).
    *   `timestamps`: `created_at` و `updated_at`.
*   **`role_permission` (جدول وسيط Many-to-Many)**:
    *   `id`: مفتاح أساسي.
    *   `role_id`: مفتاح خارجي لجدول `roles` (constrained, onDelete('cascade')).
    *   `permission_id`: مفتاح خارجي لجدول `permissions` (constrained, onDelete('cascade')).
    *   `unique(['role_id', 'permission_id'])`: لضمان عدم تكرار العلاقة.
    *   `timestamps`: `created_at` و `updated_at`.
*   **`role_user` (جدول وسيط Many-to-Many)**:
    *   `id`: مفتاح أساسي.
    *   `user_id`: مفتاح خارجي لجدول `users` (constrained, onDelete('cascade')).
    *   `role_id`: مفتاح خارجي لجدول `roles` (constrained, onDelete('cascade')).
    *   `unique(['user_id', 'role_id'])`: لضمان عدم تكرار العلاقة.
    *   `timestamps`: `created_at` و `updated_at`.

**2. تعبئة البيانات الأولية (Seeders)**

المشروع يستخدم Seeders لتعبئة قاعدة البيانات ببيانات أولية ضرورية:

*   **`DatabaseSeeder.php`**:
    *   يستدعي جميع الـ Seeders الأخرى بالترتيب التالي:
        *   `RoleSeeder::class`
        *   `PermissionSeeder::class`
        *   `RolePermissionSeeder::class`
        *   `UserSeeder::class` (تمت إضافته مؤخرًا)
        *   `ImportThesesSeeder::class`
        *   `ImportReservedThesisTitlesSeeder::class`
        *   `ImportThesisTitlesSimpleSeeder::class`
*   **`RoleSeeder.php`**:
    *   يقوم بإنشاء الأدوار الأساسية في جدول `roles`:
        *   `super_admin`
        *   `admin`
        *   `writer-titles`
        *   `writer-theses`
        *   `thesis-and-titles-reader`
*   **`PermissionSeeder.php`**:
    *   يقوم بإنشاء الصلاحيات الأساسية في جدول `permissions`:
        *   `إضافة مستخدمين`
        *   `العناوين محجوزة`
        *   `الرسائل`
        *   `إضافة الجامعات والتعديل عليها`
        *   `إضافة التخصصات والتعديل عليها`
*   **`RolePermissionSeeder.php`**:
    *   يقوم بربط الصلاحيات بالأدوار:
        *   دور `super_admin` يحصل على جميع الصلاحيات.
        *   دور `admin` يحصل على جميع الصلاحيات باستثناء `إضافة مستخدمين`.
        *   دور `writer-titles` يحصل على صلاحية `العناوين محجوزة`.
        *   دور `writer-theses` يحصل على صلاحية `الرسائل`.
        *   دور `thesis-and-titles-reader` يحصل على صلاحيتي `الرسائل` و `العناوين محجوزة`.
*   **`UserSeeder.php`**:
    *   يقوم بإنشاء مستخدم افتراضي لدور `super_admin` إذا لم يكن موجودًا:
        *   `name`: `boraq`
        *   `email`: `boraq@gmail.com`
        *   `password`: `11223344` (يتم تشفيرها باستخدام `Hash::make()`)
    *   يقوم بربط هذا المستخدم بدور `super_admin`.
*   **`ImportThesesSeeder.php`**:
    *   يقرأ ملفات JSON من المسار `storage/app/public/pdfs/json_content`.
    *   لكل ملف JSON، يحاول العثور على ملف PDF مطابق في نفس المجلد.
    *   يقوم بإنشاء أو استرداد (إذا كانت موجودة) سجلات في جداول `universities`, `specializations`, `degrees`, `authors` بناءً على البيانات في JSON.
    *   يقوم بربط الجامعات بالتخصصات في جدول `specialization_university`.
    *   يقوم بإنشاء سجلات في جدول `theses`، مع تجنب التكرار.
    *   يسجل عمليات الاستيراد في ملف `storage/app/public/pdfs/import_log.txt`.
*   **`ImportReservedThesisTitlesSeeder.php`**:
    *   يقرأ البيانات من ملف JSON يقع في `storage_path('app/all_data.json')`.
    *   يقوم بإنشاء سجلات في جدول `reserved_thesis_titles`، مع التحقق من وجود جميع الحقول المطلوبة وتجنب التكرار.
*   **`ImportThesisTitlesSimpleSeeder.php`**:
    *   يقرأ البيانات من نفس ملف JSON (`storage_path('app/all_data.json')`).
    *   يقوم بإنشاء سجلات في جدول `thesis_titles_simple`، مع التحقق من وجود جميع الحقول المطلوبة وتجنب تكرار العنوان.

**3. واجهات برمجة التطبيقات (API Endpoints) والمنطق**

المشروع يوفر مجموعة من واجهات برمجة التطبيقات (APIs) للتعامل مع البيانات:

*   **المصادقة (Authentication)**:
    *   `POST /register`:
        *   **المتحكم**: `App\Http\Controllers\Api\UserController::register`
        *   **الوظيفة**: تسجيل مستخدم جديد.
        *   **التحقق**: يتطلب `name` (مطلوب، نص، بحد أقصى 255)، `email` (مطلوب، بريد إلكتروني، بحد أقصى 255، فريد)، `password` (مطلوب، نص، بحد أدنى 8 أحرف).
        *   **الاستجابة**: يعيد `201 Created` عند النجاح، و `422 Unprocessable Entity` مع تفاصيل الأخطاء عند فشل التحقق.
*   **إدارة المستخدمين والأدوار (User & Role Management)**:
    *   `GET /users`:
        *   **المتحكم**: `App\Http\Controllers\Api\UserController::index`
        *   **الوظيفة**: عرض قائمة بجميع المستخدمين مع أدوارهم وصلاحياتهم.
    *   `PUT /users/{user}/role`:
        *   **المتحكم**: `App\Http\Controllers\Api\UserController::updateUserRole`
        *   **الوظيفة**: تحديث دور مستخدم معين.
        *   **الحماية**: يتطلب مصادقة (`auth:sanctum`) ودور `super_admin` (`role:super_admin`).
        *   **التحقق**: يتطلب `role_id` (مطلوب، موجود في جدول `roles`).
        *   **الاستجابة**: يعيد `200 OK` عند النجاح، `422` عند فشل التحقق، `403` عند عدم التصريح، `404` إذا لم يتم العثور على الدور.
    *   `GET /users/{user}/roles`:
        *   **المتحكم**: `App\Http\Controllers\Api\UserRoleController::index`
        *   **الوظيفة**: عرض الأدوار المعينة لمستخدم معين.
    *   `POST /users/{user}/roles`:
        *   **المتحكم**: `App\Http\Controllers\Api\UserRoleController::assignRole`
        *   **الوظيفة**: تعيين دور لمستخدم.
    *   `DELETE /users/{user}/roles/{role}`:
        *   **المتحكم**: `App\Http\Controllers\Api\UserRoleController::removeRole`
        *   **الوظيفة**: إزالة دور من مستخدم.
*   **إدارة الأدوار والصلاحيات (Roles & Permissions Management)**:
    *   `GET /roles/{role}/permissions`:
        *   **المتحكم**: `App\Http\Controllers\Api\RoleController::showPermissions`
        *   **الوظيفة**: عرض الصلاحيات لدور معين.
    *   `GET /roles-with-permissions`:
        *   **المتحكم**: `App\Http\Controllers\Api\RoleController::indexWithPermissions`
        *   **الوظيفة**: عرض جميع الأدوار مع صلاحياتها المرتبطة.
*   **إحصائيات عامة (General Statistics)**:
    *   `GET /stats`:
        *   **المتحكم**: `App\Http\Controllers\Api\StatsController::index`
        *   **الوظيفة**: عرض إحصائيات عامة.
*   **إدارة الرسائل (Theses Management)**:
    *   `GET /theses/latest`:
        *   **المتحكم**: `App\Http\Controllers\Api\StatsController::latestTheses`
        *   **الوظيفة**: عرض أحدث الرسائل.
    *   `GET /theses/search`:
        *   **المتحكم**: `App\Http\Controllers\Api\StatsController::searchTheses`
        *   **الوظيفة**: البحث عن الرسائل.
    *   `GET /theses/search-guests`:
        *   **المتحكم**: `App\Http\Controllers\Api\StatsController::searchThesesForGuests`
        *   **الوظيفة**: البحث عن الرسائل للضيوف.
    *   `POST /theses`:
        *   **المتحكم**: `App\Http\Controllers\Api\StatsController::storeThesis`
        *   **الوظيفة**: إضافة رسالة جديدة.
    *   `PUT /theses/{id}`:
        *   **المتحكم**: `App\Http\Controllers\Api\StatsController::updateThesis`
        *   **الوظيفة**: تحديث رسالة موجودة.
    *   `DELETE /theses/{id}`:
        *   **المتحكم**: `App\Http\Controllers\Api\StatsController::deleteThesis`
        *   **الوظيفة**: حذف رسالة.
    *   `GET /theses/years`:
        *   **المتحكم**: `App\Http\Controllers\Api\StatsController::allYears`
        *   **الوظيفة**: عرض جميع السنوات المتاحة للرسائل.
*   **الفلاتر (Dropdowns)**:
    *   `GET /specializations`:
        *   **المتحكم**: `App\Http\Controllers\Api\StatsController::allSpecializations`
        *   **الوظيفة**: عرض جميع التخصصات.
    *   `GET /universities`:
        *   **المتحكم**: `App\Http\Controllers\Api\StatsController::allUniversities`
        *   **الوظيفة**: عرض جميع الجامعات.
    *   `GET /degrees`:
        *   **المتحكم**: `App\Http\Controllers\Api\StatsController::allDegrees`
        *   **الوظيفة**: عرض جميع الدرجات العلمية.
*   **الجامعات والتخصصات (Universities & Specializations)**:
    *   `GET /universities-with-specializations`:
        *   **المتحكم**: `App\Http\Controllers\Api\StatsController::universitiesWithSpecializations`
        *   **الوظيفة**: عرض الجامعات مع تخصصاتها.
    *   `GET /universities-with-specializations-guests`:
        *   **المتحكم**: `App\Http\Controllers\Api\StatsController::universitiesWithSpecializationsForGuests`
        *   **الوظيفة**: عرض الجامعات مع تخصصاتها للضيوف.
    *   `GET /universities/search`:
        *   **المتحكم**: `App\Http\Controllers\Api\StatsController::searchUniversities`
        *   **الوظيفة**: البحث عن الجامعات.
    *   `POST /universities/{university}/add-specialization`:
        *   **المتحكم**: `App\Http\Controllers\Api\StatsController::addSpecializationToUniversity`
        *   **الوظيفة**: إضافة تخصص لجامعة معينة.
*   **الأرشيف (Archived Theses)**:
    *   `GET /archived-theses`:
        *   **المتحكم**: `App\Http\Controllers\Api\StatsController::getArchivedTheses`
        *   **الوظيفة**: عرض الرسائل المؤرشفة.
    *   `DELETE /archived-theses/{id}`:
        *   **المتحكم**: `App\Http\Controllers\Api\StatsController::deleteArchivedThesis`
        *   **الوظيفة**: حذف رسالة مؤرشفة.
    *   `POST /archived-theses/{id}/restore`:
        *   **المتحكم**: `App\Http\Controllers\Api\StatsController::restoreThesis`
        *   **الوظيفة**: استعادة رسالة من الأرشيف.
*   **عناوين الرسائل البسيطة (ThesisTitlesSimple)**:
    *   `GET /thesis-titles-simple`:
        *   **المتحكم**: `App\Http\Controllers\Api\StatsController::getThesisTitlesSimple`
        *   **الوظيفة**: عرض عناوين الرسائل البسيطة.
    *   `GET /thesis-titles-simple/{id}`:
        *   **المتحكم**: `App\Http\Controllers\Api\StatsController::showThesisTitleSimple`
        *   **الوظيفة**: عرض عنوان رسالة بسيط محدد.
    *   `POST /thesis-titles-simple`:
        *   **المتحكم**: `App\Http\Controllers\Api\StatsController::storeThesisTitleSimple`
        *   **الوظيفة**: إضافة عنوان رسالة بسيط جديد.
    *   `POST /thesis-titles-simple/add`:
        *   **المتحكم**: `App\Http\Controllers\Api\StatsController::storeThesisTitleSimpleFromQuery`
        *   **الوظيفة**: إضافة عنوان رسالة بسيط من استعلام.
    *   `PUT /thesis-titles-simple/{id}`:
        *   **المتحكم**: `App\Http\Controllers\Api\StatsController::updateThesisTitleSimple`
        *   **الوظيفة**: تحديث عنوان رسالة بسيط موجود.
    *   `DELETE /thesis-titles-simple/{id}`:
        *   **المتحكم**: `App\Http\Controllers\Api\StatsController::deleteThesisTitleSimple`
        *   **الوظيفة**: حذف عنوان رسالة بسيط.
    *   `GET /thesis-titles-simple-latest`:
        *   **المتحكم**: `App\Http\Controllers\Api\StatsController::latestThesisTitlesSimple`
        *   **الوظيفة**: عرض أحدث عناوين الرسائل البسيطة.
    *   `GET /thesis-titles-simple-search`:
        *   **المتحكم**: `App\Http\Controllers\Api\StatsController::searchThesisTitlesSimple`
        *   **الوظيفة**: البحث عن عناوين الرسائل البسيطة.
*   **عناوين الرسائل المحجوزة (ReservedThesisTitles)**:
    *   `GET /reserved-thesis-titles-latest`:
        *   **المتحكم**: `App\Http\Controllers\Api\StatsController::latestReservedThesisTitles`
        *   **الوظيفة**: عرض أحدث عناوين الرسائل المحجوزة.
    *   `GET /reserved-thesis-titles-latest-guests`:
        *   **المتحكم**: `App\Http\Controllers\Api\StatsController::latestReservedThesisTitlesForGuests`
        *   **الوظيفة**: عرض أحدث عناوين الرسائل المحجوزة للضيوف.
    *   `POST /reserved-thesis-titles`:
        *   **المتحكم**: `App\Http\Controllers\Api\StatsController::storeReservedThesisTitle`
        *   **الوظيفة**: إضافة عنوان رسالة محجوز جديد.
    *   `PUT /reserved-thesis-titles/{id}`:
        *   **المتحكم**: `App\Http\Controllers\Api\StatsController::updateReservedThesisTitle`
        *   **الوظيفة**: تحديث عنوان رسالة محجوز موجود.
    *   `DELETE /reserved-thesis-titles/{id}`:
        *   **المتحكم**: `App\Http\Controllers\Api\StatsController::deleteReservedThesisTitle`
        *   **الوظيفة**: حذف عنوان رسالة محجوز.
    *   `GET /reserved-thesis-titles-search`:
        *   **المتحكم**: `App\Http\Controllers\Api\StatsController::searchReservedThesisTitles`
        *   **الوظيفة**: البحث عن عناوين الرسائل المحجوزة.
    *   `GET /reserved-thesis-titles-search-guests`:
        *   **المتحكم**: `App\Http\Controllers\Api\StatsController::searchReservedThesisTitlesForGuests`
        *   **الوظيفة**: البحث عن عناوين الرسائل المحجوزة للضيوف.
*   **ملفات PDF المشفرة**:
    *   `GET /pdf/{token}`:
        *   **المتحكم**: `App\Http\Controllers\Api\StatsController::servePdf`
        *   **الوظيفة**: تقديم ملف PDF مشفر.

**4. Middleware المخصص**

*   **`App\Http\Middleware\RoleMiddleware.php`**:
    *   **الوظيفة**: يتحقق مما إذا كان المستخدم المصادق عليه يمتلك دورًا محددًا.
    *   **المنطق**: إذا لم يكن المستخدم مسجل الدخول أو لم يكن لديه الدور المطلوب، فإنه يعيد استجابة `403 Unauthorized`.
    *   **التسجيل**: تم تسجيله في [`bootstrap/app.php`](bootstrap/app.php:14) باسم مستعار `role` ليتم استخدامه في تعريفات المسارات.

**5. النماذج (Models)**

المشروع يستخدم نماذج Eloquent التالية لتمثيل الجداول والعلاقات:

*   `User` (مع علاقة `roles` وطريقة `hasRole`)
*   `Role` (مع علاقة `permissions`)
*   `Permission`
*   `University` (مع علاقة `specializations`)
*   `Specialization` (مع علاقة `universities`)
*   `Degree`
*   `Author`
*   `Thesis`
*   `ReservedThesisTitle`
*   `ThesisTitlesSimple`
*   `ArchiveThesis`

**6. تعليمات الإعداد لمشروع Laravel جديد**

لإنشاء هذا المشروع من الصفر، اتبع الخطوات التالية:

1.  **إنشاء مشروع Laravel جديد**:
    ```bash
    composer create-project laravel/laravel your-project-name
    cd your-project-name
    ```
2.  **تكوين قاعدة البيانات**:
    *   قم بتعديل ملف `.env` لربط قاعدة البيانات الخاصة بك (MySQL, PostgreSQL, SQLite, etc.).
3.  **إنشاء Migrations**:
    *   قم بإنشاء جميع ملفات Migrations المذكورة في القسم 1 (باستثناء Migrations الافتراضية التي تأتي مع Laravel مثل `users`, `cache`, `jobs`).
    *   انسخ محتوى كل ملف Migration إلى الملف المقابل في مشروعك الجديد.
    *   **ملاحظة**: تأكد من ترتيب Migrations بشكل صحيح (حسب التاريخ والوقت) لضمان إنشاء الجداول والعلاقات التابعة بشكل صحيح.
4.  **تشغيل Migrations**:
    ```bash
    php artisan migrate
    ```
5.  **إنشاء Models**:
    *   قم بإنشاء جميع ملفات Models المذكورة في القسم 5.
    *   انسخ محتوى كل ملف Model إلى الملف المقابل في مشروعك الجديد، بما في ذلك العلاقات والطرق المخصصة مثل `hasRole()` في نموذج `User`.
6.  **إنشاء Seeders**:
    *   قم بإنشاء جميع ملفات Seeders المذكورة في القسم 2.
    *   انسخ محتوى كل ملف Seeder إلى الملف المقابل في مشروعك الجديد.
    *   **ملاحظة**: تأكد من إنشاء مجلد `storage/app/public/pdfs/json_content` وملف `storage/app/all_data.json` (حتى لو كانت فارغة في البداية) لتجنب الأخطاء أثناء تشغيل الـ Seeders التي تعتمد عليها.
7.  **تشغيل Seeders**:
    ```bash
    php artisan db:seed
    ```
8.  **إنشاء Controllers**:
    *   قم بإنشاء المتحكمات التالية:
        *   `App\Http\Controllers\Api\UserController.php`
        *   `App\Http\Controllers\Api\RoleController.php`
        *   `App\Http\Controllers\Api\StatsController.php`
        *   `App\Http\Controllers\Api\UserRoleController.php`
    *   انسخ محتوى كل متحكم إلى الملف المقابل.
9.  **إنشاء Middleware**:
    *   قم بإنشاء ملف [`app/Http/Middleware/RoleMiddleware.php`](app/Http/Middleware/RoleMiddleware.php).
    *   انسخ المحتوى الخاص به.
    *   قم بتسجيله في ملف [`bootstrap/app.php`](bootstrap/app.php:14) ضمن قسم `withMiddleware` كما هو موضح في القسم 4.
10. **تحديد مسارات API**:
    *   قم بتعديل ملف [`routes/api.php`](routes/api.php) لإضافة جميع المسارات المذكورة في القسم 3.
    *   تأكد من استيراد المتحكمات والـ Middleware اللازمة في بداية الملف.
11. **تثبيت Laravel Sanctum (للمصادقة API)**:
    *   إذا لم يكن مثبتًا بالفعل:
        ```bash
        composer require laravel/sanctum
        php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
        php artisan migrate
        ```
    *   تأكد من إضافة `Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful` إلى مجموعة `api` middleware في `bootstrap/app.php` إذا كنت تستخدم SPA.

باتباع هذه الخطوات، يمكنك إعادة بناء المشروع بالكامل مع جميع الجداول، البيانات الأولية، منطق API، ونظام الأدوار والصلاحيات.