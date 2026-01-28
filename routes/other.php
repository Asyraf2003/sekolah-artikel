<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Other\OtherController;

Route::middleware(['auth', 'role:other', 'verified'])->group(function () {
    Route::get('/dashboard', [OtherController::class, 'index'])->name('dashboard');
});

