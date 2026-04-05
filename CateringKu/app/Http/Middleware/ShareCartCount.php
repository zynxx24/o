<?php

namespace App\Http\Middleware;

use App\Models\Cart;
use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ShareCartCount
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user()) {
            $cartCount = Cart::where('user_id', $request->user()->id)
                ->withCount('items')
                ->get()
                ->sum('items_count');
            Inertia::share('cartCount', $cartCount);
        } else {
            Inertia::share('cartCount', 0);
        }

        return $next($request);
    }
}
