<?php

use App\Http\Controllers\Api\AdditionalFacilityController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\CalendarController;
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\HealthController;
use Illuminate\Support\Facades\Route;

Route::get('/health', HealthController::class);

Route::get('/rooms', [RoomController::class, 'index']);
Route::post('/rooms', [RoomController::class, 'store']);
Route::get('/rooms/{id}', [RoomController::class, 'show'])->whereNumber('id');
Route::put('/rooms/{id}', [RoomController::class, 'update'])->whereNumber('id');
Route::patch('/rooms/{id}', [RoomController::class, 'update'])->whereNumber('id');
Route::delete('/rooms/{id}', [RoomController::class, 'destroy'])->whereNumber('id');
Route::get('/rooms/{id}/availability', [RoomController::class, 'availability'])->whereNumber('id');

Route::get('/additional-facilities', [AdditionalFacilityController::class, 'index']);
Route::post('/additional-facilities', [AdditionalFacilityController::class, 'store']);
Route::get('/additional-facilities/{id}', [AdditionalFacilityController::class, 'show'])->whereNumber('id');
Route::put('/additional-facilities/{id}', [AdditionalFacilityController::class, 'update'])->whereNumber('id');
Route::patch('/additional-facilities/{id}', [AdditionalFacilityController::class, 'update'])->whereNumber('id');
Route::delete('/additional-facilities/{id}', [AdditionalFacilityController::class, 'destroy'])->whereNumber('id');

Route::post('/bookings', [BookingController::class, 'store']);
Route::get('/bookings', [BookingController::class, 'index']);
Route::get('/bookings/{id}', [BookingController::class, 'show'])->whereNumber('id');
Route::get('/bookings/{id}/qris', [BookingController::class, 'qris'])->whereNumber('id');
Route::post('/bookings/{id}/pay', [BookingController::class, 'pay'])->whereNumber('id');
Route::post('/bookings/{id}/cancel', [BookingController::class, 'cancel'])->whereNumber('id');

Route::get('/calendar', [CalendarController::class, 'index']);
