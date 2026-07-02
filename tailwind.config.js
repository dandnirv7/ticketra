import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/views/**/*.html",
        "./resources/js/**/*.js",
        "./app/Filament/**/*.php",
    ],
    theme: {
        extend: {
            colors: {
                background: "oklch(96% 0.03 155)", // mint lembut
                surface: "#ffffff",
                primary: "oklch(78% 0.15 140)",   // medium green
                brand: "oklch(82% 0.08 290)",     // lavender
                secondary: "oklch(84% 0.06 220)", // sky
                accent: "oklch(90% 0.12 90)",     // lemon
                foreground: "oklch(20% 0.02 260)",// slate dark
                border: "oklch(25% 0.02 260)",    // border dark
                overlay: "rgba(0, 0, 0, 0.7)",
                "accent-red": "oklch(65% 0.18 25)", // semantic danger
                "auth-foreground": "#111827",
                "auth-background": "oklch(97% 0.02 130)",
                "auth-border": "#111827",
            },
            fontFamily: {
                base: ['"Archivo"', "sans-serif"],
                heading: ['"DM Sans"', "sans-serif"],
                price: ['"Space Grotesk"', "sans-serif"],
                sans: ["Figtree", ...defaultTheme.fontFamily.sans],
            },
            borderRadius: {
                base: "12px",
            },
            boxShadow: {
                neo: "6px 6px 0px 0px #000",
                "neo-lg": "12px 12px 0px 0px #000",
                "neo-sm": "3px 3px 0px 0px #000",
                "neo-hover": "3px 3px 0px 0px #000",
                "neo-active": "0px 0px 0px 0px #000",
                sm: "4px 4px 0px oklch(25% 0.02 260)",
                md: "6px 6px 0px oklch(25% 0.02 260)",
                lg: "8px 8px 0px oklch(25% 0.02 260)",
            },
        },
    },
    plugins: [forms],
};
