/** @type {import('tailwindcss').Config} */
import flowbitePlugin from 'flowbite/plugin';
import daisyui from 'daisyui';

export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
        "./node_modules/flowbite/**/*.js",
    ],
    theme: {
        extend: {},
    },
    daisyui: {
        themes: [],
    },
    plugins: [
        flowbitePlugin,
        daisyui,
    ],
};
