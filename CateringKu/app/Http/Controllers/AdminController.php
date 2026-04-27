<?php

namespace App\Http\Controllers;

use App\Models\CommissionRule;
use App\Models\ContactMessage;
use App\Models\Order;
use App\Models\OrderCommission;
use App\Models\Payment;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorApplication;
use App\Models\Wallet;
use App\Models\WithdrawalRequest;
use App\Services\CommissionService;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function __construct(
        protected CommissionService $commissionService,
        protected WalletService $walletService
    ) {
    }

    public function dashboard()
    {
        $stats = [
            'totalOrders' => Order::count(),
            'totalRevenue' => Order::where('payment_status', 'paid')->sum('total_amount'),
            'totalUsers' => User::where('role', 'customer')->count(),
            'totalVendors' => Vendor::active()->count(),
            'pendingOrders' => Order::where('status', 'pending')->count(),
            'unreadMessages' => ContactMessage::unread()->count(),
            'pendingWithdrawals' => WithdrawalRequest::where('status', 'pending')->count(),
            'pendingVendorApps' => VendorApplication::where('status', 'submitted')->count(),
            'totalCommissions' => OrderCommission::where('status', 'distributed')->sum('platform_amount'),
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

        // All-time monthly revenue from paid orders
        $monthlyRevenue = Order::where('payment_status', 'paid')
            ->selectRaw("strftime('%Y-%m', created_at) as month, SUM(total_amount) as revenue")
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(fn($row) => [
                'month' => \Carbon\Carbon::createFromFormat('Y-m', $row->month)->translatedFormat('M Y'),
                'revenue' => (float) $row->revenue,
            ]);

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'recentOrders' => $recentOrders,
            'pendingPayments' => $pendingPayments,
            'monthlyRevenue' => $monthlyRevenue,
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

    public function orderDetail(int $id)
    {
        $order = Order::with(['user', 'vendor', 'items.menuItem', 'payments', 'commission'])
            ->findOrFail($id);

        return Inertia::render('Admin/OrderDetail', [
            'order' => $order,
        ]);
    }

    public function verifyPayment(Request $request, int $paymentId)
    {
        $payment = Payment::findOrFail($paymentId);

        // FIX: Cegah verifikasi ganda — mencegah double-credit ke wallet
        if ($payment->payment_status === 'verified') {
            return back()->withErrors(['msg' => 'Pembayaran ini sudah diverifikasi sebelumnya.']);
        }

        $payment->update([
            'payment_status' => 'verified',
            'verified_by' => $request->user()->id,
            'verified_at' => now(),
        ]);

        $order = $payment->order;
        $totalPaid = $order->payments()->where('payment_status', 'verified')->sum('amount');

        if ($totalPaid >= $order->total_amount) {
            $order->update(['payment_status' => 'paid']);

            // Distribusikan komisi otomatis
            $order->load('vendor.user');
            $this->commissionService->distributeCommission($order);
        } else {
            $order->update(['payment_status' => 'partial']);
        }

        return back()->with('success', 'Pembayaran berhasil diverifikasi dan komisi telah didistribusikan.');
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

    // ─── Wallets ─────────────────────────────────────────────────────────────

    public function wallets(Request $request)
    {
        $wallets = Wallet::with('user:id,name,email,role')
            ->when($request->role, fn($q, $r) => $q->whereHas('user', fn($u) => $u->where('role', $r)))
            ->orderByDesc('balance')
            ->paginate(20);

        $summary = [
            'totalBalance' => Wallet::sum('balance'),
            'totalFrozen' => Wallet::sum('frozen_balance'),
            'totalEarned' => Wallet::sum('total_earned'),
            'totalWithdrawn' => Wallet::sum('total_withdrawn'),
        ];

        return Inertia::render('Admin/Wallets', [
            'wallets' => $wallets,
            'summary' => $summary,
            'filter' => $request->role,
        ]);
    }

    // ─── Withdrawals ─────────────────────────────────────────────────────────

    public function withdrawals(Request $request)
    {
        $withdrawals = WithdrawalRequest::with(['wallet.user:id,name,email'])
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->orderByDesc('created_at')
            ->paginate(20);

        return Inertia::render('Admin/Withdrawals', [
            'withdrawals' => $withdrawals,
            'filter' => $request->status,
        ]);
    }

    public function approveWithdrawal(Request $request, int $id)
    {
        $withdrawal = WithdrawalRequest::with('wallet.user')->findOrFail($id);

        if ($withdrawal->status !== 'pending') {
            return back()->withErrors(['msg' => 'Permintaan ini sudah diproses.']);
        }

        try {
            $this->walletService->debit(
                $withdrawal->wallet->user,
                $withdrawal->amount,
                'withdrawal',
                "Penarikan dana disetujui admin #{$request->user()->id}",
                'withdrawal_request',
                $withdrawal->id
            );

            $withdrawal->update([
                'status' => 'approved',
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
                'admin_note' => $request->note,
            ]);

            return back()->with('success', 'Penarikan dana berhasil disetujui.');
        } catch (\Exception $e) {
            return back()->withErrors(['msg' => $e->getMessage()]);
        }
    }

    public function rejectWithdrawal(Request $request, int $id)
    {
        $request->validate(['note' => 'required|string|max:500']);

        $withdrawal = WithdrawalRequest::findOrFail($id);

        if ($withdrawal->status !== 'pending') {
            return back()->withErrors(['msg' => 'Permintaan ini sudah diproses.']);
        }

        $withdrawal->update([
            'status' => 'rejected',
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'admin_note' => $request->note,
        ]);

        return back()->with('success', 'Permintaan penarikan ditolak.');
    }

    // ─── Vendor Applications ─────────────────────────────────────────────────

    public function vendorApplications(Request $request)
    {
        $applications = VendorApplication::with(['user:id,name,email', 'reviewer:id,name'])
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->orderByDesc('created_at')
            ->paginate(20);

        return Inertia::render('Admin/VendorApplications', [
            'applications' => $applications,
            'filter' => $request->status,
        ]);
    }

    public function approveVendorApplication(Request $request, int $id)
    {
        $application = VendorApplication::with('user')->findOrFail($id);

        if (!in_array($application->status, ['submitted', 'deposit_pending'])) {
            return back()->withErrors(['msg' => 'Aplikasi ini sudah diproses atau statusnya tidak memungkinkan untuk disetujui.']);
        }

        $user = $application->user;

        // Buat vendor record
        Vendor::create([
            'user_id' => $user->id,
            'vendor_name' => $application->vendor_name,
            'description' => $application->description,
            'address' => $application->address,
            'city' => $application->city,
            'province' => $application->province,
            'phone' => $application->phone,
            'email' => $application->email,
            'status' => 'active',
        ]);

        // Bekukan deposit di wallet
        $wallet = $user->getOrCreateWallet();
        $wallet->increment('frozen_balance', $application->deposit_amount);

        \App\Models\WalletTransaction::create([
            'wallet_id' => $wallet->wallet_id,
            'type' => 'debit',
            'category' => 'deposit',
            'amount' => $application->deposit_amount,
            'balance_after' => $wallet->balance,
            'reference_type' => 'vendor_application',
            'reference_id' => $application->id,
            'description' => 'Deposit vendor dibekukan setelah persetujuan',
        ]);

        // FIX: Gunakan forceFill()->save() karena 'role' dan 'vendor_deposit_status'
        // sudah dihapus dari $fillable untuk mencegah mass assignment.
        $user->forceFill([
            'role' => 'vendor',
            'vendor_deposit_status' => 'frozen',
        ])->save();

        $application->update([
            'status' => 'approved',
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Aplikasi vendor disetujui. User telah diupgrade menjadi vendor.');
    }

    public function rejectVendorApplication(Request $request, int $id)
    {
        $request->validate(['rejection_reason' => 'required|string|max:500']);

        $application = VendorApplication::findOrFail($id);

        if (!in_array($application->status, ['submitted', 'deposit_pending'])) {
            return back()->withErrors(['msg' => 'Aplikasi ini sudah diproses atau tidak dapat ditolak.']);
        }

        $application->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Aplikasi vendor ditolak.');
    }

    // ─── Commissions ─────────────────────────────────────────────────────────

    public function commissions(Request $request)
    {
        $commissions = OrderCommission::with(['order.user:id,name', 'order.vendor'])
            ->orderByDesc('created_at')
            ->paginate(20);

        $summary = [
            'totalGross' => OrderCommission::where('status', 'distributed')->sum('gross_amount'),
            'totalTax' => OrderCommission::where('status', 'distributed')->sum('tax_amount'),
            'totalPlatform' => OrderCommission::where('status', 'distributed')->sum('platform_amount'),
            'totalVendor' => OrderCommission::where('status', 'distributed')->sum('vendor_amount'),
        ];

        $rule = CommissionRule::active();

        return Inertia::render('Admin/Commissions', [
            'commissions' => $commissions,
            'summary' => $summary,
            'rule' => $rule,
        ]);
    }

    public function updateCommissionRule(Request $request)
    {
        $validated = $request->validate([
            'tax_rate' => 'required|numeric|min:0|max:50',
            'platform_rate' => 'required|numeric|min:0|max:50',
        ]);

        $rule = CommissionRule::active();
        $rule->update($validated);

        return back()->with('success', 'Aturan komisi berhasil diperbarui.');
    }
}
