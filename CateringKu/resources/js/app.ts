import { createInertiaApp } from '@inertiajs/vue3';
import { initializeTheme } from '@/composables/useAppearance';
import AppLayout from '@/layouts/AppLayout.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';

const appName = import.meta.env.VITE_APP_NAME || 'CateringKu';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name: string) => {
        const pages = import.meta.glob('./pages/**/*.vue', { eager: true }) as Record<string, any>;
        const page = pages[`./pages/${name}.vue`];

        // If the page component explicitly sets layout (including null), respect it
        if (page.default.layout !== undefined) {
            return page;
        }

        // Auto-assign layouts based on path for pages that don't specify one
        if (name.startsWith('auth/')) {
            page.default.layout = AuthLayout;
        } else if (name.startsWith('settings/')) {
            page.default.layout = AppLayout;
        }
        // All other pages have their own navigation built-in — no layout wrapper needed

        return page;
    },
    progress: {
        color: '#F97316',
    },
});

// This will set light / dark mode on page load...
initializeTheme();
