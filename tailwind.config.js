import defaultTheme from 'tailwindcss/defaultTheme';
const plugin = require('tailwindcss/plugin');
const tailwindScrollbar = require('tailwind-scrollbar');
/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
        './resources/**/*.js', 
        './resources/**/*.html', 
        // './resources/**/*.jsx',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },
    plugins: [
        tailwindScrollbar,
    ],
    variants: {
        scrollbar: ['rounded', 'hover'], // Aktifkan varian yang diperlukan
      },
};