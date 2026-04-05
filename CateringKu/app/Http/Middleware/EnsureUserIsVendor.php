<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserIsVendor
{
    public function handle(Request $request, Closure $next)
    {
        if (! $request->user() || ! $request->user()->isVendor()) {
            abort(403, 'Akses ditolak.');
        }
        return $next($request);
    }
}
