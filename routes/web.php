<?php

use Illuminate\Support\Facades\Route;
use Filament\AvatarChat\Http\Controllers\AvatarController;
use Filament\AvatarChat\Http\Controllers\AvatarChatController;
use Illuminate\Support\Facades\Auth;

Route::middleware(['web'])->group(function () {
    Route::prefix('avatar')->group(function () {
        Route::get('/', [AvatarController::class, 'index'])->name('avatar.index')->middleware('auth');
        Route::get('/{uuid}', [AvatarChatController::class, 'show'])->name('avatar.chat')->middleware('auth');
        Route::post('/chat/send', [AvatarChatController::class, 'send'])->name('avatar.chat.send')->middleware('auth');
        Route::post('/avatar/chat/reset', [AvatarChatController::class, 'reset'])->name('avatar.chat.reset');
    });
});