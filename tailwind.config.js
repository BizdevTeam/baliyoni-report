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
                sans2: ['Roboto', 'sans-serif'],
                serif: ['Georgia', 'serif'],
                inter: ['Inter', 'sans-serif'],
                lato: ['Lato', 'sans-serif'],
                montserrat: ['Montserrat', 'sans-serif'],
                playfair: ['Playfair Display', 'serif'],
                pacifico: ['Pacifico', 'cursive'],
                lobster: ['Lobster', 'cursive'],
                greatVibes: ['Great Vibes', 'cursive'],
                archivoBlack: ['Archivo Black', 'sans-serif'],
            },
        },
    },
    plugins: [
    ],
    variants: {
        extend: {
            scrollbar: ['rounded', 'hover'], // Add your custom variants
        },
    },
};
    module.exports = {
        content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
        "./node_modules/flowbite/**/*.js"
        ],
        theme: {
        extend: {},
        },
        plugins: [
            require('flowbite/plugin')
        ],
    }
