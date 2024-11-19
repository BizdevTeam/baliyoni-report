import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from 'tailwindcss';
import autoprefixer from 'autoprefixer';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/App.jsx',
                'resources/css/animated-text.css',
            ],
            refresh: true,
        }),
       
    ],
    css: {
        postcss: {
            plugins: [
                tailwindcss('./tailwind.config.js'), // Pastikan file ini ada dan benar
                autoprefixer(),
            ],
        },
    },
});
