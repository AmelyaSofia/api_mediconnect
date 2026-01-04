<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\AppointmentController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::post('/user/update', [UserController::class, 'update']);
    Route::delete('/user/delete', [UserController::class, 'destroy']);

    Route::get('/users', [UserController::class,'index']);

    Route::get('/doctors', [DoctorController::class, 'index']);
    Route::get('/doctors/{id}', [DoctorController::class, 'show']);

    Route::post('/doctors', [DoctorController::class, 'store']);
    Route::post('/doctors/{id}', [DoctorController::class, 'update']);
    Route::delete('/doctors/{id}', [DoctorController::class, 'destroy']);

    Route::post('/appointments', [AppointmentController::class, 'store']);
    Route::get('/appointments/my', [AppointmentController::class, 'myAppointments']);

    Route::get('/admin/appointments', [AppointmentController::class, 'adminIndex']);
    Route::put('/admin/appointments/{id}', [AppointmentController::class, 'updateStatus']);
});