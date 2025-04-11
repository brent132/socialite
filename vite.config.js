import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
        }),
    ],
    build: {
        // Optimize build for production
        minify: "terser",
        terserOptions: {
            compress: {
                drop_console: true,
                drop_debugger: true,
            },
        },
        // Split chunks for better caching
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ["alpinejs", "axios"],
                },
            },
        },
    },
});
