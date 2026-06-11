<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\ProductController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {
    Route::get('/products', [ProductController::class, 'indexWithoutCache']);
    Route::get('/products-cached', [ProductController::class, 'indexWithCache']);
    Route::post('/products/clear-cache', [ProductController::class, 'clearCache']);
});
