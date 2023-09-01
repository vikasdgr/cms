const defaultTheme = require('tailwindcss/defaultTheme');

/** @type {import('tailwindcss').Config} */
module.exports = {

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
        "./node_modules/flowbite/**/*.js"
    ],

    theme: {
        extend: {
            zIndex: {
                '999': '999',
              },
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors:{
                'primary': "var(--primary-color)",
                'secondary': "var(--secondary-color)",
                'tertiary': "var(--tertiary-color)",
            }
        },
    },

    plugins: [require('@tailwindcss/forms'), require('@tailwindcss/typography'),
     require('flowbite/plugin')
],
    safelist: [
        // 'bg-green-100',
        // 'bg-red-100',
        // 'border-green-400',
        // 'border-red-400',
        // 'text-red-100',
        // 'text-green-700',
        // 'text-red-500',
        // 'text-green-500', EQUIVALENT TO
        {
            pattern: /text-(red|green|700|500)/,
        },
        {
            pattern: /bg-(red|green|100|500)/,
        },
        {
            pattern: /border-(red|green|400)/,
        }
    ],
};
