<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\WithdrawalRequest;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WalletController extends Controller
{
    public function __construct(protected WalletService $walletService) {}

    public function index(Request $request)
    {
        $user = $request->user();
        $wallet = $user->getOrCreateWallet();

        $transactions = WalletTransaction::where('wallet_id', $wallet->wallet_id)
            ->orderByDesc('created_at')
            ->paginate(20);

        $pendingWithdrawal = WithdrawalRequest::where('wallet_id', $wallet->wallet_id)
            ->where('status', 'pending')
            ->first();

        $withdrawalHistory = WithdrawalRequest::where('wallet_id', $wallet->wallet_id)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return Inertia::render('Wallet/Index', [
            'wallet'             => $wallet,
            'transactions'       => $transactions,
            'pendingWithdrawal'  => $pendingWithdrawal,
            'withdrawalHistory'  => $withdrawalHistory,
        ]);
    }

    public function requestWithdrawal(Request $request)
    {
        $validated = $request->validate([
            'amount'       => 'required|numeric|min:50000',
            'bank_name'    => 'required|string|max:100',
            'bank_account' => 'required|string|max:50',
            'account_name' => 'required|string|max:100',
        ]);

        $user   = $request->user();
        $wallet = $user->getOrCreateWallet();

        if ($wallet->hasPendingWithdrawal()) {
            return back()->withErrors(['amount' => 'Anda masih memiliki permintaan penarikan yang sedang diproses.']);
        }

        if ($wallet->balance < $validated['amount']) {
            return back()->withErrors(['amount' => 'Saldo tidak mencukupi.']);
        }

        WithdrawalRequest::create([
            'wallet_id'    => $wallet->wallet_id,
            'amount'       => $validated['amount'],
            'bank_name'    => $validated['bank_name'],
            'bank_account' => $validated['bank_account'],
            'account_name' => $validated['account_name'],
            'status'       => 'pending',
        ]);

        return back()->with('success', 'Permintaan penarikan berhasil diajukan. Admin akan memproses dalam 1-2 hari kerja.');
    }
}
