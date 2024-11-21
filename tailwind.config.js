import defaultTheme from 'tailwindcss/defaultTheme';
const plugin = require('tailwindcss/plugin');

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
        './resources/**/*.html', // Ensure your HTML files are scanned
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
        extend: {
            scrollbar: ['rounded', 'hover'], // Add your custom variants
        },
    },
};
