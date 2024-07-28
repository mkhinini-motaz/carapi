<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CarController;

Route::post('login', [AuthController::class, 'login']);
Route::group([ 'middleware' => 'auth',], function ($router) {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);
    
    Route::get('car', [CarController::class, 'index']);
    Route::get('car/{carId}', [CarController::class, 'show']);
});