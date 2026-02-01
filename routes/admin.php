<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\HotInfoController;
use App\Http\Controllers\CommentController as PublicCommentController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\OtherController;
use App\Http\Controllers\Admin\PpdbController;
use App\Http\Controllers\Admin\CommentAdminController;
use App\Http\Controllers\Admin\PpdbVerificationController;
use App\Http\Controllers\Admin\QuillImageController;

Route::middleware(['auth', 'role:admin', 'verified'])
    ->group(function () {
        Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

        Route::get('/ppdb', [PpdbController::class, 'index'])->name('ppdb.index');
        Route::get('/ppdb/{ppdb}', [PpdbController::class, 'show'])->name('ppdb.show');
        Route::get('/ppdb/{ppdb}/payment-proof', [PpdbController::class, 'paymentProof'])->name('ppdb.paymentProof');

        Route::patch('/ppdb/{ppdb}/approve', [PpdbVerificationController::class, 'approve'])->name('ppdb.approve');
        Route::patch('/ppdb/{ppdb}/reject', [PpdbVerificationController::class, 'reject'])->name('ppdb.reject');
    
        Route::get('/users',  [UserController::class,  'index'])->name('users.index');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

        Route::get('/others', [OtherController::class, 'index'])->name('others.index');
        Route::delete('/others/{user}', [OtherController::class, 'destroy'])->name('others.destroy');
        
        Route::resource('gallery', GalleryController::class);
        
        Route::resource('articles', ArticleController::class);
        Route::post('quill/image', [QuillImageController::class, 'store'])->name('quill.image');

        Route::get('/comments', [CommentAdminController::class, 'index'])->name('comments.index');
        Route::patch('/comments/{comment}', [CommentAdminController::class, 'update'])->name('comments.update');
        Route::patch('/comments/bulk', [CommentAdminController::class, 'bulk'])->name('comments.bulk');
        Route::delete('/comments/{comment}', [CommentAdminController::class, 'destroy'])->name('comments.destroy');

        Route::patch('comments/{comment}/moderate', [PublicCommentController::class, 'moderate'])
            ->name('comments.moderate');

        // Kategori (full CRUD)
        Route::resource('categories', CategoryController::class);

        // Tag (ringkas: index + create/update/delete inline)
        Route::get('tags', [TagController::class, 'index'])->name('tags.index');
        Route::post('tags', [TagController::class, 'store'])->name('tags.store');
        Route::patch('tags/{tag}', [TagController::class, 'update'])->name('tags.update');
        Route::delete('tags/{tag}', [TagController::class, 'destroy'])->name('tags.destroy');

        // Hot Info / Ticker (index + store + update + destroy)
        Route::get('hot-infos', [HotInfoController::class, 'index'])->name('hot_infos.index');
        Route::post('hot-infos', [HotInfoController::class, 'store'])->name('hot_infos.store');
        Route::patch('hot-infos/{hotInfo}', [HotInfoController::class, 'update'])->name('hot_infos.update');
        Route::delete('hot-infos/{hotInfo}', [HotInfoController::class, 'destroy'])->name('hot_infos.destroy');

        // Moderasi komentar (admin)
        Route::patch('comments/{comment}/moderate', [PublicCommentController::class, 'moderate'])
            ->name('comments.moderate');
        
        Route::patch('/ppdb/{ppdb}/approve', [PpdbVerificationController::class, 'approve'])->name('ppdb.approve');
        Route::patch('/ppdb/{ppdb}/reject', [PpdbVerificationController::class, 'reject'])->name('ppdb.reject');

    });
