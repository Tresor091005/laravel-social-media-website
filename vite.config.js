import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import dotenv from 'dotenv';

dotenv.config();

export default defineConfig({
    // server: {
    //     host: '192.168.45.72',
    //     port: 3000,
    // },
    base: process.env.APP_URL ? `${process.env.APP_URL}/build/` : '/build/',
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
});
