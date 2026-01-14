<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\API\ChatApiController;
use App\Http\Controllers\API\ZePocket\SyncController;
use App\Http\Controllers\API\ZePocket\ProductController;
use App\Http\Controllers\API\ZePocket\SupplierController;
use App\Http\Controllers\API\ZePocket\QuoteController;
use App\Http\Controllers\API\ZePocket\OrderController;
use App\Models\Exam;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

/*
|--------------------------------------------------------------------------
| ZePocket API Routes
|--------------------------------------------------------------------------
*/
Route::prefix('zepocket')->middleware('auth:sanctum')->group(function () {
    // Sync
    Route::get('/sync/check', [SyncController::class, 'check']);
    Route::get('/sync/pull', [SyncController::class, 'pull']);
    Route::post('/sync/push', [SyncController::class, 'push']);

    // Products
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/search', [ProductController::class, 'search']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{product}', [ProductController::class, 'update']);
    Route::get('/products/{product}', [ProductController::class, 'show']);

    // Suppliers
    Route::get('/suppliers', [SupplierController::class, 'index']);
    Route::post('/suppliers', [SupplierController::class, 'store']);
    Route::put('/suppliers/{supplier}', [SupplierController::class, 'update']);
    Route::delete('/suppliers/{supplier}', [SupplierController::class, 'destroy']);
    Route::get('/suppliers/{supplier}', [SupplierController::class, 'show']);

    // Quotes (Product Supplier Prices)
    Route::get('/quotes', [QuoteController::class, 'index']);
    Route::post('/quotes', [QuoteController::class, 'store']);
    Route::delete('/quotes/{quote}', [QuoteController::class, 'destroy']);

    // Orders
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);
    Route::put('/orders/{order}', [OrderController::class, 'update']);
    Route::post('/orders/{order}/clone', [OrderController::class, 'clone']);
    Route::get('/orders/{order}/whatsapp', [OrderController::class, 'whatsapp']);
});
