<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WooCommerceController;

// Route::get('/user', function (Request $request) {
//     return '$request->user();';
// });


Route::post('/wc/webhook', [WooCommerceController::class, 'handle']);