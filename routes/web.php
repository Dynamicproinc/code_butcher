<?php

use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::livewire('/dashboard/test', 'dashboard.test')->name('dashboard.test');
Route::livewire('/dashboard/add-stock', 'dashboard.add_stock')->name('dashboard.add-stock');
Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
