<?php

namespace App\Http\Controllers;

use App\Models\Promo;
use Illuminate\Http\Request;

class PromoController extends Controller
{
    public function validate_(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'order_amount' => 'required|numeric|min:0',
            'vendor_id' => 'nullable|integer',
        ]);

        $promo = Promo::active()
            ->where('promo_code', strtoupper($request->code))
            ->where('min_order', '<=', $request->order_amount)
            ->when($request->vendor_id, fn($q) => $q->where(fn($q2) => $q2->whereNull('vendor_id')->orWhere('vendor_id', $request->vendor_id)))
            ->first();

        if (!$promo) {
            return response()->json(['valid' => false, 'message' => 'Kode promo tidak valid atau sudah kadaluarsa.']);
        }

        $discount = $promo->calculateDiscount($request->order_amount);

        return response()->json([
            'valid' => true,
            'promo' => $promo,
            'discount' => $discount,
        ]);
    }
}
