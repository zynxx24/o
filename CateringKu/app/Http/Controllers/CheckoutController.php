<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Promo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $carts = Cart::where('user_id', $request->user()->id)
            ->with(['vendor', 'items.menuItem', 'items.package'])
            ->get();

        if ($carts->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang kosong.');
        }

        return Inertia::render('Checkout', [
            'carts' => $carts,
            'user' => $request->user(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vendor_id'        => 'required|exists:vendors,vendor_id',
            'event_date'       => 'required|date|after:today',
            'event_time'       => 'required',
            'delivery_address' => 'required|string|max:500',
            'delivery_city'    => 'nullable|string|max:50',
            'num_people'       => 'required|integer|min:1|max:10000',
            'event_type'       => 'nullable|string|max:50',
            'special_request'  => 'nullable|string|max:1000',
            'payment_method'   => 'required|string|max:50',
            'payment_provider' => 'required|string|max:50',
            'promo_code'       => 'nullable|string|max:50',
        ], [
            'event_date.after'          => 'Tanggal acara harus setelah hari ini. Silakan pilih tanggal yang akan datang.',
            'num_people.min'            => 'Jumlah tamu minimal 1 orang.',
            'num_people.max'            => 'Jumlah tamu maksimal 10.000 orang.',
            'delivery_address.required' => 'Alamat pengiriman wajib diisi.',
            'payment_method.required'   => 'Silakan pilih metode pembayaran.',
            'payment_provider.required' => 'Silakan pilih provider pembayaran.',
        ]);

        // FIX: Pastikan vendor benar-benar aktif (bukan hanya exists di DB)
        $cart = Cart::where('user_id', $request->user()->id)
            ->where('vendor_id', $request->vendor_id)
            ->whereHas('vendor', fn ($q) => $q->where('status', 'active'))
            ->with(['items.menuItem', 'items.package'])
            ->firstOrFail();

        $numPeople = $request->num_people;

        // Calculate subtotal with quantities adjusted by num_people
        $subtotal = 0;
        foreach ($cart->items as $item) {
            $unitPrice   = $item->menuItem ? $item->menuItem->price : ($item->package ? $item->package->price_per_person : 0);
            $adjustedQty = $item->quantity * $numPeople;
            $subtotal   += $unitPrice * $adjustedQty;
        }

        // FIX: Re-validasi promo di server — TIDAK mengandalkan frontend saja.
        // Gunakan lockForUpdate() agar used_count aman dari race condition.
        $discount = 0;
        $promoId  = null;
        if (!empty($validated['promo_code'])) {
            $promo = Promo::active()
                ->where('promo_code', strtoupper($validated['promo_code']))
                ->where('min_order', '<=', $subtotal)
                ->lockForUpdate()
                ->first();

            if (!$promo) {
                return back()->withErrors(['promo_code' => 'Kode promo tidak valid, sudah kadaluarsa, atau tidak memenuhi syarat.'])->withInput();
            }

            $discount = $promo->calculateDiscount($subtotal);
            $promoId  = $promo->promo_id;
        }

        $tax          = round($subtotal * 0.11, 2);
        $deliveryFee  = 15000;
        $totalAmount  = $subtotal + $tax + $deliveryFee - $discount;

        $createdOrder = null;

        try {
            DB::transaction(function () use ($request, $cart, $subtotal, $tax, $deliveryFee, $discount, $totalAmount, $numPeople, $promoId, &$createdOrder) {
                $order = Order::create([
                    'user_id'          => $request->user()->id,
                    'vendor_id'        => $request->vendor_id,
                    'order_number'     => Order::generateOrderNumber(),
                    'order_type'       => 'custom',
                    'event_type'       => $request->event_type,
                    'event_date'       => $request->event_date,
                    'event_time'       => $request->event_time,
                    'delivery_address' => $request->delivery_address,
                    'delivery_city'    => $request->delivery_city,
                    'num_people'       => $numPeople,
                    'subtotal'         => $subtotal,
                    'tax'              => $tax,
                    'delivery_fee'     => $deliveryFee,
                    'discount'         => $discount,
                    'total_amount'     => $totalAmount,
                    'special_request'  => $request->special_request,
                ]);

                // Increment used_count promo di dalam transaksi
                if ($promoId) {
                    Promo::where('promo_id', $promoId)->increment('used_count');
                }

                foreach ($cart->items as $item) {
                    $unitPrice = $item->menuItem ? $item->menuItem->price : ($item->package ? $item->package->price_per_person : 0);
                    $itemName = $item->menuItem ? $item->menuItem->item_name : ($item->package ? $item->package->package_name : '');
                    // BUG FIX: Multiply quantity by num_people
                    $adjustedQty = $item->quantity * $numPeople;

                    OrderItem::create([
                        'order_id' => $order->order_id,
                        'item_id' => $item->item_id,
                        'package_id' => $item->package_id,
                        'item_name' => $itemName,
                        'quantity' => $adjustedQty,
                        'unit_price' => $unitPrice,
                        'subtotal' => $unitPrice * $adjustedQty,
                        'notes' => $item->notes,
                    ]);
                }

                $order->payments()->create([
                    'payment_method' => $request->payment_method,
                    'payment_provider' => $request->payment_provider,
                    'amount' => $totalAmount,
                ]);

                $cart->items()->delete();
                $cart->delete();

                $createdOrder = $order;
            });
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat memproses pesanan. Silakan coba lagi.')->withInput();
        }

        // Redirect to receipt page after successful checkout
        return redirect()->route('orders.receipt', $createdOrder->order_id)->with('success', 'Pesanan berhasil dibuat!');
    }
}
