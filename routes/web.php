<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\ProductController;

Route::prefix('products')->group(function (){
    Route::get('/', [ProductController::class,'index'])->name('products.index');
    Route::get('/create' ,[ProductController::class,'create'])->name('products.create');
    Route::post('/create',[ProductController::class,'store']);
    Route::get('/{id} ',[ProductController::class])->name('products.show');
    Route::get('/{id}/edit ',[ProductController::class])->name('products.edit');
    Route::put('/{id} ',[ProductController::class])->name('products.update');
    Route::delete('/{id}',[ProductController::class])->name('product.destroy');
});

Route::prefix('api')->group(function (){

    Route::prefix('auth')->group(function (){
        Route::post('login', [AuthController::class, 'login'])->withoutMiddleware(VerifyCsrfToken::class);
        Route::post('logout', [AuthController::class, 'logout'])->withoutMiddleware(VerifyCsrfToken::class);
    });

    Route::prefix('users')->group(function (){
            Route::get('/',[UserController::class, 'index']);
            Route::post('/',[UserController::class, 'store']);
            Route::get('/{id}',[UserController::class, 'show']);
            Route::put('/{id}',[UserController::class, 'update']);
            Route::delete('/{id}',[UserController::class, 'destroy']);
    });


    Route::get('/users', [AuthController::class, 'index']);

});



// Route::get('/test',action: [TestController::class])->name('test.about');
