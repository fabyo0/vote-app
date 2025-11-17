<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

final class AppServiceProvider extends ServiceProvider
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
        $this->adminBladeDirective();
        $this->configurePasswordValidation();
        //   $this->configureModels();
        $this->configureRateLimiting();
        $this->configureQueryLogging();
    }

    private function adminBladeDirective(): void
    {
        Blade::if('admin', fn(): bool => auth()->check() && auth()->user()->isAdmin());
    }


    private function configurePasswordValidation(): void
    {
        Password::defaults(fn() => $this->app->isProduction()
            ? Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : Password::min(6));
    }

    /**
     * Configure query logging for development
     */
    private function configureQueryLogging(): void
    {
        // N+1 Query Detection
        DB::listen(function ($query): void {
            if (str_contains((string) $query->sql, 'select * from')) {
                $backtrace = collect(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 10))
                    ->filter(fn($trace): bool => isset($trace['file']) &&
                        ! str_contains($trace['file'], '/vendor/') &&
                        ! str_contains($trace['file'], '/framework/'))
                    ->first();

                $location = isset($backtrace)
                    ? basename($backtrace['file']) . ':' . $backtrace['line']
                    : 'Unknown location';

                logger()->warning(
                    "Possible N+1 query detected in {$location}",
                    [
                        'sql' => $query->sql,
                        'bindings' => $query->bindings,
                        'time' => $query->time . 'ms',
                        'caller' => $location,
                    ],
                );
            }
        });

        DB::listen(function ($query): void {
            if ($query->time > 100) {
                logger()->warning(
                    'Slow query detected',
                    [
                        'sql' => $query->sql,
                        'bindings' => $query->bindings,
                        'time' => $query->time . 'ms',
                    ],
                );
            }
        });
    }

    private function configureRateLimiting(): void
    {
        RateLimiter::for(
            'global',
            fn(Request $request) => Limit::perMinute(60)->by($request->ip()),
        );

        RateLimiter::for(
            'api',
            fn(Request $request) => Limit::perMinute(60)->by($request->user()?->id ?: $request->ip()),
        );

        RateLimiter::for(
            'auth',
            fn(Request $request) => Limit::perMinute(5)->by($request->ip()),
        );

        RateLimiter::for(
            'login',
            fn(Request $request) => Limit::perMinute(5)
                ->by($request->input('email') . '|' . $request->ip())
                ->response(fn() => response()->json(['message' => 'Too many login attempts.'], 429)),
        );
    }
}
