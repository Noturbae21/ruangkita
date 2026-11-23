<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\CustomerAuthController;
use App\Http\Controllers\HealthController;
use Illuminate\Support\Facades\Route;

// Landing Page Utama (/)
Route::get('/', function () {
    return view('landing');
})->name('home');

// Halaman Sewa / Portal Pemesanan (/sewa) — Wajib Login Penyewa Terlebih Dahulu
Route::get('/sewa', [CustomerAuthController::class, 'bookingPortal'])->name('booking.portal');

// Autentikasi Penyewa / Customer (Login & Register)
Route::get('/login', [CustomerAuthController::class, 'showLogin'])->name('customer.login');
Route::post('/login', [CustomerAuthController::class, 'login'])->name('customer.login.submit');
Route::get('/register', [CustomerAuthController::class, 'showRegister'])->name('customer.register');
Route::post('/register', [CustomerAuthController::class, 'register'])->name('customer.register.submit');
Route::post('/logout', [CustomerAuthController::class, 'logout'])->name('customer.logout');

// Portal Admin Coworking
Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
Route::get('/admin', [AdminAuthController::class, 'dashboard'])->name('admin.dashboard');

// Healthcheck Endpoint (FR-13)
Route::get('/health', HealthController::class);
