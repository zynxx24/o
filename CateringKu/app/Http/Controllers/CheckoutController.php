<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
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
        $request->validate([
            'vendor_id' => 'required|exists:vendors,vendor_id',
            'event_date' => 'required|date|after:today',
            'event_time' => 'required',
            'delivery_address' => 'required|string|max:500',
            'delivery_city' => 'nullable|string|max:50',
            'num_people' => 'required|integer|min:1',
            'event_type' => 'nullable|string|max:50',
            'special_request' => 'nullable|string|max:1000',
            'payment_method' => 'required|in:transfer,credit_card,e-wallet,cash,cod',
        ]);

        $cart = Cart::where('user_id', $request->user()->id)
            ->where('vendor_id', $request->vendor_id)
            ->with(['items.menuItem', 'items.package'])
            ->firstOrFail();

        $subtotal = 0;
        foreach ($cart->items as $item) {
            if ($item->menuItem) {
                $subtotal += $item->menuItem->price * $item->quantity;
            } elseif ($item->package) {
                $subtotal += $item->package->price_per_person * $item->quantity;
            }
        }

        $tax = round($subtotal * 0.11, 2);
        $deliveryFee = 15000;
        $totalAmount = $subtotal + $tax + $deliveryFee;

        DB::transaction(function () use ($request, $cart, $subtotal, $tax, $deliveryFee, $totalAmount) {
            $order = Order::create([
                'user_id' => $request->user()->id,
                'vendor_id' => $request->vendor_id,
                'order_number' => Order::generateOrderNumber(),
                'order_type' => 'custom',
                'event_type' => $request->event_type,
                'event_date' => $request->event_date,
                'event_time' => $request->event_time,
                'delivery_address' => $request->delivery_address,
                'delivery_city' => $request->delivery_city,
                'num_people' => $request->num_people,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'delivery_fee' => $deliveryFee,
                'total_amount' => $totalAmount,
                'special_request' => $request->special_request,
            ]);

            foreach ($cart->items as $item) {
                $unitPrice = $item->menuItem ? $item->menuItem->price : ($item->package ? $item->package->price_per_person : 0);
                $itemName = $item->menuItem ? $item->menuItem->item_name : ($item->package ? $item->package->package_name : '');
                OrderItem::create([
                    'order_id' => $order->order_id,
                    'item_id' => $item->item_id,
                    'package_id' => $item->package_id,
                    'item_name' => $itemName,
                    'quantity' => $item->quantity,
                    'unit_price' => $unitPrice,
                    'subtotal' => $unitPrice * $item->quantity,
                    'notes' => $item->notes,
                ]);
            }

            $order->payments()->create([
                'payment_method' => $request->payment_method,
                'amount' => $totalAmount,
            ]);

            $cart->items()->delete();
            $cart->delete();
        });

        return redirect()->route('orders.index')->with('success', 'Pesanan berhasil dibuat!');
    }
}
