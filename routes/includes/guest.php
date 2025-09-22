<?php

use App\Http\Controllers\Guest\GuestExtraController;
use App\Http\Controllers\Guest\GuestIndexController;
use App\Http\Controllers\Guest\GuestNewsController;

Route::get('/', [GuestIndexController::class, 'indexPage'])->name('index');

Route::get('/news', [GuestNewsController::class, 'index'])->name('news.index');
Route::get('/news/{slug}', [GuestNewsController::class, 'show'])->name('news.show');

Route::get('/api', [GuestIndexController::class, 'apiPage'])->name('api');

Route::get('/services', [GuestIndexController::class, 'servicesPage'])->name('services');

Route::get('/rules', [GuestIndexController::class, 'rulesPage'])->name('rules');

Route::get('/policy', [GuestIndexController::class, 'policyPage'])->name('policy');


Route::get('/guest/refresh-data', [GuestExtraController::class, 'refreshData'])->name('guest.refresh_data');
