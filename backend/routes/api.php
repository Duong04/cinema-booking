<?php

use App\Http\Controllers\Apis\V1\ActionController;
use App\Http\Controllers\Apis\V1\AuthController;
use App\Http\Controllers\Apis\V1\PermissionController;
use App\Http\Controllers\Apis\V1\RoleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {

    Route::prefix('auth')->controller(AuthController::class)->group(function () {
        Route::post('/register', 'register');
        Route::post('/login', 'login');
        Route::get('/verify-email/{token}', 'verifyEmail');
        Route::post('/logout', 'logout')->middleware('auth:sanctum');
        Route::get('/profile', 'profile')->middleware('auth:sanctum');
    });

    Route::prefix('roles')->controller(RoleController::class)->middleware('auth:sanctum')->group(function () {
        Route::get('/', 'paginate');
        Route::post('/', 'create');
        Route::get('/{id}', 'show');
        Route::put('/{id}', 'update');
        Route::post('/{id}', 'delete');
    });

    Route::prefix('actions')->controller(ActionController::class)->middleware('auth:sanctum')->group(function () {
        Route::get('/', 'paginate');
        Route::post('/', 'create');
        Route::get('/{id}', 'show');
        Route::put('/{id}', 'update');
        Route::post('/{id}', 'delete');
    });

    Route::prefix('permissions')->controller(PermissionController::class)->middleware('auth:sanctum')->group(function () {
        Route::get('/', 'paginate');
        Route::post('/', 'create');
        Route::get('/{id}', 'show');
        Route::put('/{id}', 'update');
        Route::post('/{id}', 'delete');
    });

});
