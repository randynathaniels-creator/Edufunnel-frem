/** @type {import('tailwindcss').Config} */
export default {
  darkMode: 'class', // Ini yang paling penting buat fitur tadi
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {},
  },
  plugins: [],
}