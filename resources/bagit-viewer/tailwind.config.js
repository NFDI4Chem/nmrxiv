/** @type {import('tailwindcss').Config} */
export default {
    content: ['./index.html', './src/**/*.{vue,js}'],
    theme: {
        extend: {
            colors: {
                teal: {
                    50: '#f0fdfa',
                    100: '#ccfbf1',
                    400: '#2dd4bf',
                    500: '#14b8a6',
                    600: '#0d9488',
                    700: '#0f766e',
                },
            },
        },
    },
    plugins: [],
};
