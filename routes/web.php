<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\PropertyRentalController;
use App\Http\Controllers\PropertyValuationController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::get('/dashboard', DashboardController::class)->name('dashboard');

Route::resource('properties', PropertyController::class);

Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('valuations', PropertyValuationController::class)
        ->except(['show'])
        ->parameters(['valuations' => 'valuation']);

    Route::resource('rentals', PropertyRentalController::class)
        ->except(['show'])
        ->parameters(['rentals' => 'rental']);
});
