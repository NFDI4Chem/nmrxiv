const defaultTheme = require('tailwindcss/defaultTheme')
const colors = require('tailwindcss/colors')

module.exports = {
  mode: 'jit',
  purge: [
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    './vendor/laravel/jetstream/**/*.blade.php',
    './storage/framework/views/*.php',
    './resources/views/**/*.blade.php',
    './resources/js/**/*.vue',
    './vendor/spatie/laravel-support-bubble/config/**/*.php',
    './vendor/spatie/laravel-support-bubble/resources/views/**/*.blade.php',
  ],

  theme: {
    patterns: {
      opacities: {
          100: "1",
          80: ".80",
          60: ".60",
          40: ".40",
          20: ".20",
          10: ".10",
          5: ".05",
      },
      sizes: {
          1: "0.25rem",
          2: "0.5rem",
          4: "1rem",
          6: "1.5rem",
          8: "2rem",
          16: "4rem",
          20: "5rem",
          24: "6rem",
          32: "8rem",
      }
  },
    extend: {
      fontFamily: {
        sans: ['Nunito', ...defaultTheme.fontFamily.sans],
      },
      colors: {
        rose: colors.rose,
        sky: colors.sky,
        teal: {
          100: '#019DBB',
          200: '#019DBB',
          300: '#019DBB',
          400: '#02abc9',
          500: '#019DBB',
          600: '#0088a0',
          700: '#019DBB',
          800: '#019DBB',
          900: '#019DBB',
        },
      },
    }
  },

  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),
    require('@tailwindcss/line-clamp'),
    require('tailwindcss-bg-patterns'),
  ],
}
