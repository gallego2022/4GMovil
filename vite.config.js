import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    build: {
        // Optimizaciones de construcción
        minify: 'terser',
        terserOptions: {
            compress: {
                drop_console: true,
                drop_debugger: true,
                pure_funcs: ['console.log', 'console.info', 'console.debug'],
            },
            mangle: true,
        },
        
        // División de chunks para mejor cacheo
        rollupOptions: {
            output: {
                manualChunks: {
                    // Chunk para bibliotecas de proveedores
                    vendor: ['alpinejs', 'axios'],
                },
                
                // Nombres de archivos optimizados para cache
                chunkFileNames: 'js/[name]-[hash].js',
                entryFileNames: 'js/[name]-[hash].js',
                assetFileNames: (assetInfo) => {
                    const info = assetInfo.name.split('.');
                    const ext = info[info.length - 1];
                    if (/\.(css)$/.test(assetInfo.name)) {
                        return `css/[name]-[hash].${ext}`;
                    }
                    if (/\.(png|jpe?g|gif|svg|webp)$/.test(assetInfo.name)) {
                        return `images/[name]-[hash].${ext}`;
                    }
                    return `assets/[name]-[hash].${ext}`;
                },
            },
        },
        
        // Optimizaciones de CSS
        cssCodeSplit: true,
        cssMinify: true,
        
        // Optimizaciones de assets
        assetsInlineLimit: 4096, // 4KB
        chunkSizeWarningLimit: 1000,
        
        // Source maps solo en desarrollo
        sourcemap: process.env.NODE_ENV === 'development',
    },
    
    // Optimizaciones de desarrollo
    server: {
        hmr: {
            host: 'localhost',
        },
    },
    
    // Optimizaciones de dependencias
    optimizeDeps: {
        include: ['alpinejs', 'axios'],
        exclude: ['laravel-vite-plugin'],
    },
    
    // Configuración de alias para mejor rendimiento
    resolve: {
        alias: {
            '@': '/resources/js',
            '~': '/resources/css',
        },
    },
});
