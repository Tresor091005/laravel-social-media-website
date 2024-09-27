<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', function () {
    return to_route('home');
})->name('dashboard');

// HOME PAGE
Route::get('/', [HomeController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('home');

// USER PROFILE PAGE
Route::get('/u/{user:username}', [ProfileController::class, 'index'])
    ->name('profile');

Route::middleware('auth')->group(function () {
    // REFERS TO POST
    Route::post('/post', [PostController::class, 'store'])->name('post.create');
    Route::put('/post/{post}', [PostController::class, 'update'])->name('post.update');
    Route::delete('/post/{post}', [PostController::class, 'destroy'])->name('post.destroy');
    Route::get('/post/download/{attachment}', [PostController::class, 'downloadAttachment'])->name('post.download');

    // POST REACTIONS
    Route::POST('/post/{post}/reaction', [PostController::class, 'postReaction'])->name('post.reaction');

    // POST COMMENTS
    Route::POST('/post/{post}/comment', [PostController::class, 'createComment'])->name('post.comment.create');

    // REFERS TO USER PROFILE INFORMATIONS
    Route::post('/profile/update-images', [ProfileController::class, 'updateImage'])->name('profile.updateImages');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
