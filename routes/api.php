<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductApiController;

use App\Http\Controllers\Api\ApiNewsController;
use App\Http\Controllers\Api\ApiFilterController;

Route::get('/news/{id}', [ApiNewsController::class, 'show']);

Route::get('/filters', [ApiFilterController::class, 'getFilters'])->name('api.filters.get');
Route::post('/filters/apply', [ApiFilterController::class, 'applyFilters'])->name('api.filters.apply');

Route::get('/products', [ProductApiController::class, 'index']);
Route::get('/products/search', [ProductApiController::class, 'search']);
Route::get('/products/suggestions', [ProductApiController::class, 'suggestions']);
Route::get('/products/{id}', [ProductApiController::class, 'show']);
Route::post('/products/refresh-cache', [ProductApiController::class, 'refreshCache']);
