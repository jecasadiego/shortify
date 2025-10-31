<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('links-create', function ($request) {
            $key = optional($request->user())->id
                ? 'user:' . $request->user()->id
                : 'ip:' . $request->ip();

            return [
                Limit::perMinute(20)->by($key)->response(function () {
                    return response()->json([
                        'message' => 'Rate limit exceeded for link creation'
                    ], 429);
                }),
            ];
        });

        RateLimiter::for('redirects', function ($request) {
            return [
                Limit::perMinute(120)->by('ip:' . $request->ip()),
            ];
        });
    }
}
