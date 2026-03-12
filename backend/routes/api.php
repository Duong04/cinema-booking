<?php

use App\Http\Controllers\Apis\V1\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {

    Route::prefix('auth')->controller(AuthController::class)->group(function () {
        Route::post('register', 'register');
        Route::post('login', 'login');
        Route::get('verify-email/{token}', 'verifyEmail');
        Route::post('logout', 'logout')->middleware('auth:sanctum');
        Route::get('profile', 'profile')->middleware('auth:sanctum');
    });

});
