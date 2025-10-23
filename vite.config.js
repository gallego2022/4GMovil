import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            transformOnDemand: [
                'resources/fonts/**/*.woff2',
                'resources/images/**/*.svg',
            ],
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
                    vendor: ['alpinejs', 'axios', 'sweetalert2'],
                },
                
                // Nombres de archivos optimizados para cache
                chunkFileNames: 'js/[name]-[hash].js',
                entryFileNames: 'js/[name]-[hash].js',
                assetFileNames: (assetInfo) => {
                    if (assetInfo.name.match(/\.(css)$/)) {
                        return 'css/[name]-[hash][extname]';
                    }
                    if (assetInfo.name.match(/\.(png|jpe?g|gif|svg|webp)$/)) {
                        return 'images/[name]-[hash][extname]';
                    }
                    return 'assets/[name]-[hash][extname]';
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
        host: '0.0.0.0',
        port: 5173,
        strictPort: true,
        hmr: {
            host: 'localhost',
            port: 5173,
        },
    },
    
    // Optimizaciones de dependencias
    optimizeDeps: {
        include: ['alpinejs', 'axios', 'sweetalert2'],
        exclude: ['laravel-vite-plugin'],
    },
    
    // Configuración de alias para mejor rendimiento
    resolve: {
        alias: {
          '@': path.resolve(__dirname, 'resources/js'),
          '~': path.resolve(__dirname, 'resources/css'),
        },
      }
});
