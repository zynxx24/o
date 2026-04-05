import { createInertiaApp } from '@inertiajs/vue3';
import { initializeTheme } from '@/composables/useAppearance';
import AppLayout from '@/layouts/AppLayout.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';

const appName = import.meta.env.VITE_APP_NAME || 'CateringKu';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    layout: (name: string) => {
        // Auth pages → AuthLayout (centered card)
        if (name.startsWith('auth/')) {
            return AuthLayout;
        }
        // Settings pages → AppLayout (sidebar + content)
        if (name.startsWith('settings/')) {
            return AppLayout;
        }
        // All other pages (Home, Vendor, Admin, Dashboard, etc.)
        // have their own navigation built-in — no layout wrapper needed
        return null;
    },
    progress: {
        color: '#F97316',
    },
});

// This will set light / dark mode on page load...
initializeTheme();
