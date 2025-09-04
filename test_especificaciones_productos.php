<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$app = Application::configure(basePath: dirname(__FILE__))
    ->withRouting(
        web: __DIR__.'/routes/web.php',
        commands: __DIR__.'/app/Console/Commands',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    echo "=== VERIFICANDO ESPECIFICACIONES DE PRODUCTOS ===\n\n";
    
    // Contar especificaciones de productos
    $totalEspecs = \App\Models\EspecificacionProducto::count();
    echo "Total especificaciones de productos: {$totalEspecs}\n\n";
    
    if ($totalEspecs > 0) {
        // Mostrar productos con especificaciones
        $productos = \App\Models\Producto::with(['especificaciones.especificacionCategoria', 'categoria'])->get();
        
        foreach ($productos as $producto) {
            $especs = $producto->especificaciones->count();
            echo "📱 {$producto->nombre_producto} ({$producto->categoria->nombre}): {$especs} especificaciones\n";
            
            if ($especs > 0) {
                foreach ($producto->especificaciones as $espec) {
                    echo "   • {$espec->especificacionCategoria->etiqueta}: {$espec->valor}\n";
                }
            }
            echo "\n";
        }
    } else {
        echo "❌ No hay especificaciones de productos creadas\n";
        echo "Esto explica por qué el formulario no muestra campos de especificaciones\n";
    }
    
    echo "\n=== VERIFICANDO ESPECIFICACIONES DE CATEGORÍA ===\n";
    
    // Verificar especificaciones de categoría
    $categorias = \App\Models\Categoria::with('especificaciones')->get();
    
    foreach ($categorias as $categoria) {
        $especs = $categoria->especificaciones->count();
        echo "🏷️  {$categoria->nombre}: {$especs} especificaciones definidas\n";
        
        if ($especs > 0) {
            foreach ($categoria->especificaciones as $espec) {
                echo "   • {$espec->etiqueta} ({$espec->tipo_campo}) - Requerido: " . ($espec->requerido ? 'Sí' : 'No') . "\n";
            }
        }
        echo "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
}
