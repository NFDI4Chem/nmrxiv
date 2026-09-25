import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import { viteSingleFile } from 'vite-plugin-singlefile';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const version = process.env.BAGIT_VIEWER_VERSION || '1.0.0';
const buildDate = new Date().toISOString().slice(0, 10);

/**
 * After vite-plugin-singlefile, Blueprint icon SVG fonts may still sit beside
 * index.html. Inline them as data URIs so the viewer is truly one file.
 */
function inlineSiblingAssets() {
    return {
        name: 'inline-sibling-assets',
        closeBundle() {
            const distDir = path.resolve(__dirname, 'dist');
            const htmlPath = path.join(distDir, 'index.html');
            if (!fs.existsSync(htmlPath)) {
                return;
            }

            let html = fs.readFileSync(htmlPath, 'utf8');
            const assets = fs
                .readdirSync(distDir)
                .filter((name) => name !== 'index.html');

            for (const name of assets) {
                const assetPath = path.join(distDir, name);
                if (!fs.statSync(assetPath).isFile()) {
                    continue;
                }

                const bytes = fs.readFileSync(assetPath);
                const mime = name.endsWith('.svg')
                    ? 'image/svg+xml'
                    : 'application/octet-stream';
                const dataUri = `data:${mime};base64,${bytes.toString('base64')}`;
                const escaped = name.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                html = html.replace(
                    new RegExp(`(?:\\./)?${escaped}(?:\\?[^)'"\\s]*)?`, 'g'),
                    dataUri,
                );
                fs.unlinkSync(assetPath);
            }

            fs.writeFileSync(htmlPath, html);
            fs.writeFileSync(
                path.join(distDir, 'nmrxiv-bagit-viewer.html'),
                html,
            );
        },
    };
}

export default defineConfig({
    plugins: [
        vue(),
        viteSingleFile({ removeViteModuleLoader: true }),
        {
            name: 'html-version-stamp',
            transformIndexHtml(html) {
                return html
                    .replace(/%BAGIT_VIEWER_VERSION%/g, version)
                    .replace(/%BAGIT_VIEWER_BUILD_DATE%/g, buildDate);
            },
        },
        inlineSiblingAssets(),
    ],
    define: {
        __BAGIT_VIEWER_VERSION__: JSON.stringify(version),
        __BAGIT_VIEWER_BUILD_DATE__: JSON.stringify(buildDate),
    },
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'src'),
        },
        dedupe: ['react', 'react-dom', 'openchemlib'],
    },
    build: {
        outDir: 'dist',
        emptyOutDir: true,
        cssCodeSplit: false,
        assetsInlineLimit: 100000000,
        rollupOptions: {
            output: {
                inlineDynamicImports: true,
            },
        },
        chunkSizeWarningLimit: 30000,
    },
    test: {
        environment: 'node',
        include: ['tests/**/*.test.js'],
    },
});
