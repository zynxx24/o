<?php
use App\Config\Session;
use App\Middleware\Security;

include 'layout/header.php';
$error = Session::flash('error');
?>

<div class="max-w-6xl mx-auto py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Checkout</h1>
        <p class="text-gray-600 mt-2">Lengkapi data pemesanan Anda</p>
    </div>

    <?php if ($error): ?>
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-red-600">
            <?php echo Security::e($error); ?>
        </div>
    <?php endif; ?>

    <?php if (empty($cartItems)): ?>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
            <p class="text-gray-500 mb-4">Keranjang Anda kosong. Silakan tambah menu terlebih dahulu.</p>
            <a href="/" class="text-orange-600 hover:text-orange-700 font-medium">Kembali ke Beranda</a>
        </div>
    <?php else: ?>
        <form action="/order/create" method="POST">
            <?php echo Security::csrfField(); ?>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Form Section -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Event Details -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                            <span
                                class="w-8 h-8 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center text-sm font-bold mr-3">1</span>
                            Detail Acara
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Acara</label>
                                <select name="event_type" required
                                    class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">
                                    <option value="">Pilih jenis acara</option>
                                    <option value="Pernikahan">Pernikahan</option>
                                    <option value="Ulang Tahun">Ulang Tahun</option>
                                    <option value="Meeting Kantor">Meeting Kantor</option>
                                    <option value="Seminar">Seminar</option>
                                    <option value="Arisan">Arisan</option>
                                    <option value="Syukuran">Syukuran</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah Tamu</label>
                                <input type="number" name="num_people" required min="1"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition"
                                    placeholder="Jumlah orang">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Acara</label>
                                <input type="date" name="event_date" required
                                    min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Waktu Acara</label>
                                <input type="time" name="event_time" required
                                    class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">
                            </div>
                        </div>
                    </div>

                    <!-- Delivery Address -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                            <span
                                class="w-8 h-8 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center text-sm font-bold mr-3">2</span>
                            Alamat Pengiriman
                        </h3>

                        <div class="space-y-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Alamat Lengkap</label>
                                <textarea name="delivery_address" required rows="3"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition"
                                    placeholder="Nama gedung/tempat, jalan, nomor, RT/RW"><?php echo Security::e($user['address'] ?? ''); ?></textarea>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Kota</label>
                                    <input type="text" name="delivery_city" required
                                        class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition"
                                        placeholder="Nama kota">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">No. Telepon Penerima</label>
                                    <input type="tel" name="phone" value="<?php echo Security::e($user['phone'] ?? ''); ?>"
                                        required
                                        class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition"
                                        placeholder="08xxxxxxxxxx">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Special Request -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                            <span
                                class="w-8 h-8 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center text-sm font-bold mr-3">3</span>
                            Catatan Khusus
                        </h3>

                        <textarea name="special_request" rows="3"
                            class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition"
                            placeholder="Permintaan khusus, alergi makanan, dekorasi, dll (opsional)"></textarea>
                    </div>

                    <!-- Payment Method -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                            <span
                                class="w-8 h-8 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center text-sm font-bold mr-3">4</span>
                            Metode Pembayaran
                        </h3>

                        <div class="space-y-3">
                            <label
                                class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:border-orange-300 transition has-[:checked]:border-orange-500 has-[:checked]:bg-orange-50">
                                <input type="radio" name="payment_method" value="transfer" required
                                    class="w-5 h-5 text-orange-600">
                                <div class="ml-4">
                                    <span class="font-medium text-gray-900">Transfer Bank</span>
                                    <p class="text-sm text-gray-500">BCA, Mandiri, BNI, BRI</p>
                                </div>
                            </label>
                            <label
                                class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:border-orange-300 transition has-[:checked]:border-orange-500 has-[:checked]:bg-orange-50">
                                <input type="radio" name="payment_method" value="e-wallet" class="w-5 h-5 text-orange-600">
                                <div class="ml-4">
                                    <span class="font-medium text-gray-900">E-Wallet</span>
                                    <p class="text-sm text-gray-500">GoPay, OVO, DANA, ShopeePay</p>
                                </div>
                            </label>
                            <label
                                class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:border-orange-300 transition has-[:checked]:border-orange-500 has-[:checked]:bg-orange-50">
                                <input type="radio" name="payment_method" value="cod" class="w-5 h-5 text-orange-600">
                                <div class="ml-4">
                                    <span class="font-medium text-gray-900">Bayar di Tempat (COD)</span>
                                    <p class="text-sm text-gray-500">Bayar saat makanan diterima</p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-24">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Ringkasan Pesanan</h3>

                        <!-- Items -->
                        <div class="space-y-3 max-h-64 overflow-y-auto mb-4">
                            <?php foreach ($cartItems as $item): ?>
                                <?php if ($item['cart_item_id']): ?>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">
                                            <?php echo Security::e($item['item_name'] ?? $item['package_name']); ?>
                                            <span class="text-gray-400">×
                                                <?php echo $item['quantity']; ?>
                                            </span>
                                        </span>
                                        <span class="font-medium">Rp
                                            <?php echo number_format(($item['price'] ?? $item['price_per_person']) * $item['quantity'], 0, ',', '.'); ?>
                                        </span>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>

                        <div class="border-t border-gray-100 pt-4 space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Subtotal</span>
                                <span class="font-semibold">Rp
                                    <?php echo number_format($cartTotal, 0, ',', '.'); ?>
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">PPN (10%)</span>
                                <span>Rp
                                    <?php echo number_format($cartTotal * 0.1, 0, ',', '.'); ?>
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Biaya Pengiriman</span>
                                <span>Rp 50.000</span>
                            </div>
                        </div>

                        <div class="border-t border-gray-100 mt-4 pt-4">
                            <div class="flex justify-between text-lg font-bold">
                                <span>Total</span>
                                <span class="text-orange-600">Rp
                                    <?php echo number_format($cartTotal + ($cartTotal * 0.1) + 50000, 0, ',', '.'); ?>
                                </span>
                            </div>
                        </div>

                        <!-- Hidden fields for calculation -->
                        <input type="hidden" name="subtotal" value="<?php echo $cartTotal; ?>">
                        <input type="hidden" name="tax" value="<?php echo $cartTotal * 0.1; ?>">
                        <input type="hidden" name="delivery_fee" value="50000">
                        <input type="hidden" name="total_amount"
                            value="<?php echo $cartTotal + ($cartTotal * 0.1) + 50000; ?>">

                        <button type="submit"
                            class="w-full mt-6 bg-gradient-to-r from-orange-500 to-red-500 text-white py-4 px-4 rounded-lg font-semibold hover:from-orange-600 hover:to-red-600 transition transform hover:scale-[1.02] shadow-lg shadow-orange-500/25">
                            Buat Pesanan
                        </button>

                        <p class="text-xs text-gray-500 text-center mt-4">
                            Dengan memesan, Anda menyetujui <a href="#" class="text-orange-600">Syarat & Ketentuan</a> kami
                        </p>
                    </div>
                </div>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php include 'layout/footer.php'; ?>