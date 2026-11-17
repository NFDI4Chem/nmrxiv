/** @type {import('tailwindcss').Config} */
export default {
    content: ['./index.html', './src/**/*.{vue,js}'],
    theme: {
        extend: {
            colors: {
                teal: {
                    50: '#e6f6f9',
                    100: '#b3e2ec',
                    200: '#019DBB',
                    300: '#019DBB',
                    400: '#02abc9',
                    500: '#019DBB',
                    600: '#0088a0',
                    700: '#019DBB',
                    800: '#0088a0',
                    900: '#019DBB',
                },
            },
        },
    },
    plugins: [],
};
