<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'spa')->name('app');
Route::view('/login', 'auth.spa-login')->name('auth.login');
Route::view('/register', 'auth.spa-register')->name('auth.register');
Route::view('/stats', 'spa-stats')->name('app.stats');

// Redirección pública (si ya la tienes, conserva tu controlador)
Route::get('/r/{slug}', \App\Http\Controllers\RedirectController::class)
    ->middleware('throttle:redirects'); // opcional, ver paso 5
