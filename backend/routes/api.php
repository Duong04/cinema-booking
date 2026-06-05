<?php

use App\Http\Controllers\Apis\V1\ActionController;
use App\Http\Controllers\Apis\V1\AuthController;
use App\Http\Controllers\Apis\V1\CinemaChainController;
use App\Http\Controllers\Apis\V1\CinemaController;
use App\Http\Controllers\Apis\V1\CityController;
use App\Http\Controllers\Apis\V1\GenreController;
use App\Http\Controllers\Apis\V1\MovieController;
use App\Http\Controllers\Apis\V1\PermissionController;
use App\Http\Controllers\Apis\V1\PromotionController;
use App\Http\Controllers\Apis\V1\RoleController;
use App\Http\Controllers\Apis\V1\RoomController;
use App\Http\Controllers\Apis\V1\SeatController;
use App\Http\Controllers\Apis\V1\SeatHoldController;
use App\Http\Controllers\Apis\V1\SeatTypeController;
use App\Http\Controllers\Apis\V1\ShowtimeController;
use App\Http\Controllers\Apis\V1\UploadController;
use App\Http\Controllers\Apis\V1\ComboController;
use App\Http\Controllers\Apis\V1\BookingController;
use App\Http\Controllers\Apis\V1\PaymentController;
use App\Http\Controllers\Apis\V1\StatisticController;
use App\Http\Controllers\Apis\V1\UserController;
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
        Route::put('/profile', 'updateProfile')->middleware('auth:sanctum');
        Route::put('/password', 'changePassword')->middleware('auth:sanctum');
    });

    Route::prefix('users')->controller(UserController::class)->middleware('auth:sanctum', 'check.active')->group(function () {
        Route::get('/', 'paginate')->middleware('permission.action:users,view');
        Route::post('/', 'create')->middleware('permission.action:users,create');
        Route::get('/{id}', 'show')->middleware('permission.action:users,view');
        Route::put('/{id}', 'update')->middleware('permission.action:users,update');
    });

    Route::prefix('statistics')->controller(StatisticController::class)->middleware('auth:sanctum', 'check.active')->group(function () {
        Route::get('/dashboard', 'dashboard')->middleware('permission.action:dashboard,view');
    });

    Route::prefix('roles')->controller(RoleController::class)->middleware('auth:sanctum', 'check.active')->group(function () {
        Route::get('/', 'paginate')->middleware('permission.action:roles,view');
        Route::post('/', 'create')->middleware('permission.action:roles,create');
        Route::get('/{id}', 'show')->middleware('permission.action:roles,view');
        Route::put('/{id}', 'update')->middleware('permission.action:roles,update');
        Route::delete('/{id}', 'delete')->middleware('permission.action:roles,delete');
    });

    Route::prefix('actions')->controller(ActionController::class)->middleware('auth:sanctum', 'check.active')->group(function () {
        Route::get('/', 'paginate')->middleware('permission.action:actions,view');
        Route::post('/', 'create')->middleware('permission.action:actions,create');
        Route::get('/{id}', 'show')->middleware('permission.action:actions,view');
        Route::put('/{id}', 'update')->middleware('permission.action:actions,update');
        Route::delete('/{id}', 'delete')->middleware('permission.action:actions,delete');
    });

    Route::prefix('permissions')->controller(PermissionController::class)->middleware('auth:sanctum', 'check.active')->group(function () {
        Route::get('/', 'paginate')->middleware('permission.action:permissions,view');
        Route::post('/', 'create')->middleware('permission.action:permissions,create');
        Route::get('/{id}', 'show')->middleware('permission.action:permissions,view');
        Route::put('/{id}', 'update')->middleware('permission.action:permissions,update');
        Route::delete('/{id}', 'delete')->middleware('permission.action:permissions,delete');
    });

    Route::prefix('upload')->controller(UploadController::class)->middleware('auth:sanctum', 'check.active')->group(function () {
        Route::post('/image', 'uploadImage');
        Route::post('/file', 'uploadFile');
        Route::post('/multiple', 'uploadMultiple');
        Route::delete('/', 'delete');
        Route::delete('/multiple', 'deleteMultiple');
    });

    Route::prefix('cities')->controller(CityController::class)->middleware('auth:sanctum', 'check.active')->group(function () {
        Route::get('/', 'paginate')->middleware('permission.action:cities,view');
        Route::post('/', 'create')->middleware('permission.action:cities,create');
        Route::get('/{id}', 'show')->middleware('permission.action:cities,view');
        Route::put('/{id}', 'update')->middleware('permission.action:cities,update');
        Route::delete('/{id}', 'delete')->middleware('permission.action:cities,delete');
    });

    Route::prefix('cinemas')->controller(CinemaController::class)->middleware('auth:sanctum', 'check.active')->group(function () {
        Route::get('/', 'paginate')->middleware('permission.action:cinemas,view');
        Route::post('/', 'create')->middleware('permission.action:cinemas,create');
        Route::get('/{id}', 'show')->middleware('permission.action:cinemas,view');
        Route::put('/{id}', 'update')->middleware('permission.action:cinemas,update');
        Route::delete('/{id}', 'delete')->middleware('permission.action:cinemas,delete');
    });

    Route::prefix('cinema-chains')->controller(CinemaChainController::class)->middleware('auth:sanctum', 'check.active')->group(function () {
        Route::get('/', 'paginate')->middleware('permission.action:cinema-chains,view');
        Route::post('/', 'create')->middleware('permission.action:cinema-chains,create');
        Route::get('/{id}', 'show')->middleware('permission.action:cinema-chains,view');
        Route::put('/{id}', 'update')->middleware('permission.action:cinema-chains,update');
        Route::delete('/{id}', 'delete')->middleware('permission.action:cinema-chains,delete');
    });

    Route::prefix('rooms')->controller(RoomController::class)->middleware('auth:sanctum', 'check.active')->group(function () {
        Route::get('/', 'paginate')->middleware('permission.action:rooms,view');
        Route::post('/', 'create')->middleware('permission.action:rooms,create');
        Route::get('/{id}', 'show')->middleware('permission.action:rooms,view');
        Route::put('/{id}', 'update')->middleware('permission.action:rooms,update');
        Route::delete('/{id}', 'delete')->middleware('permission.action:rooms,delete');
    });

    Route::prefix('seat-types')->controller(SeatTypeController::class)->middleware('auth:sanctum', 'check.active')->group(function () {
        Route::get('/', 'paginate')->middleware('permission.action:seat-types,view');
        Route::post('/', 'create')->middleware('permission.action:seat-types,create');
        Route::get('/{id}', 'show')->middleware('permission.action:seat-types,view');
        Route::put('/{id}', 'update')->middleware('permission.action:seat-types,update');
        Route::delete('/{id}', 'delete')->middleware('permission.action:seat-types,delete');
    });

    Route::prefix('rooms')->controller(SeatController::class)->middleware('auth:sanctum', 'check.active')->group(function () {
        Route::get('/{id}/seats', 'getSeatByRoom')->middleware('permission.action:rooms,view');
        Route::post('/{id}/seats', 'create')->middleware('permission.action:rooms,create');
        Route::put('/{id}/seats/{rowLabel}', 'updateRow')->middleware('permission.action:rooms,update');
        Route::delete('/{id}/seats/{rowLabel}', 'deleteRow')->middleware('permission.action:rooms,delete');
    });

    Route::prefix('movies')->controller(MovieController::class)->middleware('auth:sanctum', 'check.active')->group(function () {
        Route::get('/', 'paginate')->middleware('permission.action:movies,view');
        Route::post('/', 'create')->middleware('permission.action:movies,create');
        Route::get('/{id}', 'show')->middleware('permission.action:movies,view');
        Route::put('/{id}', 'update')->middleware('permission.action:movies,update');
        Route::delete('/{id}', 'delete')->middleware('permission.action:movies,delete');
    });

    Route::prefix('genres')->controller(GenreController::class)->middleware('auth:sanctum', 'check.active')->group(function () {
        Route::get('/', 'paginate')->middleware('permission.action:genres,view');
        Route::post('/', 'create')->middleware('permission.action:genres,create');
        Route::get('/{id}', 'show')->middleware('permission.action:genres,view');
        Route::put('/{id}', 'update')->middleware('permission.action:genres,update');
        Route::delete('/{id}', 'delete')->middleware('permission.action:genres,delete');
    });

    Route::prefix('showtimes')->controller(ShowtimeController::class)->middleware('auth:sanctum', 'check.active')->group(function () {
        Route::get('/', 'paginate')->middleware('permission.action:showtimes,view');
        Route::post('/', 'create')->middleware('permission.action:showtimes,create');
        Route::get('/{id}/seat-overview', 'seatOverview')->middleware('permission.action:showtimes,view');
        Route::get('/{id}', 'show')->middleware('permission.action:showtimes,view');
        Route::put('/{id}', 'update')->middleware('permission.action:showtimes,update');
        Route::delete('/{id}', 'delete')->middleware('permission.action:showtimes,delete');
    });

    Route::prefix('seat-holds')->controller(SeatHoldController::class)->middleware('auth:sanctum', 'check.active')->group(function () {
        Route::get('/showtimes/{showtimeId}', 'getListShowtime')->middleware('permission.action:seat-holds,view');
        Route::post('/hold', 'hold');
        Route::post('/release', 'release');
    });

    Route::prefix('bookings')->controller(BookingController::class)->middleware('auth:sanctum', 'check.active')->group(function () {
        Route::get('/', 'paginate')->middleware('permission.action:bookings,view');
        Route::post('/', 'create')->middleware('permission.action:bookings,create');
        Route::get('/{id}', 'show')->middleware('permission.action:bookings,view');
        Route::put('/{id}', 'update')->middleware('permission.action:bookings,update');
        Route::put('/{id}/cancel', 'cancel')->middleware('permission.action:bookings,update');
    });

    Route::prefix('promotions')->controller(PromotionController::class)->middleware('auth:sanctum', 'check.active')->group(function () {
        Route::get('/', 'paginate')->middleware('permission.action:promotions,view');
        Route::post('/', 'create')->middleware('permission.action:promotions,create');
        Route::post('/check', 'check')->middleware('permission.action:promotions,view');
        Route::get('/{id}', 'show')->middleware('permission.action:promotions,view');
        Route::put('/{id}', 'update')->middleware('permission.action:promotions,update');
        Route::delete('/{id}', 'delete')->middleware('permission.action:promotions,delete');
    });

    Route::prefix('payments')->controller(PaymentController::class)->middleware('auth:sanctum', 'check.active')->group(function () {
        Route::get('/', 'paginate')->middleware('permission.action:payments,view');
        Route::post('/', 'create');
        Route::get('/{id}', 'show');
        Route::post('/{id}/confirm', 'confirm');
    });

    Route::prefix('combos')->controller(ComboController::class)->middleware('auth:sanctum', 'check.active')->group(function () {
        Route::get('/', 'paginate')->middleware('permission.action:combos,view');
        Route::post('/', 'create')->middleware('permission.action:combos,create');
        Route::get('/{id}', 'show')->middleware('permission.action:combos,view');
        Route::put('/{id}', 'update')->middleware('permission.action:combos,update');
        Route::delete('/{id}', 'delete')->middleware('permission.action:combos,delete');
    });

    Route::prefix('/public')->group(function () {
        Route::get('/cities', [CityController::class, 'getAll']);
        Route::get('/cinemas', [CinemaController::class, 'getAll']);
        Route::get('/cinema-chains', [CinemaChainController::class, 'getAll']);
        Route::get('/movies', [MovieController::class, 'getAll']);
        Route::get('/movies/{slug}', [MovieController::class, 'showPublic']);
        Route::get('/genres', [GenreController::class, 'getAll']);
        Route::get('/combos', [ComboController::class, 'active']);
        Route::get('/showtimes', [ShowtimeController::class, 'getAll']);
        Route::get('/showtimes/{id}/seat-overview', [ShowtimeController::class, 'publicSeatOverview']);
        Route::get('/showtimes/{id}', [ShowtimeController::class, 'showPublic']);
    });
});
