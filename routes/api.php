<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\StatsController;

// =====================
// 1. الإحصائيات العامة
// =====================
Route::get('/stats', [StatsController::class, 'index']);

// =====================
// 2. الرسائل (Theses)
// =====================
Route::get('/theses/latest', [StatsController::class, 'latestTheses']);
Route::get('/theses/search', [StatsController::class, 'searchTheses']);
Route::get('/theses/search-guests', [StatsController::class, 'searchThesesForGuests']);
Route::get('/theses/{id}', [StatsController::class, 'showThesis']); // البحث عن رسالة بالمعرّف
Route::post('/theses', [StatsController::class, 'storeThesis']);
Route::put('/theses/{id}', [StatsController::class, 'updateThesis']);
Route::delete('/theses/{id}', [StatsController::class, 'deleteThesis']);
Route::get('/theses/years', [StatsController::class, 'allYears']);

// =====================
// 3. الفلاتر (Dropdowns)
// =====================
Route::get('/specializations', [StatsController::class, 'allSpecializations']);
Route::get('/universities', [StatsController::class, 'allUniversities']);
Route::get('/degrees', [StatsController::class, 'allDegrees']);

// =====================
// 4. الجامعات والتخصصات
// =====================
Route::get('/universities-with-specializations', [StatsController::class, 'universitiesWithSpecializations']);
Route::get('/universities-with-specializations-guests', [StatsController::class, 'universitiesWithSpecializationsForGuests']);
Route::get('/universities/search', [StatsController::class, 'searchUniversities']);
Route::post('/universities/{university}/add-specialization', [StatsController::class, 'addSpecializationToUniversity']);
Route::post('/universities', [StatsController::class, 'storeUniversity']); // إضافة جامعة جديدة
Route::delete('/universities/{id}', [StatsController::class, 'deleteUniversity']); // حذف جامعة

Route::post('/specializations', [StatsController::class, 'storeSpecialization']); // إضافة تخصص جديد
Route::delete('/specializations/{id}', [StatsController::class, 'deleteSpecialization']); // حذف تخصص
Route::get('/specializations/search', [StatsController::class, 'searchSpecializations']); // بحث عن تخصص

// =====================
// 5. الأرشيف (Archived Theses)
// =====================
Route::get('/archived-theses', [StatsController::class, 'getArchivedTheses']);
Route::delete('/archived-theses/{id}', [StatsController::class, 'deleteArchivedThesis']);
Route::post('/archived-theses/{id}/restore', [StatsController::class, 'restoreThesis']);

// =====================
// 6. عناوين الرسائل البسيطة (ThesisTitlesSimple)
// =====================
Route::get('/thesis-titles-simple', [StatsController::class, 'getThesisTitlesSimple']);
Route::get('/thesis-titles-simple/{id}', [StatsController::class, 'showThesisTitleSimple']);
Route::post('/thesis-titles-simple', [StatsController::class, 'storeThesisTitleSimple']);
Route::post('/thesis-titles-simple/add', [StatsController::class, 'storeThesisTitleSimpleFromQuery']);
Route::put('/thesis-titles-simple/{id}', [StatsController::class, 'updateThesisTitleSimple']);
Route::delete('/thesis-titles-simple/{id}', [StatsController::class, 'deleteThesisTitleSimple']);
Route::get('/thesis-titles-simple-latest', [StatsController::class, 'latestThesisTitlesSimple']);
Route::get('/thesis-titles-simple-search', [StatsController::class, 'searchThesisTitlesSimple']);

// =====================
// 7. عناوين الرسائل المحجوزة (ReservedThesisTitles)
// =====================
Route::get('/reserved-thesis-titles-latest', [StatsController::class, 'latestReservedThesisTitles']);
Route::get('/reserved-thesis-titles-latest-guests', [StatsController::class, 'latestReservedThesisTitlesForGuests']);
Route::post('/reserved-thesis-titles', [StatsController::class, 'storeReservedThesisTitle']);
Route::put('/reserved-thesis-titles/{id}', [StatsController::class, 'updateReservedThesisTitle']);
Route::delete('/reserved-thesis-titles/{id}', [StatsController::class, 'deleteReservedThesisTitle']);
Route::get('/reserved-thesis-titles-search', [StatsController::class, 'searchReservedThesisTitles']);
Route::get('/reserved-thesis-titles-search-guests', [StatsController::class, 'searchReservedThesisTitlesForGuests']);
Route::get('/reserved-thesis-titles-search-person', [StatsController::class, 'searchReservedThesisTitlesByPerson']);
Route::get('/reserved-thesis-titles-search-person-guests', [StatsController::class, 'searchReservedThesisTitlesByPersonForGuests']);

// =====================
// 8. ملفات PDF المشفرة
// =====================
Route::get('/pdf/{token}', [StatsController::class, 'servePdf']);

// =====================
// 18. مسح الكاش
// =====================
Route::post('/clear-cache', [\App\Http\Controllers\Api\CacheController::class, 'clearCache']);


// =====================
// 9. الأدوار والصلاحيات (Roles with Permissions)
Route::get('/roles/{role}/permissions', [App\Http\Controllers\Api\RoleController::class, 'showPermissions']);
// =====================
Route::get('/roles-with-permissions', [App\Http\Controllers\Api\RoleController::class, 'indexWithPermissions']);

// =====================
// 10. أدوار المستخدمين (User Roles)
// =====================
Route::get('/users/{user}/roles', [App\Http\Controllers\Api\UserRoleController::class, 'index']);
Route::post('/users/{user}/roles', [App\Http\Controllers\Api\UserRoleController::class, 'assignRole']);
Route::delete('/users/{user}/roles/{role}', [App\Http\Controllers\Api\UserRoleController::class, 'removeRole']);

// =====================
// 11. المصادقة (Authentication)
// =====================
// Route::post('/register', [App\Http\Controllers\Api\UserController::class, 'register']); // Disabled as per request
Route::post('/login', [App\Http\Controllers\Api\UserController::class, 'login']);

// =====================
// 12. تعديل دور المستخدم (Update User Role)
// =====================
Route::middleware(['auth:sanctum', 'role:super_admin'])->group(function () {
    // User Management (accessible only by super_admin)
    Route::post('/users', [App\Http\Controllers\Api\UserController::class, 'store']); // Create User
    Route::put('/users/{user}', [App\Http\Controllers\Api\UserController::class, 'update']); // Update User
    Route::delete('/users/{user}', [App\Http\Controllers\Api\UserController::class, 'destroy']); // Delete User
});

// =====================
// 15. سجل نشاط Super Admin (Super Admin Activity Log)
// =====================
Route::middleware(['auth:sanctum', 'role:super_admin'])->group(function () {
    Route::get('/super-admin/{superAdmin}/activity-log', [App\Http\Controllers\Api\UserActivityLogController::class, 'getSuperAdminActivityLog']);
});

// =====================
// 14. عرض المستخدمين باستثناء super_admin (Users without Super Admin)
// =====================
Route::middleware(['auth:sanctum', 'role:super_admin'])->group(function () {
    Route::get('/users-without-super-admin', [App\Http\Controllers\Api\UserController::class, 'indexWithoutSuperAdmin']);
});

// =====================
// 13. سجل نشاط المستخدم (User Activity Log)
// =====================
Route::middleware(['auth:sanctum', 'role:super_admin'])->group(function () {
    Route::get('/super-admin/added-users', [App\Http\Controllers\Api\UserActivityLogController::class, 'getAddedUsersBySuperAdmin']);
    Route::get('/super-admin/users/{user}/role-history', [App\Http\Controllers\Api\UserActivityLogController::class, 'getUserRoleHistory']);
});

// =====================
// 16. تخزين UUIDs
// =====================
Route::post('/uuids', [StatsController::class, 'storeUuid']);
// عرض جميع العناصر مع id_local و id_remote فقط
Route::get('/uuids', [StatsController::class, 'listUuids']);
// البحث عن عنصر حسب id_local أو id_remote
Route::get('/uuids/search', [StatsController::class, 'searchUuid']);
// حذف عنصر حسب id_local أو id_remote
Route::delete('/uuids', [StatsController::class, 'deleteUuid']);

// =====================
// 17. تخزين User UUIDs
// =====================
Route::post('/user-uuids', [StatsController::class, 'storeUserUuid']);
// عرض جميع العناصر مع id_local و id_remote فقط
Route::get('/user-uuids', [StatsController::class, 'listUserUuids']);
// البحث عن عنصر حسب id_local أو id_remote أو user_id
Route::get('/user-uuids/search', [StatsController::class, 'searchUserUuid']);
// حذف عنصر حسب id_local أو id_remote أو user_id
Route::delete('/user-uuids', [StatsController::class, 'deleteUserUuid']);
