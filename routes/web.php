<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\CustomerAuthController;
use App\Http\Controllers\HealthController;
use Illuminate\Support\Facades\Route;

// Landing Page Utama (/)
Route::get('/', function () {
    return view('landing');
})->name('home');

// Halaman Sewa / Portal Pemesanan (/sewa, /sewa/kalender, /sewa/riwayat) — Wajib Login Penyewa
Route::get('/sewa', [CustomerAuthController::class, 'bookingPortal'])->name('booking.portal');
Route::get('/sewa/kalender', [CustomerAuthController::class, 'calendarPortal'])->name('customer.calendar');
Route::get('/sewa/riwayat', [CustomerAuthController::class, 'historyPortal'])->name('customer.history');

// Autentikasi Penyewa / Customer (Login & Register)
Route::get('/login', [CustomerAuthController::class, 'showLogin'])->name('customer.login');
Route::post('/login', [CustomerAuthController::class, 'login'])->name('customer.login.submit');
Route::get('/register', [CustomerAuthController::class, 'showRegister'])->name('customer.register');
Route::post('/register', [CustomerAuthController::class, 'register'])->name('customer.register.submit');
Route::post('/logout', [CustomerAuthController::class, 'logout'])->name('customer.logout');

// Portal Admin Coworking (4 Halaman Terpisah)
Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
Route::get('/admin', [AdminAuthController::class, 'dashboard'])->name('admin.dashboard');
Route::get('/admin/fasilitas', [AdminAuthController::class, 'facilities'])->name('admin.facilities');
Route::get('/admin/bookings', [AdminAuthController::class, 'bookings'])->name('admin.bookings');
Route::get('/admin/kalender', [AdminAuthController::class, 'calendar'])->name('admin.calendar');

// Healthcheck Endpoint (FR-13)
Route::get('/health', HealthController::class);
