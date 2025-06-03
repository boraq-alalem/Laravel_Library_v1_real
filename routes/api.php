<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\StatsController;

Route::get('/stats', [StatsController::class, 'index']);
