<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', function () {
    return to_route('home');
})->name('dashboard');

Route::get('/',[HomeController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('home');

Route::get('/u/{user:username}',[ProfileController::class, 'index'])
    ->name('profile');

Route::middleware('auth')->group(function () {
    Route::post('/post', [PostController::class, 'store'])->name('post.create');
    Route::put('/post/{post}', [PostController::class, 'update'])->name('post.update');
    Route::delete('/post/{post}', [PostController::class, 'destroy'])->name('post.destroy');

    Route::post('/profile/update-images', [ProfileController::class, 'updateImage'])->name('profile.updateImages');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
