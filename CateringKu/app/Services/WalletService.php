<?php

namespace App\Services;

use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;

class WalletService
{
    /**
     * Kredit (masuk) ke dompet user.
     */
    public function credit(
        User $user,
        float $amount,
        string $category,
        string $description,
        ?string $referenceType = null,
        ?int $referenceId = null
    ): WalletTransaction {
        return DB::transaction(function () use ($user, $amount, $category, $description, $referenceType, $referenceId) {
            $wallet = $user->getOrCreateWallet();

            $wallet->increment('balance', $amount);
            $wallet->increment('total_earned', $amount);
            $wallet->refresh();

            return WalletTransaction::create([
                'wallet_id'      => $wallet->wallet_id,
                'type'           => 'credit',
                'category'       => $category,
                'amount'         => $amount,
                'balance_after'  => $wallet->balance,
                'reference_type' => $referenceType,
                'reference_id'   => $referenceId,
                'description'    => $description,
            ]);
        });
    }

    /**
     * Debit (keluar) dari dompet user.
     *
     * @throws \Exception jika saldo tidak cukup
     */
    public function debit(
        User $user,
        float $amount,
        string $category,
        string $description,
        ?string $referenceType = null,
        ?int $referenceId = null
    ): WalletTransaction {
        return DB::transaction(function () use ($user, $amount, $category, $description, $referenceType, $referenceId) {
            // FIX: lockForUpdate() mencegah race condition — dua request
            // tidak bisa membaca saldo yang sama secara bersamaan.
            $wallet = Wallet::where('user_id', $user->id)->lockForUpdate()->first()
                ?? $user->getOrCreateWallet();

            if ($wallet->balance < $amount) {
                throw new \Exception("Saldo tidak mencukupi. Saldo tersedia: Rp " . number_format($wallet->balance, 0, ',', '.'));
            }

            $wallet->decrement('balance', $amount);
            $wallet->increment('total_withdrawn', $amount);
            $wallet->refresh();

            return WalletTransaction::create([
                'wallet_id'      => $wallet->wallet_id,
                'type'           => 'debit',
                'category'       => $category,
                'amount'         => $amount,
                'balance_after'  => $wallet->balance,
                'reference_type' => $referenceType,
                'reference_id'   => $referenceId,
                'description'    => $description,
            ]);
        });
    }

    /**
     * Bekukan saldo (untuk deposit vendor).
     * Dana ditransfer dari balance → frozen_balance.
     *
     * @throws \Exception jika saldo tidak cukup
     */
    public function freeze(User $user, float $amount, string $description): WalletTransaction
    {
        return DB::transaction(function () use ($user, $amount, $description) {
            // FIX: lockForUpdate() untuk atomisitas freeze
            $wallet = Wallet::where('user_id', $user->id)->lockForUpdate()->first()
                ?? $user->getOrCreateWallet();

            if ($wallet->balance < $amount) {
                throw new \Exception("Saldo tidak mencukupi untuk dibekukan.");
            }

            $wallet->decrement('balance', $amount);
            $wallet->increment('frozen_balance', $amount);
            $wallet->refresh();

            return WalletTransaction::create([
                'wallet_id'      => $wallet->wallet_id,
                'type'           => 'debit',
                'category'       => 'deposit',
                'amount'         => $amount,
                'balance_after'  => $wallet->balance,
                'reference_type' => null,
                'reference_id'   => null,
                'description'    => $description,
            ]);
        });
    }

    /**
     * Cairkan (unfreeze) deposit — saat vendor tarik deposit / ditolak.
     */
    public function unfreeze(User $user, float $amount, string $description): WalletTransaction
    {
        return DB::transaction(function () use ($user, $amount, $description) {
            $wallet = $user->getOrCreateWallet();

            if ($wallet->frozen_balance < $amount) {
                throw new \Exception("Dana yang dibekukan tidak mencukupi.");
            }

            $wallet->decrement('frozen_balance', $amount);
            $wallet->increment('balance', $amount);
            $wallet->refresh();

            return WalletTransaction::create([
                'wallet_id'      => $wallet->wallet_id,
                'type'           => 'credit',
                'category'       => 'deposit_refund',
                'amount'         => $amount,
                'balance_after'  => $wallet->balance,
                'reference_type' => null,
                'reference_id'   => null,
                'description'    => $description,
            ]);
        });
    }
}
