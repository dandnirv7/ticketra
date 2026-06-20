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
    ],
    theme: {
        extend: {
            colors: {
                background: "oklch(0.96 0.01 155)",
                "auth-background": "oklch(97% 0.02 130)",
                foreground: "oklch(20% 0.02 260)",
                "auth-foreground": "#111827",
                border: "oklch(25% 0.02 260)",
                "auth-border": "#111827",
                ring: "oklch(45% 0.05 140)",
                overlay: "rgba(0, 0, 0, 0.7)",
                "secondary-background": "oklch(100% 0 0)",
                "main-foreground": "oklch(18% 0.02 260)",
                main: "oklch(78% 0.15 140)",
                "pastel-mint": "oklch(92% 0.08 165)",
                "pastel-sky": "oklch(92% 0.07 220)",
                "pastel-lavender": "oklch(92% 0.06 290)",
                "pastel-lemon": "oklch(95% 0.09 95)",
                "pastel-peach": "oklch(92% 0.08 45)",
                "pastel-pink": "#fbcfe8",
                "accent-green": "oklch(80% 0.12 145)",
                "accent-yellow": "oklch(85% 0.12 90)",
                "accent-red": "oklch(80% 0.14 25)",
                "lime-neon": "#bef264",
                "neo-pink": "#FFB6C1",
                "neo-blue": "#B6D7FF",
                "neo-yellow": "#FFF3B0",
                "neo-green": "#B6FFB6",
                "neo-purple": "#D4B6FF",
                "neo-orange": "#FFD4B6",
                "neo-cream": "#FFF8E7",
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
