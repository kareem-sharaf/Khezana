import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    // Phase 1.5: Remove console/debugger in production
    esbuild: {
        drop: process.env.NODE_ENV === 'production' ? ['console', 'debugger'] : [],
    },
    // Server configuration for development (only when running npm run dev)
    server: {
        host: '0.0.0.0',
        port: 5173,
        strictPort: false,
        hmr: { host: 'localhost' },
    },
    // Phase 1.5: Build configuration for production
    build: {
        manifest: true,
        outDir: 'public/build',
        emptyOutDir: true,
        target: 'esnext',
        cssCodeSplit: true,
        rollupOptions: {
            output: {
                assetFileNames: 'assets/[name]-[hash][extname]',
                entryFileNames: 'assets/[name]-[hash].js',
                chunkFileNames: 'assets/[name]-[hash].js',
                manualChunks: (id) => {
                    if (id.includes('node_modules')) {
                        if (id.includes('alpinejs')) return 'vendor-alpine';
                        if (id.includes('axios')) return 'vendor-axios';
                        return 'vendor';
                    }
                },
            },
        },
    },
});
