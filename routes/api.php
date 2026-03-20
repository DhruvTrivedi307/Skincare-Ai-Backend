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

Route::get('/analysis/{id}', [GeminiAuthController::class, 'result']);

Route::get('/products', [ProductController::class, 'getProducts']);
