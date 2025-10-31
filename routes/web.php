<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RedirectController;

Route::middleware('throttle:redirects')
    ->get('/r/{slug}', RedirectController::class)
    ->name('redirect.go');
