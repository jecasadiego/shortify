<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ShortLinkController;
use App\Http\Controllers\StatsController;

Route::prefix('v1')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('/login',    [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);

        Route::get('/links',         [ShortLinkController::class, 'index']);
        Route::post('/links',        [ShortLinkController::class, 'store']);

        Route::get('/links/{id}',    [ShortLinkController::class, 'show']);
        Route::patch('/links/{id}',  [ShortLinkController::class, 'update']);
        Route::delete('/links/{id}', [ShortLinkController::class, 'destroy']);

        Route::get('/links/{id}/stats/summary', [StatsController::class, 'summary']);
    });

    // Ruta de prueba
    Route::get('/ping', fn() => response()->json(['pong' => true]));
});
