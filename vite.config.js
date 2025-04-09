import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    build: {
        // Enable minification and optimization
        minify: 'terser',
        // Configure code splitting
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ['alpinejs', 'axios', 'pusher-js', 'laravel-echo'],
                },
            },
        },
        // Enable CSS code splitting
        cssCodeSplit: true,
        // Enable source maps for production debugging if needed
        sourcemap: false,
        // Improve build performance
        chunkSizeWarningLimit: 1000,
    },
    // Cache optimization
    server: {
        hmr: {
            overlay: false,
        },
        watch: {
            usePolling: false,
        },
    },
});
