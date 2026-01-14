<?php

use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\ZeToolsAuthController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::middleware('guest')->group(function () {
    Volt::route('register', 'pages.auth.register')
        ->name('register');

    // Use blade login view with Google button
    Route::view('login', 'livewire.pages.auth.login')
        ->name('login');

    Volt::route('forgot-password', 'pages.auth.forgot-password')
        ->name('password.request');

    Volt::route('reset-password/{token}', 'pages.auth.reset-password')
        ->name('password.reset');
});

Route::middleware('auth')->group(function () {
    Volt::route('verify-email', 'pages.auth.verify-email')
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Volt::route('confirm-password', 'pages.auth.confirm-password')
        ->name('password.confirm');
});

Route::post('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    
    return redirect('/');
})->name('logout');


// Rotas de autenticação ZeTools OAuth2 (Provider Principal)
Route::middleware('guest')->group(function () {
  Route::get('auth/zetools', [ZeToolsAuthController::class, 'redirect'])->name('auth.zetools');
  Route::get('auth/callback', [ZeToolsAuthController::class, 'callback'])->name('auth.zetools.callback');
  // Switch account: logout and redirect to ZeTools for new login
  Route::get('auth/switch', [ZeToolsAuthController::class, 'switch'])->name('auth.switch');
});

Route::post('logout', [ZeToolsAuthController::class, 'logout'])->name('logout')->middleware('auth');
