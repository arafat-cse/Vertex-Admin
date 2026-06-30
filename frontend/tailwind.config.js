/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./src/**/*.{html,ts,scss}"
  ],
  darkMode: 'class',
  theme: {
    extend: {
      colors: {
        primary: {
          50:  '#f5f3ff',
          100: '#ede9fe',
          200: '#ddd6fe',
          300: '#c4b5fd',
          400: '#a78bfa',
          500: '#8b5cf6',
          600: '#7C3AED',
          700: '#6d28d9',
          800: '#5b21b6',
          900: '#4c1d95',
          950: '#2e1065',
          DEFAULT: '#7C3AED'
        },
        sidebar: {
          DEFAULT: '#1E1B4B',
          light:   '#2d2a5e',
          dark:    '#13112e'
        },
        vertex: {
          purple:  '#7C3AED',
          indigo:  '#1E1B4B',
          violet:  '#6d28d9',
          success: '#10b981',
          warning: '#f59e0b',
          danger:  '#ef4444',
          info:    '#3b82f6'
        }
      },
      fontFamily: {
        sans:  ['Inter', 'ui-sans-serif', 'system-ui', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'Helvetica Neue', 'Arial', 'Noto Sans', 'sans-serif'],
        inter: ['Inter', 'sans-serif']
      },
      boxShadow: {
        card:    '0 1px 3px 0 rgba(0,0,0,0.1), 0 1px 2px -1px rgba(0,0,0,0.1)',
        sidebar: '4px 0 6px -1px rgba(0,0,0,0.1), 2px 0 4px -2px rgba(0,0,0,0.1)'
      },
      borderRadius: {
        DEFAULT: '0.5rem'
      },
      transitionDuration: {
        DEFAULT: '200ms'
      }
    }
  },
  plugins: []
};
