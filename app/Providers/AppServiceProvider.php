<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
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
     *
     * @return void
     */
    public function register(): void
    {
        if ($this->app->isLocal()) {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->adminBladeDirective();
        $this->configurePasswordValidation();
        $this->configureModels();
        $this->configureRateLimiting();

    }

    private function adminBladeDirective(): void
    {
        Blade::if('admin', fn() => auth()->check() && auth()->user()->isAdmin());
    }


    private function configurePasswordValidation(): void
    {
        Password::defaults(fn() => $this->app->isProduction()
            ? Password::min(8)
                ->mixedCase()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : Password::min(6));
    }

    private function configureModels(): void
    {
        if ($this->app->environment('local', 'testing')) {
            DB::listen(function ($query): void {
                if (str_contains($query->sql, 'select * from')) {
                    $backtrace = collect(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 10))
                        ->filter(fn($trace): bool => isset($trace['file']) &&
                            ! str_contains($trace['file'], '/vendor/') &&
                            ! str_contains($trace['file'], '/framework/'))
                        ->first();

                    $location = isset($backtrace)
                        ? basename($backtrace['file']) . ':' . $backtrace['line']
                        : 'Unknown location';

                    logger()->info(
                        "Possible N+1 query in {$location}: {$query->sql}",
                        ['bindings' => $query->bindings, 'time' => $query->time, 'caller' => $location],
                    );
                }
            });
        }
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
    }
}
