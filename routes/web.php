<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/chat/send', [ChatController::class, 'send'])->name('chat.send');
    Route::get('/notifications', 'ChatController@fetchNotifications');

    Route::get('/messagenotification', [ChatController::class, 'index'])->name('messagenotification.index');
    Route::post('/notifications/mark-as-read/{id}', [ChatController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/chat/delete/{id}',[ChatController::class,'delete'])->name('chat.delete');

});

require __DIR__.'/auth.php';
