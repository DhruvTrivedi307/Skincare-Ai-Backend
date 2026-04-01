<?php

use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\GeminiAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/analyze', [GeminiAuthController::class, "analyze"])->middleware('throttle:ai-analyze','admin-token');

Route::get('/all-analysis', [AdminController::class, "getAllAnalysis"])->middleware('admin-token');

Route::get('/analysis/{id}', [AdminController::class, 'getResult'])->middleware('admin-token');

Route::get('/get-users', [AdminController::class, "getUsers"])->middleware('admin-token');

Route::get('/get-scans', [AdminController::class, "getScans"])->middleware('admin-token');

Route::get('/get-token-usage', [AdminController::class, "getTokenUsage"])->middleware('admin-token');