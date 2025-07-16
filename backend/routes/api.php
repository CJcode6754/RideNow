<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DriverController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('login/verify', [AuthController::class, 'verify']);

Route::middleware('auth.sanctum')->group(function(){
    Route::get('/driver', [DriverController::class, 'show']);
    Route::post('/driver', [DriverController::class, 'update']);
});