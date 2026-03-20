<?php

use App\Http\Controllers\Admin\ActivityController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\UserAuthController;
use App\Models\SkinAnalysis;
use Illuminate\Support\Facades\Route;

Route::get('/', [LoginController::class, "landing"]);

Route::get('/login', function () {
    return view('login');
})->name('login');

Route::get('/register', function () {
    return view('sign-up');
})->name('register');

Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');


Route::get('/dashboard', [DashboardController::class, "fetch_admin"])->name('dashboard');
Route::get('/profile', [DashboardController::class, "profile"])->name('profile');
Route::put('/profile', [DashboardController::class, "updateProfile"])->name('profile.update');
Route::post('/profile', [DashboardController::class, "updatePassword"])->name('password.update');

Route::get('/subscriptions', [DashboardController::class, "subscriptions"])->name('subscriptions');
Route::post('/subscriptions', [DashboardController::class, "add_subscriptions"])->name('subscriptions.add');
Route::put('/subscriptions/{id}', [DashboardController::class, "update_subscriptions"])->name('subscriptions.update');
Route::delete('/subscriptions/{id}', [DashboardController::class, "delete_subscriptions"])->name('subscriptions.delete');

Route::get('/coupon-codes', [DashboardController::class, "coupon_codes"])->name('coupon-codes');
Route::post('/coupon-codes', [DashboardController::class, "add_coupon_code"])->name('coupon-codes.add');
Route::put('/coupon-codes/{id}', [DashboardController::class, "update_coupon_code"])->name('coupon-codes.update');
Route::delete('/coupon-codes/{id}', [DashboardController::class, "delete_coupon_code"])->name('coupon-codes.delete');

Route::get('/activity-logs', [ActivityController::class, "activity_logs"])->name('activity-logs');

Route::get('/users', [CustomerController::class, "customers"])->name("users");
Route::post('/add-customer', [CustomerController::class, "addCustomers"])->name("add-customer");
Route::post('/edit-customer', [CustomerController::class, "editCustomers"])->name("edit-customer");
Route::post('/delete-customer', [CustomerController::class, "deleteCustomers"])->name("delete-customer");

Route::get('/usage-metadata', [DashboardController::class, "usage_metadata"])->name('usage-metadata');

Route::get('/products', [ProductController::class, "products"])->name('products');
Route::post('/products', [ProductController::class, "add_product"])->name('products.add');
Route::put('/products/{id}', [ProductController::class, "update_product"])->name('products.update');
Route::delete('/products/{id}', [ProductController::class, "delete_product"])->name('products.delete');
Route::post('/concern', [ProductController::class, "concern_add"])->name('concern.add');
Route::delete('/concern/{id}', [ProductController::class, "concern_delete"])->name('concern.delete');