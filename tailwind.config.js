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
                background: "#FAF8FF",
                foreground: "#111827",
                border: "#1C1917",
                primary: "#B7A5F8",
                secondary: "#A7F3D0",
                "accent-yellow": "#FEF08A",
                "accent-red": "#FCA5A5",
                "brand-hover": "#A88CF8",
                "brand-light": "#CDBBFF",
                "auth-background": "#FAF8FF",
                "auth-foreground": "#111827",
                "auth-border": "#1C1917",
                ring: "#B7A5F8",
                overlay: "rgba(0, 0, 0, 0.7)",
                "secondary-background": "#FFFFFF",
                "main-foreground": "#111827",
                main: "#B7A5F8",
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
