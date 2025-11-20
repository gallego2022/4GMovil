<?php

/**
 * Script para analizar código no utilizado
 * Identifica controladores, servicios y funciones no utilizadas
 */

require __DIR__ . '/../vendor/autoload.php';

class CodeAnalyzer
{
    private $basePath;
    private $controllers = [];
    private $services = [];
    private $routes = [];
    private $usedControllers = [];
    private $usedServices = [];

    public function __construct($basePath)
    {
        $this->basePath = $basePath;
    }

    /**
     * Analizar todos los archivos
     */
    public function analyze()
    {
        echo "🔍 Iniciando análisis de código...\n\n";
        
        $this->scanControllers();
        $this->scanServices();
        $this->scanRoutes();
        $this->findUsedControllers();
        $this->findUsedServices();
        
        $this->generateReport();
    }

    /**
     * Escanear todos los controladores
     */
    private function scanControllers()
    {
        echo "📁 Escaneando controladores...\n";
        $controllerPath = $this->basePath . '/app/Http/Controllers';
        $this->scanDirectory($controllerPath, $this->controllers, 'Controller');
        echo "   Encontrados: " . count($this->controllers) . " controladores\n\n";
    }

    /**
     * Escanear todos los servicios
     */
    private function scanServices()
    {
        echo "📁 Escaneando servicios...\n";
        $servicePath = $this->basePath . '/app/Services';
        $this->scanDirectory($servicePath, $this->services, 'Service');
        echo "   Encontrados: " . count($this->services) . " servicios\n\n";
    }

    /**
     * Escanear archivos de rutas
     */
    private function scanRoutes()
    {
        echo "🛣️  Escaneando rutas...\n";
        $routeFiles = [
            'routes/web.php',
            'routes/admin.php',
            'routes/cliente.php',
            'routes/api.php',
            'routes/publico.php'
        ];

        foreach ($routeFiles as $file) {
            $fullPath = $this->basePath . '/' . $file;
            if (file_exists($fullPath)) {
                $content = file_get_contents($fullPath);
                $this->routes[$file] = $content;
            }
        }
        echo "   Archivos de rutas escaneados: " . count($this->routes) . "\n\n";
    }

    /**
     * Escanear directorio recursivamente
     */
    private function scanDirectory($path, &$collection, $suffix)
    {
        if (!is_dir($path)) {
            return;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path)
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $relativePath = str_replace($this->basePath . '/', '', $file->getPathname());
                $className = $this->extractClassName($file->getPathname());
                
                if ($className && str_ends_with($className, $suffix)) {
                    $collection[$relativePath] = [
                        'path' => $relativePath,
                        'class' => $className,
                        'namespace' => $this->extractNamespace($file->getPathname())
                    ];
                }
            }
        }
    }

    /**
     * Extraer nombre de clase de un archivo
     */
    private function extractClassName($filePath)
    {
        $content = file_get_contents($filePath);
        if (preg_match('/class\s+(\w+)/', $content, $matches)) {
            return $matches[1];
        }
        return null;
    }

    /**
     * Extraer namespace de un archivo
     */
    private function extractNamespace($filePath)
    {
        $content = file_get_contents($filePath);
        if (preg_match('/namespace\s+([^;]+);/', $content, $matches)) {
            return trim($matches[1]);
        }
        return null;
    }

    /**
     * Encontrar controladores usados en rutas
     */
    private function findUsedControllers()
    {
        echo "🔎 Buscando controladores usados en rutas...\n";
        
        // Primero, identificar clases base (que se extienden)
        $baseClasses = [];
        foreach ($this->controllers as $path => $info) {
            $fullPath = $this->basePath . '/' . $path;
            if (file_exists($fullPath)) {
                $content = file_get_contents($fullPath);
                // Buscar si es abstract o si otras clases la extienden
                if (str_contains($content, 'abstract class') || 
                    str_contains($content, 'abstract ')) {
                    $baseClasses[$info['class']] = true;
                }
            }
        }
        
        // Buscar qué clases extienden a otras
        foreach ($this->controllers as $path => $info) {
            $fullPath = $this->basePath . '/' . $path;
            if (file_exists($fullPath)) {
                $content = file_get_contents($fullPath);
                // Buscar "extends NombreClase"
                if (preg_match('/extends\s+(\w+)/', $content, $matches)) {
                    $extendedClass = $matches[1];
                    if (isset($baseClasses[$extendedClass])) {
                        // Esta clase base se está usando
                        $this->usedControllers[$path] = $info;
                    }
                }
            }
        }
        
        // Buscar en rutas
        foreach ($this->routes as $routeFile => $content) {
            foreach ($this->controllers as $path => $info) {
                if (isset($this->usedControllers[$path])) {
                    continue; // Ya está marcado como usado
                }
                
                $className = $info['class'];
                $fullClass = $info['namespace'] . '\\' . $className;
                
                // Buscar referencias en rutas
                if (str_contains($content, $className . '::class') || 
                    str_contains($content, $fullClass) ||
                    preg_match('/\b' . preg_quote($className, '/') . '\b/', $content)) {
                    $this->usedControllers[$path] = $info;
                }
            }
        }
        
        echo "   Controladores usados: " . count($this->usedControllers) . "\n";
        echo "   Controladores no usados: " . (count($this->controllers) - count($this->usedControllers)) . "\n\n";
    }

    /**
     * Encontrar servicios usados
     */
    private function findUsedServices()
    {
        echo "🔎 Buscando servicios usados...\n";
        
        // Buscar en controladores
        foreach ($this->usedControllers as $path => $info) {
            $fullPath = $this->basePath . '/' . $path;
            if (file_exists($fullPath)) {
                $content = file_get_contents($fullPath);
                $this->findServiceReferences($content);
            }
        }
        
        // Buscar en otros servicios
        foreach ($this->services as $path => $info) {
            $fullPath = $this->basePath . '/' . $path;
            if (file_exists($fullPath)) {
                $content = file_get_contents($fullPath);
                $this->findServiceReferences($content);
            }
        }
        
        echo "   Servicios usados: " . count($this->usedServices) . "\n";
        echo "   Servicios no usados: " . (count($this->services) - count($this->usedServices)) . "\n\n";
    }

    /**
     * Buscar referencias a servicios en contenido
     */
    private function findServiceReferences($content)
    {
        foreach ($this->services as $path => $info) {
            $className = $info['class'];
            $fullClass = $info['namespace'] . '\\' . $className;
            
            if (str_contains($content, $className) || 
                str_contains($content, $fullClass)) {
                $this->usedServices[$path] = $info;
            }
        }
    }

    /**
     * Generar reporte
     */
    private function generateReport()
    {
        echo "\n" . str_repeat("=", 80) . "\n";
        echo "📊 REPORTE DE ANÁLISIS\n";
        echo str_repeat("=", 80) . "\n\n";

        // Controladores no usados
        $unusedControllers = array_diff_key($this->controllers, $this->usedControllers);
        if (!empty($unusedControllers)) {
            echo "❌ CONTROLADORES NO UTILIZADOS:\n";
            echo str_repeat("-", 80) . "\n";
            
            // Separar entre clases base y controladores realmente no usados
            $baseClasses = [];
            $trulyUnused = [];
            
            foreach ($unusedControllers as $path => $info) {
                $fullPath = $this->basePath . '/' . $path;
                if (file_exists($fullPath)) {
                    $content = file_get_contents($fullPath);
                    if (str_contains($content, 'abstract class') || 
                        str_contains($content, 'abstract ')) {
                        $baseClasses[$path] = $info;
                    } else {
                        $trulyUnused[$path] = $info;
                    }
                }
            }
            
            if (!empty($trulyUnused)) {
                echo "\n⚠️  CONTROLADORES REALMENTE NO UTILIZADOS (pueden eliminarse):\n";
                foreach ($trulyUnused as $path => $info) {
                    echo "  - {$path}\n";
                    echo "    Clase: {$info['class']}\n";
                    echo "    Namespace: {$info['namespace']}\n\n";
                }
            }
            
            if (!empty($baseClasses)) {
                echo "\nℹ️  CLASES BASE (verificar si se extienden):\n";
                foreach ($baseClasses as $path => $info) {
                    echo "  - {$path}\n";
                    echo "    Clase: {$info['class']}\n";
                    echo "    Namespace: {$info['namespace']}\n";
                    echo "    Nota: Verificar manualmente si se extiende\n\n";
                }
            }
        } else {
            echo "✅ Todos los controladores están en uso\n\n";
        }

        // Servicios no usados
        $unusedServices = array_diff_key($this->services, $this->usedServices);
        if (!empty($unusedServices)) {
            echo "❌ SERVICIOS NO UTILIZADOS:\n";
            echo str_repeat("-", 80) . "\n";
            foreach ($unusedServices as $path => $info) {
                echo "  - {$path}\n";
                echo "    Clase: {$info['class']}\n";
                echo "    Namespace: {$info['namespace']}\n\n";
            }
        } else {
            echo "✅ Todos los servicios están en uso\n\n";
        }

        // Resumen
        echo str_repeat("=", 80) . "\n";
        echo "📈 RESUMEN:\n";
        echo str_repeat("-", 80) . "\n";
        echo "Total controladores: " . count($this->controllers) . "\n";
        echo "Controladores usados: " . count($this->usedControllers) . "\n";
        echo "Controladores no usados: " . count($unusedControllers) . "\n\n";
        echo "Total servicios: " . count($this->services) . "\n";
        echo "Servicios usados: " . count($this->usedServices) . "\n";
        echo "Servicios no usados: " . count($unusedServices) . "\n";
        echo str_repeat("=", 80) . "\n";
    }
}

// Ejecutar análisis
$basePath = dirname(__DIR__);
$analyzer = new CodeAnalyzer($basePath);
$analyzer->analyze();

