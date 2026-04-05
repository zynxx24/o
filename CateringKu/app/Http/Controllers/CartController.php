<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $carts = Cart::where('user_id', $request->user()->id)
            ->with(['vendor', 'items.menuItem', 'items.package'])
            ->get();

        $totalAmount = 0;
        foreach ($carts as $cart) {
            foreach ($cart->items as $item) {
                if ($item->menuItem) {
                    $totalAmount += $item->menuItem->price * $item->quantity;
                } elseif ($item->package) {
                    $totalAmount += $item->package->price_per_person * $item->quantity;
                }
            }
        }

        return Inertia::render('Cart', [
            'carts' => $carts,
            'totalAmount' => $totalAmount,
        ]);
    }

    public function addItem(Request $request)
    {
        $request->validate([
            'vendor_id' => 'required|exists:vendors,vendor_id',
            'item_id' => 'nullable|exists:menu_items,item_id',
            'package_id' => 'nullable|exists:packages,package_id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:255',
        ]);

        $cart = Cart::firstOrCreate([
            'user_id' => $request->user()->id,
            'vendor_id' => $request->vendor_id,
        ]);

        $existingItem = CartItem::where('cart_id', $cart->cart_id)
            ->where(function ($q) use ($request) {
                if ($request->item_id) $q->where('item_id', $request->item_id);
                if ($request->package_id) $q->where('package_id', $request->package_id);
            })
            ->first();

        if ($existingItem) {
            $existingItem->update(['quantity' => $existingItem->quantity + $request->quantity]);
        } else {
            CartItem::create([
                'cart_id' => $cart->cart_id,
                'item_id' => $request->item_id,
                'package_id' => $request->package_id,
                'quantity' => $request->quantity,
                'notes' => $request->notes,
            ]);
        }

        return back()->with('success', 'Item ditambahkan ke keranjang!');
    }

    public function updateQuantity(Request $request, int $cartItemId)
    {
        $request->validate(['quantity' => 'required|integer|min:0']);

        $cartItem = CartItem::findOrFail($cartItemId);
        $cart = Cart::findOrFail($cartItem->cart_id);
        abort_unless($cart->user_id === $request->user()->id, 403);

        if ($request->quantity <= 0) {
            $cartItem->delete();
        } else {
            $cartItem->update(['quantity' => $request->quantity]);
        }

        // Clean up empty carts
        if ($cart->items()->count() === 0) {
            $cart->delete();
        }

        return back();
    }

    public function removeItem(Request $request, int $cartItemId)
    {
        $cartItem = CartItem::findOrFail($cartItemId);
        $cart = Cart::findOrFail($cartItem->cart_id);
        abort_unless($cart->user_id === $request->user()->id, 403);

        $cartItem->delete();

        if ($cart->items()->count() === 0) {
            $cart->delete();
        }

        return back()->with('success', 'Item dihapus dari keranjang.');
    }
}
