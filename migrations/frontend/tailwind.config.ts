import type { Config } from "tailwindcss";

const config: Config = {
  content: [
    "./pages/**/*.{js,ts,jsx,tsx,mdx}",
    "./components/**/*.{js,ts,jsx,tsx,mdx}",
    "./app/**/*.{js,ts,jsx,tsx,mdx}",
  ],
  theme: {
    extend: {
      colors: {
        // E.MO.TI.VE – prototipos: teal/cyan, gold, beige, dark gray
        primary: {
          DEFAULT: "#0d9488",
          light: "#14b8a6",
          dark: "#0f766e",
        },
        accent: {
          DEFAULT: "#0d9488",
          light: "#14b8a6",
        },
        gold: {
          DEFAULT: "#b8860b",
          light: "#d4a84b",
          dark: "#8b6914",
        },
        emotive: {
          beige: "#e8e4df",
          "beige-dark": "#c9c4be",
          "gray-header": "#374151",
          "gray-footer": "#1f2937",
          "panel-bg": "#f3f4f6",
        },
      },
      fontFamily: {
        sans: ["var(--font-quicksand)", "Quicksand", "system-ui", "sans-serif"],
      },
    },
  },
  plugins: [],
};
export default config;
