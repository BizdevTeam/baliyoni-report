import defaultTheme from 'tailwindcss/defaultTheme';
const plugin = require('tailwind-scrollbar');
/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
        './resources/**/*.jsx',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },
    plugins: [
        plugin({ nocompatible: true }), // Tambahkan ini
    ],
    variants: {
        scrollbar: ['rounded', 'hover'], // Aktifkan varian yang diperlukan
    },
};