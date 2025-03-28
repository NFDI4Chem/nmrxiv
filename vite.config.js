import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    server: { 
        https: false, 
        host: 'localhost', 
        port: 8080
    }, 
    plugins: [
        laravel([
            'resources/js/app.js',
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
            '@': '/resources/js'
        }
    }
});