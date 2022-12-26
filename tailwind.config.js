const defaultTheme = require('tailwindcss/defaultTheme');

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
        './resources/css/**/*.css',
        './modules/**/**/resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Nunito', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [
        require('@tailwindcss/forms'),
        require("daisyui"),
    ],

    daisyui: {
        themes: [
            // 'light',
            {
                light: {
                    ...require("daisyui/src/colors/themes")["[data-theme=light]"],
                    "primary": "#6419E6",
                    "secondary": "#D926A9",
                    "accent": "#16a34A",
                    "neutral": "#1f2937",
                    "base-100": "#FFFFFF",
                    "info": "#38bdf8",
                    "success": "#36D399",
                    "warning": "#F97316",
                    "error": "#E11d48",
                },
            },
            {
                'newdark': {
                    'primary': '#570df8',
                    'primary-focus': '#4506cb',
                    'primary-content': '#ffffff',
                    'secondary': '#f000b8',
                    'secondary-focus': '#bd0091',
                    'secondary-content': '#ffffff',
                    'accent': '#37cdbe',
                    'accent-focus': '#2aa79b',
                    'accent-content': '#ffffff',
                    'neutral': '#3d4451',
                    'neutral-focus': '#2a2e37',
                    'neutral-content': '#d1d1d1',
                    'base-100': '#252c3c',
                    // 'base-200': '#1e2633',
                    // 'base-200': '#1d2430',
                    'base-200': '#1a2028',
                    'base-300': '#000000',
                    'base-content': '#e0e0e0',
                    'info': '#2094f3',
                    'success': '#009485',
                    // 'warning': '#ff9900',
                    'warning': '#decb5a',
                    'error': '#ff5724',
                },
            },
        ],
    },
};
