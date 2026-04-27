<?php

namespace App\Services;

use App\Models\CommissionRule;
use App\Models\Order;
use App\Models\OrderCommission;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CommissionService
{
    public function __construct(protected WalletService $walletService)
    {
    }

    /**
     * Distribusikan komisi setelah pembayaran terverifikasi lunas.
     * Dipanggil dari AdminController::verifyPayment() ketika order payment_status = 'paid'.
     */
    public function distributeCommission(Order $order): OrderCommission
    {
        return DB::transaction(function () use ($order) {
            // Hindari distribusi ganda
            $existing = OrderCommission::where('order_id', $order->order_id)->first();
            if ($existing && $existing->status === 'distributed') {
                return $existing;
            }

            $rule = CommissionRule::active();
            $gross = (float) $order->total_amount;

            // 1. Pajak negara: gunakan nilai PPN yang sudah dihitung saat checkout
            //    (BUKAN hitung ulang dari gross, karena gross sudah termasuk PPN → double-tax)
            $taxAmount = (float) $order->tax;
            $taxRate = $gross > 0 ? round($taxAmount / $gross * 100, 2) : (float) $rule->tax_rate;

            // 2. Net yang bisa didistribusikan: gross dikurangi PPN & ongkir
            //    Ongkir tidak ikut split platform/vendor (biaya operasional)
            $deliveryFee = (float) $order->delivery_fee;
            $netAmount = $gross - $taxAmount - $deliveryFee; // = subtotal

            // 3. Platform/admin fee (dari net)
            $platformAmount = round($netAmount * ($rule->platform_rate / 100), 2);

            // 4. Vendor dapat sisanya
            $vendorAmount = round($netAmount - $platformAmount, 2);

            // Buat/update record komisi
            $commission = OrderCommission::updateOrCreate(
                ['order_id' => $order->order_id],
                [
                    'gross_amount' => $gross,
                    'tax_amount' => $taxAmount,
                    'platform_amount' => $platformAmount,
                    'vendor_amount' => $vendorAmount,
                    'tax_rate' => $taxRate,         // rate aktual berdasarkan nilai nyata
                    'platform_rate' => $rule->platform_rate,
                    'status' => 'pending',
                ]
            );

            // Credit wallet platform (admin)
            $adminUser = User::where('role', 'admin')->first();
            if ($adminUser) {
                $this->walletService->credit(
                    $adminUser,
                    $platformAmount,
                    'platform_fee',
                    "Platform fee dari order #{$order->order_number}",
                    'order',
                    $order->order_id
                );
            }

            // Credit wallet vendor
            $vendorUser = $order->vendor?->user;
            if ($vendorUser) {
                $this->walletService->credit(
                    $vendorUser,
                    $vendorAmount,
                    'commission',
                    "Payout vendor dari order #{$order->order_number}",
                    'order',
                    $order->order_id
                );
            }

            $commission->update([
                'status' => 'distributed',
                'distributed_at' => now(),
            ]);

            return $commission;
        });
    }
}
