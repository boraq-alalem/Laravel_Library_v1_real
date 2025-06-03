<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\StatsController;

Route::get('/stats', [StatsController::class, 'index']);
Route::get('/theses/latest', [StatsController::class, 'latestTheses']);
Route::get('/specializations', [StatsController::class, 'allSpecializations']);
Route::get('/universities', [StatsController::class, 'allUniversities']);
Route::get('/degrees', [StatsController::class, 'allDegrees']);
Route::get('/theses/years', [StatsController::class, 'allYears']);
Route::get('/theses/search', [StatsController::class, 'searchTheses']);
Route::put('/theses/{id}', [StatsController::class, 'updateThesis']);
Route::delete('/theses/{id}', [StatsController::class, 'deleteThesis']);
