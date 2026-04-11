<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PromoController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\VendorDashboardController;
use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Middleware\EnsureUserIsVendor;
use App\Http\Middleware\ShareCartCount;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::middleware(ShareCartCount::class)->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/search', [VendorController::class, 'index'])->name('search');
    Route::get('/vendor/{id}', [VendorController::class, 'show'])->name('vendor.show');
    Route::get('/about', [AboutController::class, 'index'])->name('about');
    Route::get('/contact', [ContactController::class, 'index'])->name('contact');
    Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
    Route::get('/terms', fn () => \Inertia\Inertia::render('Terms'))->name('terms');
    Route::get('/privacy', fn () => \Inertia\Inertia::render('Privacy'))->name('privacy');

    /*
    |--------------------------------------------------------------------------
    | Authenticated Customer Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth', 'verified'])->group(function () {
        // Cart
        Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
        Route::post('/cart/add', [CartController::class, 'addItem'])->name('cart.add');
        Route::patch('/cart/{cartItemId}', [CartController::class, 'updateQuantity'])->name('cart.update');
        Route::delete('/cart/{cartItemId}', [CartController::class, 'removeItem'])->name('cart.remove');

        // Checkout
        Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
        Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

        // Orders
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
        Route::get('/orders/{id}/receipt', [OrderController::class, 'receipt'])->name('orders.receipt');
        Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
        Route::post('/orders/{id}/review', [OrderController::class, 'storeReview'])->name('orders.review');

        // Profile (public-facing)
        Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');

        // Promo validation (API)
        Route::post('/api/promo/validate', [PromoController::class, 'validate_'])->name('promo.validate');

        // Dashboard redirect based on role
        Route::get('/dashboard', function () {
            $user = auth()->user();
            if ($user->isAdmin()) return redirect()->route('admin.dashboard');
            if ($user->isVendor()) return redirect()->route('vendor.dashboard');
            return redirect()->route('orders.index');
        })->name('dashboard');
    });

    /*
    |--------------------------------------------------------------------------
    | Admin Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth', EnsureUserIsAdmin::class])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/orders', [AdminController::class, 'orders'])->name('orders');
        Route::get('/orders/{id}', [AdminController::class, 'orderDetail'])->name('orders.show');
        Route::patch('/orders/{id}/status', [AdminController::class, 'updateOrderStatus'])->name('orders.status');
        Route::patch('/payments/{id}/verify', [AdminController::class, 'verifyPayment'])->name('payments.verify');
        Route::get('/messages', [AdminController::class, 'messages'])->name('messages');
        Route::get('/messages/{id}', [AdminController::class, 'messageDetail'])->name('messages.show');
    });

    /*
    |--------------------------------------------------------------------------
    | Vendor Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth', EnsureUserIsVendor::class])->prefix('vendor-panel')->name('vendor.')->group(function () {
        Route::get('/', [VendorDashboardController::class, 'dashboard'])->name('dashboard');
        Route::get('/orders', [VendorDashboardController::class, 'orders'])->name('orders');
        Route::get('/orders/{id}', [VendorDashboardController::class, 'orderDetail'])->name('orders.show');
        Route::patch('/orders/{id}/status', [VendorDashboardController::class, 'updateOrderStatus'])->name('orders.status');
        Route::get('/menu', [VendorDashboardController::class, 'menu'])->name('menu');
        Route::post('/menu', [VendorDashboardController::class, 'storeMenuItem'])->name('menu.store');
        Route::patch('/menu/{id}', [VendorDashboardController::class, 'updateMenuItem'])->name('menu.update');
        Route::delete('/menu/{id}', [VendorDashboardController::class, 'deleteMenuItem'])->name('menu.destroy');
        Route::get('/reviews', [VendorDashboardController::class, 'reviews'])->name('reviews');
        Route::post('/reviews/{id}/respond', [VendorDashboardController::class, 'respondReview'])->name('reviews.respond');
    });
});

require __DIR__.'/settings.php';
