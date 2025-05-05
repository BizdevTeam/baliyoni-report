// vite.config.js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/tailwind.css',
                'resources/css/custom.css',
                'resources/js/app.js'
            ],
            refresh: true,
        }),
    ],
    // Optimize CSS loading
    css: {
        devSourcemap: true,
    },
    // Optimize build
    build: {
        // Generate CSS as separate files for faster loading
        cssCodeSplit: true,
        // Minify output
        minify: 'terser',
        // Improve chunking strategy
        rollupOptions: {
            output: {
                manualChunks: {
                    // Split vendor code from application code
                    vendor: ['chart.js', 'flowbite']
                }
            }
        }
    }
});