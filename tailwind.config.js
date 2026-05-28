import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],

    theme: {
        extend: {
            colors: {
                '3ao': {
                    'green-dark':  '#2D6A4F',
                    'green':       '#40916C',
                    'green-light': '#52B788',
                    'green-pale':  '#B7E4C7',
                    'ocre':        '#D4A017',
                    'ocre-light':  '#F4C842',
                    'cream':       '#F8F5F0',
                    'text':        '#1A1A2E',
                    'muted':       '#6B7280',
                },
            },
            fontFamily: {
                sans:    ['Inter', ...defaultTheme.fontFamily.sans],
                display: ['Poppins', ...defaultTheme.fontFamily.sans],
            },
            backgroundImage: {
                'hero-gradient': 'linear-gradient(135deg, #2D6A4F 0%, #40916C 50%, #52B788 100%)',
            },
            animation: {
                'count-up': 'countUp 2s ease-out forwards',
                'fade-in':  'fadeIn 0.6s ease-out forwards',
                'slide-up': 'slideUp 0.5s ease-out forwards',
            },
            keyframes: {
                countUp: { from: { opacity: 0 }, to: { opacity: 1 } },
                fadeIn:  { from: { opacity: 0 }, to: { opacity: 1 } },
                slideUp: { from: { opacity: 0, transform: 'translateY(20px)' }, to: { opacity: 1, transform: 'translateY(0)' } },
            },
        },
    },

    plugins: [forms, typography],

    // Classes générées dynamiquement (badges actualités via PHP / composants)
    safelist: [
        'badge-annonce',
        'badge-evenement',
        'badge-publication',
        'badge-actualite',
        'badge-financement',
    ],
};
