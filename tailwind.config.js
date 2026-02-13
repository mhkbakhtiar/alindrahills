export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
    "./app/**/*.php",          // 🔥 INI KUNCI UTAMANYA
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          50: '#eff6ff',
          100: '#dbeafe',
          200: '#bfdbfe',
          300: '#93c5fd',
          400: '#60a5fa',
          500: '#3b82f6',
          600: '#2563eb',
          700: '#1d4ed8',
          800: '#1e40af',
          900: '#1e3a8a',
        },
      },
      fontSize: {
        xs: '0.75rem',
        sm: '0.8125rem',
        base: '0.875rem',
        lg: '0.9375rem',
      },
      spacing: {
        18: '4.5rem',
        88: '22rem',
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
  ],
}
