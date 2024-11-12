/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [   
    "*.php",
    "./includes/**/*.*",
    "./assets/**/*.js",
    "./templates/**/*.*",
     "!./vendor/./node_modules",          
    ],
  theme: {
    extend: {},
  },
  plugins: [],
};
