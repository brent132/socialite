<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\PostsController;
use App\Http\Controllers\ProfilesController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\CommentsController;

// Redirect '/' to '/home'
Route::get('/', function () {
    return redirect('/home');
});


Auth::routes();

Route::get('/p/create', [PostsController::class, 'create']);
Route::post('/p', [PostsController::class, 'store']);
Route::get('/p/{post}', [PostsController::class, 'show']);
Route::delete('/p/{post}', [PostsController::class, 'destroy'])->name('posts.destroy');
Route::post('/p/{post}/like', [PostsController::class, 'like'])->name('posts.like')->middleware('auth');

// Comment routes
Route::get('/p/{post}/comments', [CommentsController::class, 'index'])->name('comments.index');
Route::post('/p/{post}/comments', [CommentsController::class, 'store'])->name('comments.store');
Route::patch('/comments/{comment}', [CommentsController::class, 'update'])->name('comments.update');
Route::delete('/comments/{comment}', [CommentsController::class, 'destroy'])->name('comments.destroy');
Route::post('/comments/{comment}/like', [CommentsController::class, 'like'])->name('comments.like');

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::get('/profile/{user}', [ProfilesController::class, 'index'])->name('profile.show');
Route::get('/profile/{user}/edit', [ProfilesController::class, 'edit'])->name('profile.edit');
Route::get('/profile/{user}/following', [FollowController::class, 'followingPage'])->name('following.page');

Route::patch('/profile/{user}', [ProfilesController::class, 'update'])->name('profile.update');
Route::patch('/profile/{user}/picture', [ProfilesController::class, 'updatePicture'])->name('profile.picture.update');
Route::patch('/profile/{user}/bio', [ProfilesController::class, 'updateBio'])->name('profile.update.bio');

Route::post('/follow/{id}', [FollowController::class, 'follow'])->name('follow');
Route::post('/unfollow/{id}', [FollowController::class, 'unfollow'])->name('unfollow');

Route::middleware('auth')->group(function () {
    Route::get('/chat', [ChatController::class, 'followingList'])->name('chat.list');
    Route::get('/chat/{user}', [ChatController::class, 'chatWithUser'])->name('chat.user');
    Route::get('/chat/messages/{user}', [ChatController::class, 'fetchMessages'])->name('chat.messages');
    Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');
});

Route::get('/search', [SearchController::class, 'search'])->name('search');
Route::get('/search-page', [App\Http\Controllers\SearchController::class, 'index'])->name('search.page');
