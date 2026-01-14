import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    plugins: [
        tailwindcss(),
        laravel({
            input: [
                "resources/css/app.css",
                "resources/js/app.js",
                // ZePocket Supply Module
                "resources/css/zepocket.css",
                "resources/js/modules/zepocket/main.js",
            ],
            refresh: true, // Atualiza automaticamente quando houver alterações
        }),
    ],
    server: {
        watch: {
            usePolling: false, // Tenta reduzir watchers
            interval: 1000, // Ajusta a frequência de verificação
        },

        fs: {
            strict: true, // Restringe o monitoramento a arquivos importantes
            // Permite servir arquivos também de node_modules durante o dev (evita 403 ao importar pacotes)
            allow: [process.cwd(), "resources", "public"], // inclui a raiz do projeto
        },
        host: "127.0.0.1",
        port: 5173,
    },
    build: {
        sourcemap: false, // Desabilita mapas de fonte no ambiente de produção
        rollupOptions: {
            output: {
                manualChunks: (id) => {
                    if (id.includes("node_modules")) {
                        return "vendor"; // Agrupa pacotes em "vendor.js"
                    }
                },
            },
        },
    },
});
