<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Comprehensive security headers to harden the application against
     * XSS, clickjacking, MIME sniffing, data injection, and other common attacks.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // ── Anti-Sniffing ──────────────────────────────────
        // Prevent MIME type sniffing (stop IE/Chrome from guessing)
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // ── Anti-Clickjacking ──────────────────────────────
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // ── XSS Filter ─────────────────────────────────────
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // ── Referrer Policy ─────────────────────────────────
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // ── Permissions / Feature Policy ─────────────────────
        $response->headers->set('Permissions-Policy', implode(', ', [
            'camera=()',
            'microphone=()',
            'geolocation=(self)',
            'payment=()',
            'usb=()',
            'bluetooth=()',
            'magnetometer=()',
            'gyroscope=()',
            'accelerometer=()',
        ]));

        // ── Cross-Origin Policies ───────────────────────────
        // Prevent other sites from embedding our resources
        $response->headers->set('Cross-Origin-Resource-Policy', 'same-origin');
        // Prevent window.opener attacks
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');

        // ── Content Security Policy ─────────────────────────
        // In dev: allow Vite dev server (port 5173) for HMR & asset loading
        $isDev = config('app.env') === 'local';
        $viteOrigins = $isDev ? ' http://127.0.0.1:5173 http://localhost:5173' : '';

        $cspDirectives = [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval'{$viteOrigins}", // unsafe-eval for Vite HMR
            "style-src 'self' 'unsafe-inline' https://fonts.bunny.net https://fonts.googleapis.com{$viteOrigins}",
            "font-src 'self' https://fonts.bunny.net https://fonts.gstatic.com data:{$viteOrigins}",
            "img-src 'self' data: blob: https:",
            "connect-src 'self' ws://127.0.0.1:5173 ws://localhost:5173 wss: https:{$viteOrigins}", // WebSocket for Vite HMR
            "media-src 'self'",
            "object-src 'none'",
            "child-src 'self'",
            "frame-src 'none'",
            "frame-ancestors 'self'",
            "form-action 'self'",
            "base-uri 'self'",
            "manifest-src 'self'",
            "worker-src 'self' blob:",
        ];
        $response->headers->set('Content-Security-Policy', implode('; ', $cspDirectives));

        // ── Cache Control (for authenticated/sensitive pages) ─
        if ($request->user()) {
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
        }

        // ── HTTPS Strict Transport Security ─────────────────
        if ($request->isSecure() || config('app.env') === 'production') {
            $response->headers->set('Strict-Transport-Security', 'max-age=63072000; includeSubDomains; preload');
        }

        // ── Remove Server Identification Headers ────────────
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');

        return $response;
    }
}
