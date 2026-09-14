import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import path from 'path';

export default defineConfig({
    server: {
        host: '0.0.0.0',
        origin: 'http://localhost:5179',
        port: 5179,
        strictPort: true,
        watch: {
            usePolling: true
        },
        cors: {
            origin: '*',
            methods: ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
            allowedHeaders: ['Content-Type', 'Authorization']
        }
    },

    resolve: {
        alias: {
            '@': path.resolve('resources/js'),
            'ziggy-js': path.resolve('vendor/tightenco/ziggy'),
        },
    },

    plugins: [
        vue(),
        laravel({
            input: ['resources/js/main.js'],
            buildDirectory: 'asset/admin/build',
            refresh: true,
        })
    ],
    build: {
        sourcemap: true
    }
});
