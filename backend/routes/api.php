<?php

use App\Http\Controllers\Apis\V1\ActionController;
use App\Http\Controllers\Apis\V1\AuthController;
use App\Http\Controllers\Apis\V1\CinemaChainController;
use App\Http\Controllers\Apis\V1\CinemaController;
use App\Http\Controllers\Apis\V1\CityController;
use App\Http\Controllers\Apis\V1\GenreController;
use App\Http\Controllers\Apis\V1\MovieController;
use App\Http\Controllers\Apis\V1\PermissionController;
use App\Http\Controllers\Apis\V1\RoleController;
use App\Http\Controllers\Apis\V1\RoomController;
use App\Http\Controllers\Apis\V1\SeatController;
use App\Http\Controllers\Apis\V1\SeatTypeController;
use App\Http\Controllers\Apis\V1\ShowtimeController;
use App\Http\Controllers\Apis\V1\UploadController;
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
        Route::delete('/{id}', 'delete');
    });

    Route::prefix('actions')->controller(ActionController::class)->middleware('auth:sanctum')->group(function () {
        Route::get('/', 'paginate');
        Route::post('/', 'create');
        Route::get('/{id}', 'show');
        Route::put('/{id}', 'update');
        Route::delete('/{id}', 'delete');
    });

    Route::prefix('permissions')->controller(PermissionController::class)->middleware('auth:sanctum')->group(function () {
        Route::get('/', 'paginate');
        Route::post('/', 'create');
        Route::get('/{id}', 'show');
        Route::put('/{id}', 'update');
        Route::delete('/{id}', 'delete');
    });

    Route::prefix('upload')->controller(UploadController::class)->middleware('auth:sanctum')->group(function () {
        Route::post('/image', 'uploadImage');
        Route::post('/file', 'uploadFile');
        Route::post('/multiple', 'uploadMultiple');
        Route::delete('/', 'delete');
        Route::delete('/multiple', 'deleteMultiple');
    });

    Route::prefix('cities')->controller(CityController::class)->middleware('auth:sanctum')->group(function () {
        Route::get('/', 'paginate');
        Route::post('/', 'create');
        Route::get('/{id}', 'show');
        Route::put('/{id}', 'update');
        Route::delete('/{id}', 'delete');
    });

    Route::prefix('cinemas')->controller(CinemaController::class)->middleware('auth:sanctum')->group(function () {
        Route::get('/', 'paginate');
        Route::post('/', 'create');
        Route::get('/{id}', 'show');
        Route::put('/{id}', 'update');
        Route::delete('/{id}', 'delete');
    });

    Route::prefix('cinema-chains')->controller(CinemaChainController::class)->middleware('auth:sanctum')->group(function () {
        Route::get('/', 'paginate');
        Route::post('/', 'create');
        Route::get('/{id}', 'show');
        Route::put('/{id}', 'update');
        Route::delete('/{id}', 'delete');
    });

    Route::prefix('rooms')->controller(RoomController::class)->middleware('auth:sanctum')->group(function () {
        Route::get('/', 'paginate');
        Route::post('/', 'create');
        Route::get('/{id}', 'show');
        Route::put('/{id}', 'update');
        Route::delete('/{id}', 'delete');
    });

    Route::prefix('seat-types')->controller(SeatTypeController::class)->middleware('auth:sanctum')->group(function () {
        Route::get('/', 'paginate');
        Route::post('/', 'create');
        Route::get('/{id}', 'show');
        Route::put('/{id}', 'update');
        Route::delete('/{id}', 'delete');
    });

    Route::prefix('rooms')->controller(SeatController::class)->middleware('auth:sanctum')->group(function () {
        Route::get('/{id}/seats', 'getSeatByRoom');
        Route::post('/{id}/seats', 'create');
    });

    Route::prefix('movies')->controller(MovieController::class)->middleware('auth:sanctum')->group(function () {
        Route::get('/', 'paginate');
        Route::post('/', 'create');
        Route::get('/{id}', 'show');
        Route::put('/{id}', 'update');
        Route::delete('/{id}', 'delete');
    });

    Route::prefix('genres')->controller(GenreController::class)->middleware('auth:sanctum')->group(function () {
        Route::get('/', 'paginate');
        Route::post('/', 'create');
        Route::get('/{id}', 'show');
        Route::put('/{id}', 'update');
        Route::delete('/{id}', 'delete');
    });

    Route::prefix('showtimes')->controller(ShowtimeController::class)->middleware('auth:sanctum')->group(function () {
        Route::get('/', 'paginate');
        Route::post('/', 'create');
        Route::get('/{id}', 'show');
        Route::put('/{id}', 'update');
        Route::delete('/{id}', 'delete');
    });
});
