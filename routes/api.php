<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\StatsController;
use App\Http\Controllers\Api\ThesisController;
use App\Http\Controllers\Api\UniversityController;
use App\Http\Controllers\Api\SpecializationController;
use App\Http\Controllers\Api\DegreeController;
use App\Http\Controllers\Api\ArchiveThesisController;
use App\Http\Controllers\Api\ThesisTitlesSimpleController;
use App\Http\Controllers\Api\ReservedThesisTitleController;

// =====================
// 1. الإحصائيات العامة
// =====================
Route::get('/stats', [StatsController::class, 'index']);

// =====================
// 2. الرسائل (Theses)
// =====================
Route::get('/theses/latest', [ThesisController::class, 'latestTheses']);
Route::get('/theses/search', [ThesisController::class, 'searchTheses']);
Route::get('/theses/search-guests', [ThesisController::class, 'searchThesesForGuests']);
Route::post('/theses', [ThesisController::class, 'storeThesis']);
Route::put('/theses/{id}', [ThesisController::class, 'updateThesis']);
Route::delete('/theses/{id}', [ThesisController::class, 'deleteThesis']);
Route::get('/theses/years', [ThesisController::class, 'allYears']);

// =====================
// 3. الفلاتر (Dropdowns)
// =====================
Route::get('/specializations', [SpecializationController::class, 'allSpecializations']);
Route::get('/universities', [UniversityController::class, 'allUniversities']);
Route::get('/degrees', [DegreeController::class, 'allDegrees']);

// =====================
// 4. الجامعات والتخصصات
// =====================
Route::get('/universities-with-specializations', [UniversityController::class, 'universitiesWithSpecializations']);
Route::get('/universities-with-specializations-guests', [UniversityController::class, 'universitiesWithSpecializationsForGuests']);
Route::get('/universities/search', [UniversityController::class, 'searchUniversities']);
Route::post('/universities/{university}/add-specialization', [UniversityController::class, 'addSpecializationToUniversity']);

// =====================
// 5. الأرشيف (Archived Theses)
// =====================
Route::get('/archived-theses', [ArchiveThesisController::class, 'getArchivedTheses']);
Route::delete('/archived-theses/{id}', [ArchiveThesisController::class, 'deleteArchivedThesis']);
Route::post('/archived-theses/{id}/restore', [ArchiveThesisController::class, 'restoreThesis']);

// =====================
// 6. عناوين الرسائل البسيطة (ThesisTitlesSimple)
// =====================
Route::get('/thesis-titles-simple', [ThesisTitlesSimpleController::class, 'getThesisTitlesSimple']);
Route::get('/thesis-titles-simple/{id}', [ThesisTitlesSimpleController::class, 'showThesisTitleSimple']);
Route::post('/thesis-titles-simple', [ThesisTitlesSimpleController::class, 'storeThesisTitleSimple']);
Route::post('/thesis-titles-simple/add', [ThesisTitlesSimpleController::class, 'storeThesisTitleSimpleFromQuery']);
Route::put('/thesis-titles-simple/{id}', [ThesisTitlesSimpleController::class, 'updateThesisTitleSimple']);
Route::delete('/thesis-titles-simple/{id}', [ThesisTitlesSimpleController::class, 'deleteThesisTitleSimple']);
Route::get('/thesis-titles-simple-latest', [ThesisTitlesSimpleController::class, 'latestThesisTitlesSimple']);
Route::get('/thesis-titles-simple-search', [ThesisTitlesSimpleController::class, 'searchThesisTitlesSimple']);

// =====================
// 7. عناوين الرسائل المحجوزة (ReservedThesisTitles)
// =====================
Route::get('/reserved-thesis-titles-latest', [ReservedThesisTitleController::class, 'latestReservedThesisTitles']);
Route::get('/reserved-thesis-titles-latest-guests', [ReservedThesisTitleController::class, 'latestReservedThesisTitlesForGuests']);
Route::post('/reserved-thesis-titles', [ReservedThesisTitleController::class, 'storeReservedThesisTitle']);
Route::put('/reserved-thesis-titles/{id}', [ReservedThesisTitleController::class, 'updateReservedThesisTitle']);
Route::delete('/reserved-thesis-titles/{id}', [ReservedThesisTitleController::class, 'deleteReservedThesisTitle']);
Route::get('/reserved-thesis-titles-search', [ReservedThesisTitleController::class, 'searchReservedThesisTitles']);
Route::get('/reserved-thesis-titles-search-guests', [ReservedThesisTitleController::class, 'searchReservedThesisTitlesForGuests']);

// =====================
// 8. ملفات PDF المشفرة
// =====================
Route::get('/pdf/{token}', [ThesisController::class, 'servePdf']);


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
