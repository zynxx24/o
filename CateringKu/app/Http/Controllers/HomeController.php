<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Vendor;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        $vendors = Vendor::active()
            ->orderByDesc('rating')
            ->limit(6)
            ->get();

        $categories = Category::withCount(['menuItems' => fn($q) => $q->where('is_available', true)])
            ->get();

        return Inertia::render('Home', [
            'vendors' => $vendors,
            'categories' => $categories,
        ]);
    }
}
