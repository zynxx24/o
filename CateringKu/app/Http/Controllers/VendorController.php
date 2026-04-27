<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Review;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VendorController extends Controller
{
    public function index(Request $request)
    {
        $query = Vendor::active();

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('vendor_name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        $sort = $request->get('sort', 'rating');
        $query = match ($sort) {
            'newest' => $query->orderByDesc('created_at'),
            'name' => $query->orderBy('vendor_name'),
            default => $query->orderByDesc('rating')->orderByDesc('total_reviews'),
        };

        $vendors = $query->paginate(12)->withQueryString();
        $categories = Category::all();
        $cities = Vendor::active()->distinct()->pluck('city')->filter()->values();

        return Inertia::render('Search', [
            'vendors' => $vendors,
            'categories' => $categories,
            'cities' => $cities,
            'filters' => $request->only(['q', 'city', 'sort', 'type']),
        ]);
    }

    public function show(string $slug)
    {
        $vendor = Vendor::findBySlug($slug);

        $vendor->load([
            'menuItems' => function ($q) {
                $q->available()->with('category')->orderBy('category_id');
            },
            'packages' => function ($q) {
                $q->where('is_available', true)->with('items');
            }
        ]);

        $reviews = Review::where('vendor_id', $vendor->vendor_id)
            ->with('user:id,name')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $categories = $vendor->menuItems->pluck('category')->filter()->unique('category_id')->values();

        return Inertia::render('VendorDetail', [
            'vendor' => $vendor,
            'reviews' => $reviews,
            'menuCategories' => $categories,
        ]);
    }
}
