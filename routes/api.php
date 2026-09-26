<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserDeviceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::prefix('v1')->group(function (){

 Route::controller(AuthController::class)->group(function () {
        Route::post('auth/register', 'register');
        Route::post('auth/login', 'login')->middleware('throttle:login');
    });

    Route::middleware('auth:sanctum')->group(function () {

        Route::controller(AuthController::class)->group(function () {
            Route::post('auth/logout', 'logout');
        });

        Route::controller(UserDeviceController::class)->group(function () {
            Route::post('device/register', 'registerDevice');
        });


         Route::post(
        'attendance/check-in',
        [AttendanceController::class, 'checkIn']
    );

    });
});