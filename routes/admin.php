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
use App\Http\Controllers\Admin\AboutSectionController;
use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\SiteStatController;
use App\Http\Controllers\Admin\ProgramController;
use App\Http\Controllers\Admin\ExtracurricularController;

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
        Route::patch('/comments/bulk', [CommentAdminController::class, 'bulk'])->name('comments.bulk');
        Route::patch('/comments/{comment}', [CommentAdminController::class, 'update'])->name('comments.update');
        Route::delete('/comments/{comment}', [CommentAdminController::class, 'destroy'])->name('comments.destroy');
        Route::patch('comments/{comment}/moderate', [PublicCommentController::class, 'moderate'])
            ->name('comments.moderate');
        Route::patch('/comments/{comment}', [CommentAdminController::class, 'update'])
            ->whereNumber('comment')
            ->name('comments.update');

        Route::delete('/comments/{comment}', [CommentAdminController::class, 'destroy'])
            ->whereNumber('comment')
            ->name('comments.destroy');

        Route::resource('categories', CategoryController::class);

        Route::get('tags', [TagController::class, 'index'])->name('tags.index');
        Route::post('tags', [TagController::class, 'store'])->name('tags.store');
        Route::patch('tags/{tag}', [TagController::class, 'update'])->name('tags.update');
        Route::delete('tags/{tag}', [TagController::class, 'destroy'])->name('tags.destroy');

        Route::get('hot-infos', [HotInfoController::class, 'index'])->name('hot_infos.index');
        Route::post('hot-infos', [HotInfoController::class, 'store'])->name('hot_infos.store');
        Route::patch('hot-infos/{hotInfo}', [HotInfoController::class, 'update'])->name('hot_infos.update');
        Route::delete('hot-infos/{hotInfo}', [HotInfoController::class, 'destroy'])->name('hot_infos.destroy');

        Route::patch('comments/{comment}/moderate', [PublicCommentController::class, 'moderate'])
            ->name('comments.moderate');
        
        Route::patch('/ppdb/{ppdb}/approve', [PpdbVerificationController::class, 'approve'])->name('ppdb.approve');
        Route::patch('/ppdb/{ppdb}/reject', [PpdbVerificationController::class, 'reject'])->name('ppdb.reject');

        Route::get('about', [AboutSectionController::class, 'edit'])->name('about.edit');
        Route::put('about/{aboutSection}', [AboutSectionController::class, 'update'])->name('about.update');

        Route::resource('announcements', AnnouncementController::class)->except(['show']);
        Route::patch('announcements/{announcement}/restore', [AnnouncementController::class, 'restore'])
            ->name('announcements.restore');
        Route::delete('announcements/{announcement}/force', [AnnouncementController::class, 'forceDestroy'])
            ->name('announcements.forceDestroy');

        Route::resource('site-stats', SiteStatController::class)->only(['index','edit','update']);

        Route::resource('programs', ProgramController::class)->except(['show']);
        Route::patch('programs/{program}/restore', [ProgramController::class, 'restore'])
            ->name('programs.restore');
        Route::delete('programs/{program}/force', [ProgramController::class, 'forceDestroy'])
            ->name('programs.forceDestroy');

        Route::resource('extracurriculars', ExtracurricularController::class)->except(['show']);
        Route::patch('extracurriculars/{extracurricular}/restore', [ExtracurricularController::class, 'restore'])
            ->name('extracurriculars.restore');
        Route::delete('extracurriculars/{extracurricular}/force', [ExtracurricularController::class, 'forceDestroy'])
            ->name('extracurriculars.forceDestroy');
    });