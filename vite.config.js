import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import { VitePWA } from 'vite-plugin-pwa';

export default defineConfig({
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
        VitePWA({
            registerType: 'autoUpdate',
            includeAssets: ['favicon.ico', 'apple-touch-icon.png'],
            manifest: {
                name: '75 hard tracker',
                short_name: '75Hard',
                theme_color: '#111111',
                background_color: '#111111',
                display: 'standalone',
                start_url: '/',
                icons: [
                    { src: '/pwa-192.svg', sizes: '192x192', type: 'image/svg+xml' },
                    { src: '/pwa-512.svg', sizes: '512x512', type: 'image/svg+xml' },
                ],
            },
            workbox: {
                navigateFallback: '/offline',
                runtimeCaching: [
                    {
                        urlPattern: ({ request }) => request.destination === 'script' || request.destination === 'style' || request.destination === 'font',
                        handler: 'CacheFirst',
                    },
                    {
                        urlPattern: ({ url }) => url.pathname.startsWith('/logs') || url.pathname.startsWith('/photos'),
                        handler: 'NetworkFirst',
                    },
                ],
            },
        }),
    ],
});
