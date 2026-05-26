<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\FoodProductController;

Route::get('/food/search', [FoodProductController::class, 'search']);
Route::get('/food/product/{barcode}', [FoodProductController::class, 'show']);