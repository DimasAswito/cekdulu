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
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Deep forest green — primary brand color, bolder and more saturated
                // than a stock Tailwind green so it reads as confident rather than "safe".
                brand: {
                    50: '#f0f7f1',
                    100: '#dcecdf',
                    200: '#b9d9c0',
                    300: '#8cbf98',
                    400: '#5c9e6c',
                    500: '#3c7f4c',
                    600: '#2b6539',
                    700: '#22502e',
                    800: '#1c4025',
                    900: '#17331f',
                    950: '#0b1c11',
                },
                // Vivid lime — reserved for high-contrast CTAs, highlights, and accents
                // against the dark forest tones.
                accent: {
                    50: '#f7fbe7',
                    100: '#edf6c4',
                    200: '#dced93',
                    300: '#c7de5c',
                    400: '#b0cc32',
                    500: '#93ac22',
                    600: '#74861b',
                    700: '#59661a',
                    800: '#49521c',
                    900: '#3f461e',
                    950: '#1f2510',
                },
            },
        },
    },

    plugins: [forms],
};
