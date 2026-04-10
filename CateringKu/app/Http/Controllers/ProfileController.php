<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Review;
use Inertia\Inertia;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user();

        $recentOrders = Order::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get(['order_id', 'order_number', 'status', 'total_amount', 'created_at']);

        $reviews = Review::where('reviews.user_id', $user->id)
            ->join('vendors', 'reviews.vendor_id', '=', 'vendors.vendor_id')
            ->orderByDesc('reviews.created_at')
            ->limit(5)
            ->get([
                'reviews.review_id',
                'vendors.vendor_name',
                'reviews.rating',
                'reviews.review_text as comment',
                'reviews.created_at',
            ]);

        $stats = [
            'totalOrders' => Order::where('user_id', $user->id)->count(),
            'completedOrders' => Order::where('user_id', $user->id)->where('status', 'completed')->count(),
            'totalSpent' => (int) Order::where('user_id', $user->id)->where('status', 'completed')->sum('total_amount'),
        ];

        return Inertia::render('Profile', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'phone' => $user->phone,
                'address' => $user->address,
                'created_at' => $user->created_at,
            ],
            'recentOrders' => $recentOrders,
            'reviews' => $reviews,
            'stats' => $stats,
        ]);
    }
}
