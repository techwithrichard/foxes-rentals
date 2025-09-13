import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    build: {
        // Optimize build performance
        minify: 'esbuild',
        // Enable CSS code splitting
        cssCodeSplit: true,
        // Optimize chunk size
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ['alpinejs', 'axios'],
                },
            },
        },
        // Enable source maps for production debugging
        sourcemap: false,
        // Optimize asset handling
        assetsInlineLimit: 4096,
    },
    // Development server optimizations
    server: {
        hmr: {
            host: 'localhost',
        },
    },
});
