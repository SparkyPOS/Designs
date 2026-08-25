import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [react()],
    build: {
        outDir: '../assets/designer',
        emptyOutDir: true,
        cssCodeSplit: false,
        sourcemap: true,
        rollupOptions: {
            input: 'resources/js/designer/main.jsx',
            output: {
                entryFileNames: 'designer.js',
                chunkFileNames: 'chunks/[name]-[hash].js',
                assetFileNames: (assetInfo) => assetInfo.name?.endsWith('.css') ? 'designer.css' : 'assets/[name]-[hash][extname]',
            },
        },
    },
});
