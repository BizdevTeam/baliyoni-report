import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/tailwind.css',
                'resources/css/custom.css',
                'resources/js/app.js'
              ],
            refresh: true,
        }),
    ],
    css: {
        devSourcemap: true,
    },
    build: {
        cssCodeSplit: true,
        minify: 'terser',
    },
    optimizeDeps: {
        include: ['chart.js', 'flowbite'],
    },
});
