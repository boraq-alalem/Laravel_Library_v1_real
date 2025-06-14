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
