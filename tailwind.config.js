import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.js",
    ],
    theme: {
        extend: {
            colors: {
                "neo-pink": "#FFB6C1",
                "neo-blue": "#B6D7FF",
                "neo-yellow": "#FFF3B0",
                "neo-green": "#B6FFB6",
                "neo-purple": "#D4B6FF",
                "neo-orange": "#FFD4B6",
                "neo-cream": "#FFF8E7",
            },
            boxShadow: {
                neo: "6px 6px 0px 0px #000",
                "neo-lg": "12px 12px 0px 0px #000",
                "neo-sm": "3px 3px 0px 0px #000",
                "neo-hover": "3px 3px 0px 0px #000",
                "neo-active": "0px 0px 0px 0px #000",
            },
            fontFamily: {
                sans: ["Figtree", ...defaultTheme.fontFamily.sans],
            },
        },
    },
    plugins: [],
};
