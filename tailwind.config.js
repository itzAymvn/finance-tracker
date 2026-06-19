import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                mono: ['JetBrains Mono', ...defaultTheme.fontFamily.mono],
            },
            colors: {
                surface: {
                    DEFAULT: '#f8fafc',
                    card: '#ffffff',
                    sidebar: '#0f172a',
                    hover: '#f1f5f9',
                    dark: '#0b1120',
                    'dark-card': '#131b2e',
                    'dark-hover': '#1e293b',
                },
                primary: {
                    DEFAULT: '#6366f1',
                    hover: '#4f46e5',
                    light: '#eef2ff',
                    subtle: '#a5b4fc',
                    400: '#818cf8',
                },
                ink: {
                    DEFAULT: '#0f172a',
                    dim: '#334155',
                    soft: '#64748b',
                    dark: '#f1f5f9',
                    'dark-dim': '#94a3b8',
                    'dark-soft': '#64748b',
                },
                border: '#e2e8f0',
                'border-dark': '#1e293b',
                emerald: {
                    DEFAULT: '#059669',
                    300: '#6ee7b7',
                    400: '#34d399',
                    500: '#10b981',
                    lt: '#ecfdf5',
                    'dark-bg': '#052e16',
                },
                ruby: {
                    DEFAULT: '#dc2626',
                    300: '#fca5a5',
                    400: '#f87171',
                    500: '#ef4444',
                    lt: '#fef2f2',
                    'dark-bg': '#450a0a',
                },
                amber: {
                    DEFAULT: '#d97706',
                    300: '#fcd34d',
                    400: '#fbbf24',
                    500: '#f59e0b',
                    lt: '#fffbeb',
                    'dark-bg': '#451a03',
                },
                sapphire: {
                    DEFAULT: '#2563eb',
                    300: '#93c5fd',
                    400: '#60a5fa',
                    500: '#3b82f6',
                    lt: '#eff6ff',
                    'dark-bg': '#172554',
                },
            },
        },
    },

    plugins: [forms],
};
