<?php

use App\Http\Controllers\Gemini\GeminiController;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => ['auth', 'verified']], function () {
    Route::get('/', [GeminiController::class, 'index'])->name('home');
    Route::post('/ask-gemini', [GeminiController::class, 'postQuestion'])->name('ask-gemini')->middleware('throttle:20,1');;
});
require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
