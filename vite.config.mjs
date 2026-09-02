import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel([
            'resources/js/app.js',
            'resources/css/app.css',
        ]),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
                compilerOptions: {
                    isCustomElement: (tag) => tag.includes('ontology')
                }
            },
        }),
    ],
    resolve: {
        alias: {
            '@': '/resources/js',
            'ziggy-js': path.resolve('vendor/tightenco/ziggy'),
        },
        dedupe: ['openchemlib', '3dmol'],
    },
    optimizeDeps: {
        include: ['openchemlib', '3dmol'],
    },
    server: {
        host: '0.0.0.0',
        port: 5173,
        https: false,
        hmr: {
            host: 'localhost',
        },
    },
});