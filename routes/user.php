<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\User\PpdbProfileController;

Route::middleware(['auth', 'role:user', 'verified'])
    ->group(function () {
        Route::get('/dashboard', [UserController::class, 'index'])->name('dashboard');

        Route::get('/ppdb/edit', [PpdbProfileController::class, 'edit'])->name('ppdb.edit');
        Route::post('/ppdb/edit', [PpdbProfileController::class, 'update'])->name('ppdb.update');
        Route::get('/ppdb/payment-proof', [PpdbProfileController::class, 'paymentProof'])->name('ppdb.paymentProof');
    });
