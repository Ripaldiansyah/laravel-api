<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserRequestHistoryController;
use App\Models\UserRequestHistory;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\ProductController;

Route::prefix('api')->group(function () {

    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::post('logout', [AuthController::class, 'logout']);
    });

    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'store']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
    });

    Route::prefix('companies')->group(function () {
        Route::get('/', [CompanyController::class, 'index']);
        Route::post('/', [CompanyController::class, 'store']);
        Route::get('/{id}', [CompanyController::class, 'show']);
        Route::put('/{id}', [CompanyController::class, 'update']);
        Route::delete('/{id}', [CompanyController::class, 'destroy']);
    });

    Route::prefix('user_request_histories')->group(function () {
        Route::get('/', [UserRequestHistoryController::class, 'index']);
        Route::post('/', [UserRequestHistoryController::class, 'store']);
        Route::get('/{id}', [UserRequestHistoryController::class, 'show']);
        Route::put('/{id}', [UserRequestHistoryController::class, 'update']);
        Route::delete('/{id}', [UserRequestHistoryController::class, 'destroy']);
    });
});



// Route::get('/test',action: [TestController::class])->name('test.about');
