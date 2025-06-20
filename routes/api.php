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

// =====================
// 8. ملفات PDF المشفرة
// =====================
Route::get('/pdf/{token}', [StatsController::class, 'servePdf']);


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
Route::post('/register', [App\Http\Controllers\Api\UserController::class, 'register']);

// =====================
// 12. تعديل دور المستخدم (Update User Role)
// =====================
Route::middleware(['auth:sanctum', 'role:super_admin'])->put('/users/{user}/role', [App\Http\Controllers\Api\UserController::class, 'updateUserRole']);
