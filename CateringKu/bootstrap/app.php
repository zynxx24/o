<?php

use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->encryptCookies(except: ['appearance', 'sidebar_state']);

        $middleware->web(append: [
            SecurityHeaders::class,
            HandleAppearance::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);

        // ── Security Hardening ──────────────────────────────────

        // Prevent CSRF token mismatches
        $middleware->validateCsrfTokens(except: [
            // Add any webhook endpoints here
        ]);

        // Rate limiting — stricter for auth, normal for API
        $middleware->throttleApi('60,1');

        // Trust proxies (for load balancers / reverse proxies)
        $middleware->trustProxies(at: '*');

        // Force HTTPS in production
        if (env('APP_ENV') === 'production') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

