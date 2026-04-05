<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'totalOrders' => Order::count(),
            'totalRevenue' => Order::where('payment_status', 'paid')->sum('total_amount'),
            'totalUsers' => User::where('role', 'customer')->count(),
            'totalVendors' => Vendor::active()->count(),
            'pendingOrders' => Order::where('status', 'pending')->count(),
            'unreadMessages' => ContactMessage::unread()->count(),
        ];

        $recentOrders = Order::with(['user:id,name', 'vendor'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $pendingPayments = Payment::where('payment_status', 'pending')
            ->with(['order.user:id,name'])
            ->orderBy('payment_date')
            ->limit(10)
            ->get();

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'recentOrders' => $recentOrders,
            'pendingPayments' => $pendingPayments,
        ]);
    }

    public function orders(Request $request)
    {
        $orders = Order::with(['user:id,name', 'vendor'])
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->orderByDesc('created_at')
            ->paginate(15);

        return Inertia::render('Admin/Orders', ['orders' => $orders, 'filter' => $request->status]);
    }

    public function updateOrderStatus(Request $request, int $id)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,preparing,delivering,completed,cancelled',
        ]);

        $order = Order::findOrFail($id);
        $order->update(['status' => $request->status]);

        return back()->with('success', 'Status pesanan berhasil diperbarui.');
    }

    public function verifyPayment(Request $request, int $paymentId)
    {
        $payment = Payment::findOrFail($paymentId);
        $payment->update([
            'payment_status' => 'verified',
            'verified_by' => $request->user()->id,
            'verified_at' => now(),
        ]);

        $order = $payment->order;
        $totalPaid = $order->payments()->where('payment_status', 'verified')->sum('amount');
        if ($totalPaid >= $order->total_amount) {
            $order->update(['payment_status' => 'paid']);
        } else {
            $order->update(['payment_status' => 'partial']);
        }

        return back()->with('success', 'Pembayaran berhasil diverifikasi.');
    }

    public function messages()
    {
        $messages = ContactMessage::orderByDesc('created_at')->paginate(20);
        return Inertia::render('Admin/Messages', ['messages' => $messages]);
    }

    public function messageDetail(int $id)
    {
        $message = ContactMessage::findOrFail($id);
        $message->update(['is_read' => true]);
        return Inertia::render('Admin/MessageDetail', ['message' => $message]);
    }
}
