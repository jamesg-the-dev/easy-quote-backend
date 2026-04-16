/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './resources/views/**/*.blade.php',
    './resources/js/**/*.{js,jsx,ts,tsx}',
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Inter', 'system-ui', 'sans-serif'],
      },
      animation: {
        'accordion-down': 'accordion-down 0.2s ease-out',
        'accordion-up': 'accordion-up 0.2s ease-out',
      },
      keyframes: {
        'accordion-down': {
          from: { opacity: 0, height: 0 },
          to: { opacity: 1, height: 'var(--radix-accordion-content-height)' },
        },
        'accordion-up': {
          from: { opacity: 1, height: 'var(--radix-accordion-content-height)' },
          to: { opacity: 0, height: 0 },
        },
      },
    },
  },
  plugins: [],
}
