<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductImageController;
use App\Http\Controllers\RateController;
use App\Http\Controllers\ShopController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => ['api']], function ($router) {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('refresh', [AuthController::class, 'refresh']);

    Route::group(['middleware' => ['jwt.auth']], function ($router) {
        Route::get('me', [AuthController::class, 'me']);
        Route::get('logout', [AuthController::class, 'logout']);

        Route::group(['middleware' => 'userType:admin', 'prefix' => 'admin'], function ($type) {
            Route::apiResource('users', UserController::class);
            Route::apiResource('categories', CategoryController::class);
            Route::apiResource('shops', ShopController::class);
            Route::apiResource('products', ProductController::class);
            Route::apiResource('images', ProductImageController::class);
            Route::apiResource('rates', RateController::class);
        });

        Route::group(['middleware' => 'userType:seller'], function ($type) {
            Route::put('users/{user}', [UserController::class, 'update']);
            Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);
            Route::apiResource('shops', ShopController::class);
            Route::get('products', [ProductController::class, 'index']);
            Route::apiResource('shops.products', ProductController::class);
            Route::post('products/{product}/images', [ProductImageController::class, 'store']);
            Route::post('products/{product}/reorder', [ProductImageController::class, 'reorder']);
        });


        Route::get('cart', [CartController::class, 'userCartItems']);
        Route::post('products/{product}', [CartController::class, 'addToCart']);
        Route::post('cart/{id}', [CartController::class, 'removeFromCart']);
        Route::post('cart/buyAll', [CartController::class, 'buyAll']);
        Route::get('orders', [OrderController::class, 'orders']);
        Route::post('products/{product}/rate', [RateController::class, 'avgRate']);
        Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);
        Route::apiResource('shops', ShopController::class)->only(['index', 'show']);
        Route::apiResource('products', ProductController::class)->only(['index', 'show']);
        Route::apiResource('rates', RateController::class)->only('store', 'update');

//        Route::get();
    });
});
