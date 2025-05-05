import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import { fileURLToPath, URL } from "node:url";

export default defineConfig({
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            "@": fileURLToPath(new URL("./resources/js", import.meta.url)),
        },
    },
});
