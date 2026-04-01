<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PageController;
use App\Http\Controllers\UserController;
use App\Models\User;

Route::get('/', [PageController::class, 'show'])->name('blog.home');

Route::middleware('auth')->group(function () {
    Route::get('profile', [PageController::class, 'profile'])->name('blog.profile');
    Route::get('create', [PageController::class, 'create'])->name('blog.create');
    Route::post('create', [BlogController::class, 'store'])->name('blog.store');
    Route::get('edit/{post}', [BlogController::class, 'edit'])->name('blog.edit');
    Route::put('edit/{post}', [BlogController::class, 'update'])->name('blog.update');
    Route::delete('post/{post}', [BlogController::class, 'delete'])->name('blog.delete');
    Route::post('logout', [UserController::class, 'logout'])->name('logout');
});


Route::get('post/{post}', [PageController::class, 'show_specific_post'])->name('blog.post');
Route::get('about', [PageController::class, 'about'])->name('blog.about');
Route::get('contact', [PageController::class, 'contact'])->name('blog.contact');


Route::middleware('guest')->group(function () {
    Route::get('login', [PageController::class, 'showlogin'])->name('show.login');
    Route::post('login', [UserController::class, 'login'])->name('login');
    Route::get('register', [UserController::class, 'showregister'])->name('show.register');
    Route::post('register', [UserController::class, 'register'])->name('register');
});
