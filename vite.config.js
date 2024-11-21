import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js', 'resources/css/tailwind.css',  'public/templates/dist/css/adminlte.css','resources/css/custom.css','resources/js/sidebar.js'],  
            refresh: true,
        }),
    ],
});
