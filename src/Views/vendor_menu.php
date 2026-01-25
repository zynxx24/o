<?php
include 'layout/header.php';
?>

<main class="container mx-auto px-4 py-8">
    <div class="mb-6 flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Kelola Menu</h1>
            <p class="text-gray-500">Tambah, edit, atau hapus item menu Anda</p>
        </div>
        <div class="flex gap-2">
            <a href="/vendor-dashboard"
                class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition">
                &larr; Kembali
            </a>
            <button onclick="openModal('add')"
                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Menu Baru
            </button>
        </div>
    </div>

    <!-- Menu Items Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <?php foreach ($menuItems as $item): ?>
            <div
                class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden group hover:shadow-md transition">
                <div class="aspect-video bg-gray-100 relative overflow-hidden">
                    <img src="<?php echo htmlspecialchars($item['image_url'] ?? '/assets/images/default-food.jpg'); ?>"
                        alt="<?php echo htmlspecialchars($item['item_name']); ?>"
                        class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                    <div class="absolute top-2 right-2">
                        <span
                            class="px-2 py-1 bg-white/90 backdrop-blur rounded-lg text-xs font-bold <?php echo $item['is_available'] ? 'text-green-600' : 'text-red-500'; ?>">
                            <?php echo $item['is_available'] ? 'Tersedia' : 'Habis'; ?>
                        </span>
                    </div>
                </div>
                <div class="p-4">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="font-bold text-gray-900 line-clamp-1">
                            <?php echo htmlspecialchars($item['item_name']); ?>
                        </h3>
                        <p class="font-bold text-orange-600">
                            Rp
                            <?php echo number_format($item['price'] / 1000, 0); ?>k
                        </p>
                    </div>
                    <p class="text-sm text-gray-500 line-clamp-2 mb-4 h-10">
                        <?php echo htmlspecialchars($item['description']); ?>
                    </p>

                    <div class="flex gap-2 pt-2 border-t border-gray-50">
                        <button onclick='editItem(<?php echo json_encode($item); ?>)'
                            class="flex-1 px-3 py-2 bg-blue-50 text-blue-600 rounded-lg text-sm font-medium hover:bg-blue-100 transition text-center">
                            Edit
                        </button>
                        <form action="/vendor/menu/<?php echo $item['item_id']; ?>/delete" method="POST" class="flex-1"
                            onsubmit="return confirm('Yakin hapus menu ini?')">
                            <?php echo App\Middleware\Security::csrfField(); ?>
                            <button type="submit"
                                class="w-full px-3 py-2 bg-red-50 text-red-600 rounded-lg text-sm font-medium hover:bg-red-100 transition">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</main>

<!-- Modal -->
<div id="menuModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4 backdrop-blur-sm">
    <div class="bg-white rounded-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto shadow-2xl transform transition-all scale-95 opacity-0"
        id="modalContent">
        <div class="p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4" id="modalTitle">Tambah Menu Baru</h2>

            <form id="menuForm" action="/vendor/menu/add" method="POST" enctype="multipart/form-data" class="space-y-4">
                <?php echo App\Middleware\Security::csrfField(); ?>
                <input type="hidden" name="item_id" id="itemId">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Menu</label>
                    <input type="text" name="item_name" id="itemName" required
                        class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                    <textarea name="description" id="itemDesc" rows="3"
                        class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Harga</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-gray-500">Rp</span>
                            <input type="number" name="price" id="itemPrice" required
                                class="w-full pl-10 rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                        <select name="category_id" id="itemCategory"
                            class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['category_id']; ?>">
                                    <?php echo htmlspecialchars($cat['category_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Waktu Persiapan (menit)</label>
                        <input type="number" name="preparation_time" id="itemPrepTime" value="15"
                            class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="is_available" id="itemStatus"
                            class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                            <option value="1">Tersedia</option>
                            <option value="0">Habis</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Foto Menu (Optional)</label>
                    <input type="file" name="image" accept="image/*"
                        class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100 mt-6">
                    <button type="button" onclick="closeModal()"
                        class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg font-medium transition">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium transition shadow-lg shadow-green-600/20">
                        Simpan Menu
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const modal = document.getElementById('menuModal');
    const modalContent = document.getElementById('modalContent');
    const form = document.getElementById('menuForm');
    const title = document.getElementById('modalTitle');

    function openModal(mode, data = null) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => {
            modalContent.classList.remove('scale-95', 'opacity-0');
            modalContent.classList.add('scale-100', 'opacity-100');
        }, 10);

        if (mode === 'edit' && data) {
            title.textContent = 'Edit Menu';
            form.action = `/vendor/menu/${data.item_id}/update`;
            document.getElementById('itemId').value = data.item_id;
            document.getElementById('itemName').value = data.item_name;
            document.getElementById('itemDesc').value = data.description;
            document.getElementById('itemPrice').value = data.price;
            document.getElementById('itemCategory').value = data.category_id;
            document.getElementById('itemPrepTime').value = data.preparation_time;
            document.getElementById('itemStatus').value = data.is_available ? '1' : '0';
        } else {
            title.textContent = 'Tambah Menu Baru';
            form.action = '/vendor/menu/add';
            form.reset();
            document.getElementById('itemId').value = '';
        }
    }

    function closeModal() {
        modalContent.classList.remove('scale-100', 'opacity-100');
        modalContent.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }, 300);
    }

    function editItem(data) {
        openModal('edit', data);
    }

    // Close on click outside
    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
    });
</script>

<?php include 'layout/footer.php'; ?>