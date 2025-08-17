<?php

use App\Http\Controllers\Gemini\GeminiController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;



Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::group(['middleware' => ['auth', 'verified']], function () {
    Route::get('/', [GeminiController::class, 'index'])->name('home');
    Route::post('/ask-gemini', [GeminiController::class, 'getQuestion'])->name('ask-gemini')->middleware('throttle:20,1');
    Route::get('/chat-window-history', [GeminiController::class, 'getChatWindowHistory'])->name('chat-window-history');
    Route::get('conversations-history', [GeminiController::class, 'getConversationsHistory'])->name('conversations-history');
});
require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
