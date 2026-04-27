<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Models\Order;
use App\Models\Review;
use App\Models\Vendor;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VendorDashboardController extends Controller
{
    private function getVendor(Request $request): Vendor
    {
        return Vendor::where('user_id', $request->user()->id)->firstOrFail();
    }

    public function dashboard(Request $request)
    {
        $vendor = $this->getVendor($request);

        $stats = [
            'totalOrders' => $vendor->orders()->count(),
            'pendingOrders' => $vendor->orders()->where('status', 'pending')->count(),
            'totalRevenue' => $vendor->orders()->where('payment_status', 'paid')->sum('total_amount'),
            'avgRating' => $vendor->rating,
            'totalMenuItems' => $vendor->menuItems()->count(),
        ];

        $recentOrders = $vendor->orders()
            ->with('user:id,name,phone')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // All-time monthly revenue from paid orders
        $monthlyRevenue = $vendor->orders()
            ->where('payment_status', 'paid')
            ->selectRaw("strftime('%Y-%m', created_at) as month, SUM(total_amount) as revenue")
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(fn($row) => [
                'month' => \Carbon\Carbon::createFromFormat('Y-m', $row->month)->translatedFormat('M Y'),
                'revenue' => (float) $row->revenue,
            ]);

        return Inertia::render('Vendor/Dashboard', [
            'vendor' => $vendor,
            'stats' => $stats,
            'recentOrders' => $recentOrders,
            'monthlyRevenue' => $monthlyRevenue,
        ]);
    }

    public function orders(Request $request)
    {
        $vendor = $this->getVendor($request);

        $orders = $vendor->orders()
            ->with('user:id,name,phone')
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->orderByDesc('created_at')
            ->paginate(15);

        return Inertia::render('Vendor/Orders', ['orders' => $orders, 'filter' => $request->status]);
    }

    public function updateOrderStatus(Request $request, int $id)
    {
        $vendor = $this->getVendor($request);
        $order = $vendor->orders()->findOrFail($id);

        $request->validate(['status' => 'required|in:confirmed,preparing,delivering,completed']);
        $order->update(['status' => $request->status]);

        return back()->with('success', 'Status pesanan diperbarui.');
    }

    public function orderDetail(Request $request, int $id)
    {
        $vendor = $this->getVendor($request);
        $order = $vendor->orders()
            ->with(['user', 'items.menuItem', 'payments'])
            ->findOrFail($id);

        return Inertia::render('Vendor/OrderDetail', [
            'order' => $order,
        ]);
    }

    public function menu(Request $request)
    {
        $vendor = $this->getVendor($request);
        $menuItems = $vendor->menuItems()->with('category')->orderBy('category_id')->get();
        $categories = Category::all();

        return Inertia::render('Vendor/Menu', [
            'menuItems' => $menuItems,
            'categories' => $categories,
        ]);
    }

    public function storeMenuItem(Request $request)
    {
        $vendor = $this->getVendor($request);
        $request->validate([
            'item_name' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'category_id' => 'nullable|exists:categories,category_id',
            'description' => 'nullable|string|max:500',
            'unit' => 'required|string|max:20',
            'min_order' => 'required|integer|min:1',
        ]);

        MenuItem::create(array_merge(
            $request->only(['item_name', 'price', 'category_id', 'description', 'unit', 'min_order']),
            ['vendor_id' => $vendor->vendor_id, 'is_available' => true]
        ));

        return back()->with('success', 'Menu berhasil ditambahkan.');
    }

    public function updateMenuItem(Request $request, int $id)
    {
        $vendor = $this->getVendor($request);
        $item = MenuItem::where('vendor_id', $vendor->vendor_id)->findOrFail($id);

        $request->validate([
            'item_name' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'is_available' => 'required|boolean',
        ]);

        $item->update($request->only(['item_name', 'price', 'description', 'category_id', 'unit', 'min_order', 'is_available']));

        return back()->with('success', 'Menu berhasil diperbarui.');
    }

    public function deleteMenuItem(Request $request, int $id)
    {
        $vendor = $this->getVendor($request);
        MenuItem::where('vendor_id', $vendor->vendor_id)->findOrFail($id)->delete();
        return back()->with('success', 'Menu berhasil dihapus.');
    }

    public function reviews(Request $request)
    {
        $vendor = $this->getVendor($request);
        $reviews = $vendor->reviews()
            ->with('user:id,name')
            ->orderByDesc('created_at')
            ->paginate(15);

        return Inertia::render('Vendor/Reviews', ['reviews' => $reviews, 'vendorRating' => $vendor->rating]);
    }

    public function respondReview(Request $request, int $id)
    {
        $vendor = $this->getVendor($request);
        $review = Review::where('vendor_id', $vendor->vendor_id)->findOrFail($id);

        $request->validate(['response' => 'required|string|max:500']);
        $review->update(['vendor_response' => $request->response, 'response_date' => now()]);

        return back()->with('success', 'Respon berhasil dikirim.');
    }
}
