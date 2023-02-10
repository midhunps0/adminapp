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
            minWidth: {
                '1': '.25rem',
                '2': '.5rem',
                '3': '.75rem',
                '4': '1rem',
                '8': '2rem',
                '12': '3rem',
                '14': '3.5rem',
                '16': '4rem',
                '18': '4.5rem',
                '20': '5rem',
                '24': '6rem',
                '36': '9rem',
                '48': '12rem',
                '72': '18rem',
                '96': '24rem',
                '100': '28rem',
                '200': '56rem',
                '300': '84rem',
                '400': '112rem',
                '1/5': '20%',
                '1/4': '25%',
                '1/3': '33.3333%',
                '1/2': '50%',
                '2/3': '66.6667%',
                '3/4': '75%',
                '4/5': '80%',
                '9/10': '90%'
               },
            minHeight: {
                '1': '.25rem',
                '2': '.5rem',
                '3': '.75rem',
                '4': '1rem',
                '8': '2rem',
                '12': '3rem',
                '14': '3.5rem',
                '16': '4rem',
                '18': '4.5rem',
                '20': '5rem',
                '24': '6rem',
                '36': '9rem',
                '48': '12rem',
                '72': '18rem',
                '96': '24rem',
                '192': '48rem',
                '384': '96rem',
                '1/5': '20vh',
                '1/4': '25vh',
                '1/3': '33.3333vh',
                '1/2': '50vh',
                '2/3': '67.6666vh',
                '3/4': '75vh',
                '4/5': '80vh',
                '9/10': '90vh',
            },
            maxWidth: {
                '1': '.25rem',
                '2': '.5rem',
                '3': '.75rem',
                '4': '1rem',
                '8': '2rem',
                '12': '3rem',
                '14': '3.5rem',
                '16': '4rem',
                '18': '4.5rem',
                '20': '5rem',
                '24': '6rem',
                '36': '9rem',
                '48': '12rem',
                '72': '18rem',
                '96': '24rem',
                '100': '28rem',
                '200': '56rem',
                '300': '84rem',
                '400': '112rem',
                '1/5': '20%',
                '1/4': '25%',
                '1/3': '33.3333%',
                '1/2': '50%',
                '2/3': '66.6667%',
                '3/4': '75%',
                '4/5': '80%',
                '9/10': '90%'
               },
            maxHeight: {
                '1': '.25rem',
                '2': '.5rem',
                '3': '.75rem',
                '4': '1rem',
                '8': '2rem',
                '12': '3rem',
                '14': '3.5rem',
                '16': '4rem',
                '18': '4.5rem',
                '20': '5rem',
                '24': '6rem',
                '36': '9rem',
                '48': '12rem',
                '72': '18rem',
                '96': '24rem',
                '192': '48rem',
                '384': '96rem',
                '1/5': '20vh',
                '1/4': '25vh',
                '1/3': '33.3333vh',
                '1/2': '50vh',
                '2/3': '67.6666vh',
                '3/4': '75vh',
                '4/5': '80vh',
                '9/10': '90vh',
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
