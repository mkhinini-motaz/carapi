<?php

use App\Http\Controllers\AuthController;

Route::post('login', [AuthController::class, 'login']);
Route::group([ 'middleware' => 'auth',], function ($router) {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);
});