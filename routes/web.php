<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\HealthController;
use Illuminate\Support\Facades\Route;

// Portal Pengguna / Pemesan
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Portal Admin Coworking
Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
Route::get('/admin', [AdminAuthController::class, 'dashboard'])->name('admin.dashboard');

// Healthcheck Endpoint (FR-13)
Route::get('/health', HealthController::class);
