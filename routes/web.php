<?php

use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\FriendshipController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ReactionController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'show'])->name('blog.home');

Route::middleware('auth')->group(function () {
    Route::get('profile', [PageController::class, 'profile'])->name('blog.profile');
    Route::get('profile/edit', [UserController::class, 'editProfile'])->name('blog.profile.edit');
    Route::put('profile', [UserController::class, 'updateProfile'])->name('blog.profile.update');
    Route::get('favourites', [PageController::class, 'favourites'])->name('blog.favourites');
    Route::get('create', [PageController::class, 'create'])->name('blog.create');
    Route::post('create', [BlogController::class, 'store'])->name('blog.store');
    Route::get('edit/{post}', [BlogController::class, 'edit'])->name('blog.edit');
    Route::put('edit/{post}', [BlogController::class, 'update'])->name('blog.update');
    Route::delete('post/{post}', [BlogController::class, 'delete'])->name('blog.delete');
    Route::post('logout', [UserController::class, 'logout'])->name('logout');
    Route::post('post/{post}/react/{type}', [ReactionController::class, 'toggle'])->name('post.react');
    Route::get('statistics', [StatsController::class, 'index'])->name('blog.stats');
    Route::get('statistics/export', [StatsController::class, 'exportCsv'])->name('blog.stats.export');

    Route::post('friends/{user}', [FriendshipController::class, 'store'])->name('friends.request');
    Route::put('friends/{friendship}/accept', [FriendshipController::class, 'accept'])->name('friends.accept');
    Route::delete('friends/{friendship}', [FriendshipController::class, 'destroy'])->name('friends.destroy');

    Route::post('follow/{user}', [FollowController::class, 'store'])->name('follow.store');
    Route::delete('follow/{user}', [FollowController::class, 'destroy'])->name('follow.destroy');

    Route::get('messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('messages/{user}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('messages/{user}', [MessageController::class, 'store'])->name('messages.store');
});

Route::get('post/{post}', [PageController::class, 'show_specific_post'])->name('blog.post');
Route::get('author/{user}', [PageController::class, 'authorProfile'])->name('blog.author');
Route::get('tag/{tag}', [PageController::class, 'showTag'])->name('blog.tag');
Route::get('about', [PageController::class, 'about'])->name('blog.about');
Route::get('contact', [PageController::class, 'contact'])->name('blog.contact');

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('users', [AdminUserController::class, 'index'])->name('users.index');
    Route::patch('users/{user}/role', [AdminUserController::class, 'updateRole'])->name('users.role');
    Route::delete('users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

    Route::get('posts', [AdminPostController::class, 'index'])->name('posts.index');
    Route::delete('posts/{post}', [AdminPostController::class, 'destroy'])->name('posts.destroy');

    Route::resource('categories', AdminCategoryController::class)->except(['show'])->names('categories');
});

Route::middleware('guest')->group(function () {
    Route::get('login', [PageController::class, 'showlogin'])->name('show.login');
    Route::post('login', [UserController::class, 'login'])->name('login')->middleware('throttle:5,1');
    Route::get('register', [UserController::class, 'showregister'])->name('show.register');
    Route::post('register', [UserController::class, 'register'])->name('register')->middleware('throttle:5,1');
});
