<?php

use App\Http\Controllers\Extra\Select2AjaxController;
use App\Http\Controllers\Extra\UploadFileController;
use App\Http\Controllers\Extra\CurrencyController;

Route::post('/upload-file', [UploadFileController::class, 'upload'])->name('upload_file');
Route::post('/wysiwyg_upload', [UploadFileController::class, 'wysiwygUpload'])->name('wysiwyg_upload');
Route::get('/select2/search', [Select2AjaxController::class, 'getSearchResults'])->name('select2.search');

// Currency
Route::get('/currency/list', [CurrencyController::class, 'list'])->name('currency.list');
Route::post('/currency/set', [CurrencyController::class, 'set'])->name('currency.set');
