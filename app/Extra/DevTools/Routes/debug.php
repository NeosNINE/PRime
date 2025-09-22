<?php

use App\Extra\DevTools\Controllers\Debug\TestController;
use App\Http\Controllers\Debug\TestGitIgnoreController;

Route::get('/debug/test', [TestController::class, 'index'])->name('debug.test');
Route::get('/debug/test_git_ignore', [TestGitIgnoreController::class, 'index'])->name('debug.test_git_ignore');
