<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Review;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::where('user_id', $request->user()->id)
            ->with('vendor')
            ->orderByDesc('created_at')
            ->paginate(10);

        return Inertia::render('Orders/Index', ['orders' => $orders]);
    }

    public function show(Request $request, int $id)
    {
        $order = Order::with(['vendor', 'items.menuItem', 'payments', 'review'])
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);

        $canReview = $order->status === 'completed' && !$order->review;

        return Inertia::render('Orders/Show', [
            'order' => $order,
            'canReview' => $canReview,
        ]);
    }

    public function receipt(Request $request, int $id)
    {
        $query = Order::with(['user', 'vendor', 'items.menuItem', 'payments', 'commission']);

        // Admin can view any order receipt, users can only view their own
        if ($request->user()->role !== 'admin') {
            $query->where('user_id', $request->user()->id);
        }

        $order = $query->findOrFail($id);

        return Inertia::render('Orders/Receipt', [
            'order' => $order,
        ]);
    }

    public function cancel(Request $request, int $id)
    {
        $order = Order::where('user_id', $request->user()->id)
            ->where('status', 'pending')
            ->findOrFail($id);

        // FIX: Validasi reason agar tidak bisa diisi string panjang/berbahaya
        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $order->update([
            'status' => 'cancelled',
            'cancellation_reason' => $request->input('reason', 'Dibatalkan oleh pelanggan'),
        ]);

        return back()->with('success', 'Pesanan berhasil dibatalkan.');
    }

    public function storeReview(Request $request, int $orderId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'food_rating' => 'nullable|integer|min:1|max:5',
            'service_rating' => 'nullable|integer|min:1|max:5',
            'delivery_rating' => 'nullable|integer|min:1|max:5',
            'review_text' => 'nullable|string|max:1000',
        ]);

        $order = Order::where('user_id', $request->user()->id)
            ->where('status', 'completed')
            ->findOrFail($orderId);

        abort_if($order->review, 409, 'Sudah pernah memberikan review.');

        Review::create([
            'order_id' => $order->order_id,
            'user_id' => $request->user()->id,
            'vendor_id' => $order->vendor_id,
            'rating' => $request->rating,
            'food_rating' => $request->food_rating,
            'service_rating' => $request->service_rating,
            'delivery_rating' => $request->delivery_rating,
            'review_text' => $request->review_text,
        ]);

        // Update vendor rating
        $vendor = $order->vendor;
        $vendor->update([
            'rating' => $vendor->reviews()->avg('rating'),
            'total_reviews' => $vendor->reviews()->count(),
        ]);

        return back()->with('success', 'Review berhasil dikirim!');
    }
}
