import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['DM Sans', ...defaultTheme.fontFamily.sans],
                mono: ['DM Mono', ...defaultTheme.fontFamily.mono],
                serif: ['DM Serif Display', ...defaultTheme.fontFamily.serif],
            },
            colors: {
                ink: {
                    DEFAULT: '#1a1814',
                    dim: '#2e2b27',
                    soft: '#6b6358',
                },
                cream: {
                    DEFAULT: '#f5f0e8',
                    2: '#ede6d6',
                    3: '#e3dac8',
                },
                border: '#d5ccba',
                amber: {
                    DEFAULT: '#d4820a',
                    lt: '#f0a832',
                    bg: '#fdf3e0',
                },
                emerald: {
                    DEFAULT: '#2d7a4f',
                    lt: '#e6f4ed',
                },
                ruby: {
                    DEFAULT: '#b83232',
                    lt: '#fdeaea',
                },
                sapphire: {
                    DEFAULT: '#2a5f9e',
                    lt: '#e8f0fa',
                },
            },
        },
    },

    plugins: [forms],
};
