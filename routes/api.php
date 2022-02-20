<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderStatusController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\FileController;

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

// Public routes
Route::prefix('admin')->group(function () {
    Route::post('login', [AdminController::class, 'login']);
});

Route::prefix('user')->group(function () {
    Route::post('create', [UserController::class, 'create']);
    Route::post('login', [UserController::class, 'login']);
    Route::post('forgot-password', [UserController::class, 'forgotPassword']);
    Route::post('reset-password-token', [UserController::class, 'resetPasswordToken']);
});

Route::prefix('main')->group(function () {
    Route::get('blog/{uuid?}', [MainController::class, 'blog']);
    Route::get('promotions', [MainController::class, 'promotions']);
});

Route::get('brands', [BrandController::class, 'index']);
Route::prefix('brand')->group(function () {
    Route::get('{uuid}', [BrandController::class, 'index']);
});

Route::get('categories', [CategoryController::class, 'index']);
Route::prefix('category')->group(function () {
    Route::get('{uuid}', [CategoryController::class, 'index']);
});

Route::get('file/{file:uuid}', [FileController::class, 'download']);
Route::post('file/upload', [FileController::class, 'upload']);

// Admin protected routes
Route::middleware('auth.jwt:admin')->group(function () {
    Route::prefix('admin')->group(function () {
        Route::get('logout', [AdminController::class, 'logout']);
        Route::get('user-listing', [AdminController::class, 'listUsers']);
        Route::post('create', [AdminController::class, 'create']);
    });

    Route::prefix('brand')->group(function () {
        Route::post('create', [BrandController::class, 'create']);
        Route::put('{brand:uuid}', [BrandController::class, 'edit']);
        Route::delete('{brand:uuid}', [BrandController::class, 'delete']);
    });

    Route::prefix('category')->group(function () {
        Route::post('create', [CategoryController::class, 'create']);
        Route::put('{category:uuid}', [CategoryController::class, 'edit']);
        Route::delete('{category:uuid}', [CategoryController::class, 'delete']);
    });

    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index']);
        Route::get('dashboard', [OrderController::class, 'dashboard']);
        Route::get('shipment-locator', [OrderController::class, 'shipmentLocator']);
    });

    Route::get('order-statuses', [OrderStatusController::class, 'index']);
    Route::prefix('order-status')->group(function () {
        Route::get('{orderStatus:uuid}', [OrderStatusController::class, 'index']);
        Route::post('create', [OrderStatusController::class, 'create']);
        Route::put('{orderStatus:uuid}', [OrderStatusController::class, 'edit']);
        Route::delete('{orderStatus:uuid}', [OrderStatusController::class, 'delete']);
    });

    Route::get('payments', [PaymentController::class, 'index']);
    Route::prefix('payment')->group(function () {
        Route::get('{payment:uuid}', [PaymentController::class, 'index']);
        Route::post('create', [PaymentController::class, 'create']);
        Route::put('{payment:uuid}', [PaymentController::class, 'edit']);
        Route::delete('{payment:uuid}', [PaymentController::class, 'delete']);
    });

    Route::get('products', [ProductController::class, 'index']);
    Route::prefix('product')->group(function () {
        Route::get('{product:uuid}', [ProductController::class, 'index']);
        Route::post('create', [ProductController::class, 'create']);
        Route::put('{product:uuid}', [ProductController::class, 'edit']);
        Route::delete('{product:uuid}', [ProductController::class, 'delete']);
    });
});

// User protected routes
Route::middleware('auth.jwt')->group(function () {
    Route::prefix('user')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::delete('/', [UserController::class, 'delete']);
        Route::get('orders', [UserController::class, 'listOrders']);
        Route::get('logout', [UserController::class, 'logout']);
        Route::put('edit', [UserController::class, 'edit']);
    });

    Route::prefix('order')->group(function () {
        Route::get('{uuid}', [OrderController::class, 'index']);
        Route::get('{uuid}/download', [OrderController::class, 'download']);
        Route::post('create', [OrderController::class, 'create']);
        Route::put('{uuid}', [OrderController::class, 'edit']);
        Route::delete('{uuid}', [OrderController::class, 'delete']);
    });
});
