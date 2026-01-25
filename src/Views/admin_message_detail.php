<?php
use App\Config\Session;
use App\Middleware\Security;
use App\Domain\ContactRepository;

include 'layout/header.php';
?>

<div class="max-w-4xl mx-auto py-8">
    <!-- Header -->
    <div class="flex items-center gap-4 mb-8">
        <a href="/admin/messages"
            class="w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded-xl flex items-center justify-center text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                </path>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Detail Pesan</h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm">Dari
                <?php echo Security::e($message['name']); ?>
            </p>
        </div>
    </div>

    <!-- Message Card -->
    <div
        class="bg-white dark:bg-gray-800/80 rounded-2xl shadow-sm dark:shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden backdrop-blur-sm">
        <!-- Header -->
        <div class="bg-gradient-to-r from-orange-500 via-red-500 to-pink-500 p-6 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-10 -mt-10"></div>
            <div class="relative z-10 flex items-start gap-4">
                <div
                    class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center text-2xl font-bold backdrop-blur-sm">
                    <?php echo strtoupper(substr($message['name'], 0, 1)); ?>
                </div>
                <div class="flex-1">
                    <h2 class="text-xl font-bold">
                        <?php echo Security::e($message['name']); ?>
                    </h2>
                    <p class="text-orange-100">
                        <?php echo Security::e($message['email']); ?>
                    </p>
                    <div class="flex items-center gap-4 mt-2 text-sm text-orange-100">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                            <?php echo date('d M Y, H:i', strtotime($message['created_at'])); ?> WIB
                        </span>
                        <span class="px-2 py-0.5 bg-white/20 rounded-full text-xs font-medium">
                            <?php echo ContactRepository::getSubjectLabel($message['subject']); ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Message Content -->
        <div class="p-6">
            <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Isi Pesan
            </h3>
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-5 border border-gray-100 dark:border-gray-600">
                <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap leading-relaxed">
                    <?php echo nl2br(Security::e($message['message'])); ?>
                </p>
            </div>
        </div>

        <!-- Actions -->
        <div class="px-6 pb-6 flex flex-wrap gap-3">
            <a href="mailto:<?php echo Security::e($message['email']); ?>?subject=Re: <?php echo Security::e(ContactRepository::getSubjectLabel($message['subject'])); ?>"
                class="btn-glow px-5 py-2.5 bg-gradient-to-r from-orange-500 to-red-500 text-white rounded-xl font-medium hover:from-orange-600 hover:to-red-600 transition-all flex items-center shadow-lg shadow-orange-500/25">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                    </path>
                </svg>
                Balas via Email
            </a>
            <form action="/admin/messages/<?php echo $message['message_id']; ?>/delete" method="POST" class="inline"
                onsubmit="return confirm('Yakin ingin menghapus pesan ini?')">
                <?php echo Security::csrfField(); ?>
                <button type="submit"
                    class="px-5 py-2.5 bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-xl font-medium hover:bg-red-200 dark:hover:bg-red-900/50 transition-colors flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                        </path>
                    </svg>
                    Hapus Pesan
                </button>
            </form>
        </div>
    </div>
</div>

<?php include 'layout/footer.php'; ?>