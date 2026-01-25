<?php
use App\Config\Session;
use App\Middleware\Security;
use App\Domain\ContactRepository;

include 'layout/header.php';
$success = Session::flash('success');
$error = Session::flash('error');
?>

<div class="max-w-6xl mx-auto py-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center">
                <span class="w-1 h-8 bg-gradient-to-b from-orange-500 to-red-500 rounded-full mr-3"></span>
                Pesan Masuk (CS)
            </h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">
                Kelola pesan dan pertanyaan dari pelanggan
            </p>
        </div>
        <div class="flex gap-3">
            <a href="/admin"
                class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-medium hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
            </a>
            <?php if ($unreadCount > 0): ?>
                <form action="/admin/messages/mark-all-read" method="POST" class="inline">
                    <?php echo Security::csrfField(); ?>
                    <button type="submit"
                        class="px-4 py-2 bg-green-500 text-white rounded-xl font-medium hover:bg-green-600 transition-colors flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Tandai Semua Dibaca
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($success): ?>
        <div
            class="mb-6 p-4 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-xl text-green-600 dark:text-green-400 backdrop-blur-sm">
            <?php echo Security::e($success); ?>
        </div>
    <?php endif; ?>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div
            class="bg-white dark:bg-gray-800/80 rounded-2xl p-5 text-center border border-gray-100 dark:border-gray-700 backdrop-blur-sm">
            <div class="text-3xl font-bold gradient-text">
                <?php echo count($messages); ?>
            </div>
            <div class="text-gray-500 dark:text-gray-400 text-sm">Total Pesan</div>
        </div>
        <div
            class="bg-white dark:bg-gray-800/80 rounded-2xl p-5 text-center border border-gray-100 dark:border-gray-700 backdrop-blur-sm">
            <div class="text-3xl font-bold text-red-500">
                <?php echo $unreadCount; ?>
            </div>
            <div class="text-gray-500 dark:text-gray-400 text-sm">Belum Dibaca</div>
        </div>
        <div
            class="bg-white dark:bg-gray-800/80 rounded-2xl p-5 text-center border border-gray-100 dark:border-gray-700 backdrop-blur-sm">
            <div class="text-3xl font-bold text-green-500">
                <?php echo count($messages) - $unreadCount; ?>
            </div>
            <div class="text-gray-500 dark:text-gray-400 text-sm">Sudah Dibaca</div>
        </div>
        <div
            class="bg-white dark:bg-gray-800/80 rounded-2xl p-5 text-center border border-gray-100 dark:border-gray-700 backdrop-blur-sm">
            <div class="text-3xl font-bold text-blue-500">
                <?php
                $today = count(array_filter($messages, fn($m) => date('Y-m-d', strtotime($m['created_at'])) === date('Y-m-d')));
                echo $today;
                ?>
            </div>
            <div class="text-gray-500 dark:text-gray-400 text-sm">Hari Ini</div>
        </div>
    </div>

    <!-- Messages List -->
    <?php if (empty($messages)): ?>
        <div
            class="bg-white dark:bg-gray-800/80 rounded-2xl shadow-sm dark:shadow-xl border border-gray-100 dark:border-gray-700 p-12 text-center backdrop-blur-sm">
            <div
                class="w-20 h-20 mx-auto bg-gradient-to-br from-orange-100 to-orange-200 dark:from-orange-900/50 dark:to-orange-800/50 rounded-2xl flex items-center justify-center mb-4">
                <svg class="w-10 h-10 text-orange-500 dark:text-orange-400" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                    </path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Tidak Ada Pesan</h3>
            <p class="text-gray-500 dark:text-gray-400">Belum ada pesan masuk dari pelanggan.</p>
        </div>
    <?php else: ?>
        <div
            class="bg-white dark:bg-gray-800/80 rounded-2xl shadow-sm dark:shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden backdrop-blur-sm">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-100 dark:border-gray-700">
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Status
                            </th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Pengirim
                            </th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Subjek
                            </th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Pesan
                            </th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Tanggal
                            </th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700 dark:text-gray-300">Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <?php foreach ($messages as $msg): ?>
                            <tr
                                class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors <?php echo !$msg['is_read'] ? 'bg-orange-50/50 dark:bg-orange-900/10' : ''; ?>">
                                <td class="px-6 py-4">
                                    <?php if (!$msg['is_read']): ?>
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400">
                                            <span class="w-2 h-2 bg-red-500 rounded-full mr-1.5 animate-pulse"></span>
                                            Baru
                                        </span>
                                    <?php else: ?>
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Dibaca
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 bg-gradient-to-br from-orange-400 to-red-500 rounded-xl flex items-center justify-center text-white font-bold">
                                            <?php echo strtoupper(substr($msg['name'], 0, 1)); ?>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900 dark:text-white text-sm">
                                                <?php echo Security::e($msg['name']); ?>
                                            </p>
                                            <p class="text-gray-500 dark:text-gray-400 text-xs">
                                                <?php echo Security::e($msg['email']); ?>
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                                        <?php echo ContactRepository::getSubjectLabel($msg['subject']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 max-w-xs">
                                    <p class="text-gray-600 dark:text-gray-400 text-sm truncate">
                                        <?php echo Security::e(substr($msg['message'], 0, 60)); ?>
                                        <?php echo strlen($msg['message']) > 60 ? '...' : ''; ?>
                                    </p>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        <?php echo date('d M Y', strtotime($msg['created_at'])); ?>
                                        <br>
                                        <span class="text-xs">
                                            <?php echo date('H:i', strtotime($msg['created_at'])); ?> WIB
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="/admin/messages/<?php echo $msg['message_id']; ?>"
                                            class="px-3 py-1.5 bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 rounded-lg text-sm font-medium hover:bg-orange-200 dark:hover:bg-orange-900/50 transition-colors">
                                            Lihat
                                        </a>
                                        <form action="/admin/messages/<?php echo $msg['message_id']; ?>/delete" method="POST"
                                            class="inline" onsubmit="return confirm('Yakin ingin menghapus pesan ini?')">
                                            <?php echo Security::csrfField(); ?>
                                            <button type="submit"
                                                class="px-3 py-1.5 bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-lg text-sm font-medium hover:bg-red-200 dark:hover:bg-red-900/50 transition-colors">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'layout/footer.php'; ?>