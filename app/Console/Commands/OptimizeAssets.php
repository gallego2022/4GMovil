<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class OptimizeAssets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assets:optimize {--force : Forzar reoptimización}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimizar y cachear assets estáticos (CSS, JS, imágenes)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Iniciando optimización de assets...');

        // Limpiar cache existente
        $this->cleanCache();

        // Optimizar CSS
        $this->optimizeCSS();

        // Optimizar JavaScript
        $this->optimizeJS();

        // Generar archivos de versión
        $this->generateVersionFiles();

        // Limpiar y optimizar cache de Laravel
        $this->optimizeLaravelCache();

        $this->info('✅ Optimización de assets completada exitosamente!');
        
        return Command::SUCCESS;
    }

    /**
     * Limpiar cache existente
     */
    private function cleanCache()
    {
        $this->info('🧹 Limpiando cache existente...');
        
        $cachePaths = [
            storage_path('framework/cache'),
            storage_path('framework/views'),
            storage_path('framework/sessions'),
        ];

        foreach ($cachePaths as $path) {
            if (File::exists($path)) {
                File::deleteDirectory($path);
                File::makeDirectory($path, 0755, true);
            }
        }
    }

    /**
     * Optimizar archivos CSS
     */
    private function optimizeCSS()
    {
        $this->info('🎨 Optimizando archivos CSS...');
        
        $cssPath = public_path('css');
        if (File::exists($cssPath)) {
            $cssFiles = File::glob($cssPath . '/*.css');
            
            foreach ($cssFiles as $cssFile) {
                $this->minifyCSS($cssFile);
            }
        }
    }

    /**
     * Optimizar archivos JavaScript
     */
    private function optimizeJS()
    {
        $this->info('⚡ Optimizando archivos JavaScript...');
        
        $jsPath = public_path('js');
        if (File::exists($jsPath)) {
            $jsFiles = File::glob($jsPath . '/*.js');
            
            foreach ($jsFiles as $jsFile) {
                $this->minifyJS($jsFile);
            }
        }
    }

    /**
     * Minificar archivo CSS
     */
    private function minifyCSS(string $filePath)
    {
        $content = File::get($filePath);
        
        // Remover comentarios
        $content = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $content);
        
        // Remover espacios en blanco innecesarios
        $content = preg_replace('/\s+/', ' ', $content);
        $content = str_replace(['; ', ' {', '{ ', ' }', '} ', ': ', ' :'], [';', '{', '{', '}', '}', ':', ':'], $content);
        
        // Crear archivo minificado
        $minifiedPath = str_replace('.css', '.min.css', $filePath);
        File::put($minifiedPath, trim($content));
        
        $this->line("  ✓ Minificado: " . basename($filePath));
    }

    /**
     * Minificar archivo JavaScript
     */
    private function minifyJS(string $filePath)
    {
        $content = File::get($filePath);
        
        // Remover comentarios de una línea
        $content = preg_replace('/\/\/.*$/m', '', $content);
        
        // Remover comentarios multilínea
        $content = preg_replace('/\/\*[\s\S]*?\*\//', '', $content);
        
        // Remover espacios en blanco innecesarios
        $content = preg_replace('/\s+/', ' ', $content);
        $content = str_replace(['; ', ' {', '{ ', ' }', '} ', ': ', ' :'], [';', '{', '{', '}', '}', ':', ':'], $content);
        
        // Crear archivo minificado
        $minifiedPath = str_replace('.js', '.min.js', $filePath);
        File::put($minifiedPath, trim($content));
        
        $this->line("  ✓ Minificado: " . basename($filePath));
    }

    /**
     * Generar archivos de versión
     */
    private function generateVersionFiles()
    {
        $this->info('📝 Generando archivos de versión...');
        
        $version = date('Y.m.d.His');
        $versionFile = storage_path('app/asset-version.txt');
        
        File::put($versionFile, $version);
        
        $this->line("  ✓ Versión generada: {$version}");
    }

    /**
     * Optimizar cache de Laravel
     */
    private function optimizeLaravelCache()
    {
        $this->info('⚙️ Optimizando cache de Laravel...');
        
        Artisan::call('config:cache');
        Artisan::call('route:cache');
        Artisan::call('view:cache');
        
        $this->line("  ✓ Cache de configuración optimizado");
        $this->line("  ✓ Cache de rutas optimizado");
        $this->line("  ✓ Cache de vistas optimizado");
    }
}
