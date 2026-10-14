<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\DocumentController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/documents');

Route::middleware('guest')->group(function () {
    Route::get('/connexion', [LoginController::class, 'create'])->name('login');
    Route::post('/connexion', [LoginController::class, 'store']);

    Route::get('/mot-de-passe-oublie', [ForgotPasswordController::class, 'create'])
        ->name('password.request');
    Route::post('/mot-de-passe-oublie', [ForgotPasswordController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('password.email');
});

// Liens reçus par courriel : accessibles même si une session est ouverte.
Route::get('/reinitialisation/{token}', [NewPasswordController::class, 'create'])
    ->defaults('broker', 'users')->name('password.reset');
Route::post('/reinitialisation', [NewPasswordController::class, 'store'])
    ->defaults('broker', 'users')->middleware('throttle:6,1')->name('password.update');

Route::get('/invitation/{token}', [NewPasswordController::class, 'create'])
    ->defaults('broker', 'invitations')->name('invitation.accept');
Route::post('/invitation', [NewPasswordController::class, 'store'])
    ->defaults('broker', 'invitations')->middleware('throttle:6,1')->name('invitation.store');

Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');
    Route::post('/deconnexion', [LoginController::class, 'destroy'])->name('logout');
});