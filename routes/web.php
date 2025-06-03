<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ThesisController;
use App\Http\Controllers\UniversityController;
use App\Http\Controllers\SpecializationController;
use App\Http\Controllers\DegreeController;
use App\Http\Controllers\AuthorController;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('theses', ThesisController::class);
Route::resource('universities', UniversityController::class);
Route::resource('specializations', SpecializationController::class);
Route::resource('degrees', DegreeController::class);
Route::resource('authors', AuthorController::class);
