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
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    orange: '#F97316',
                    'orange-dark': '#EA580C',
                    'orange-light': '#FFF7ED',
                    black: '#0A0A0A',
                    'black-soft': '#18181B',
                    surface: '#FAFAFA',
                    muted: '#71717A',
                    border: '#E4E4E7',
                },
            },
            boxShadow: {
                card: '0 1px 3px 0 rgb(0 0 0 / 0.06), 0 1px 2px -1px rgb(0 0 0 / 0.06)',
                'card-hover': '0 4px 12px 0 rgb(0 0 0 / 0.08)',
            },
        },
    },

    plugins: [forms],
};
