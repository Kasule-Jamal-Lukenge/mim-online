<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AnalyticsController;


// Route::any('/ping', function () {
//     return response()->json(['pong' => true, 'php' => PHP_VERSION]);
// });

// Route::options('/{any}', function () {
//     return response()->json(['message' => 'CORS preflight handled']);
// })->where('any', '.*');


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

//Buyers Can As well View These
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    // Buyer orders
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);

     // Admin-only CRUD
    Route::middleware('admin')->group(function () {
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::put('/categories/{id}', [CategoryController::class, 'update']);
        Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

        Route::post('/products', [ProductController::class, 'store']);
        Route::put('/products/{id}', [ProductController::class, 'update']);
        Route::delete('/products/{id}', [ProductController::class, 'destroy']);
    });
});

// Admin Order Management
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/admin/orders', [OrderController::class, 'allOrders']);
    Route::get('/admin/orders/{id}', [OrderController::class, 'viewOrder']);
    Route::put('/admin/orders/{id}/status', [OrderController::class, 'updateStatus']);

    Route::get('/admin/analytics/orders/month', [AnalyticsController::class, 'ordersPerMonth']);
    Route::get('/admin/analytics/orders/year', [AnalyticsController::class, 'ordersPerYear']);
    Route::get('/admin/analytics/orders/status', [AnalyticsController::class, 'ordersByStatus']);
    Route::get('/admin/analytics/users/month', [AnalyticsController::class, 'usersPerMonth']);
    Route::get('/admin/analytics/sales/total', [AnalyticsController::class, 'totalSales']);
    Route::get('/admin/analytics/summary', [AnalyticsController::class, 'summary']);
});


Route::options('{any}', function () {
    return response()->json(['status' => 'ok']);
})->where('any', '.*');
