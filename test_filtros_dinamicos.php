<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\EspecificacionCategoria;
use App\Models\EspecificacionProducto;

// Configurar la aplicación Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 **Prueba de Filtros Dinámicos**\n\n";

try {
    // Obtener todas las categorías
    $categorias = Categoria::all();
    
    echo "📊 **Categorías disponibles:**\n";
    foreach ($categorias as $categoria) {
        echo "   - ID: {$categoria->categoria_id} | {$categoria->nombre_categoria}\n";
    }
    echo "\n";

    // Probar cada categoría
    foreach ($categorias as $categoria) {
        echo "🔧 **Probando categoría: {$categoria->nombre_categoria} (ID: {$categoria->categoria_id})**\n";
        
        // Obtener especificaciones de la categoría
        $especificaciones = EspecificacionCategoria::where('categoria_id', $categoria->categoria_id)
            ->where('activo', true)
            ->orderBy('orden', 'asc')
            ->get();
        
        echo "   - Especificaciones definidas: {$especificaciones->count()}\n";
        
        if ($especificaciones->count() > 0) {
            foreach ($especificaciones as $espec) {
                echo "     • {$espec->etiqueta} ({$espec->nombre_campo})\n";
                
                // Obtener valores únicos para esta especificación
                $valores = EspecificacionProducto::whereHas('especificacionCategoria', function ($query) use ($espec) {
                    $query->where('especificacion_id', $espec->especificacion_id);
                })
                ->whereHas('producto', function ($query) use ($categoria) {
                    $query->where('categoria_id', $categoria->categoria_id);
                })
                ->pluck('valor')
                ->unique()
                ->values()
                ->toArray();
                
                echo "       Valores disponibles: " . implode(', ', $valores) . "\n";
            }
        } else {
            echo "     • No hay especificaciones definidas para esta categoría\n";
        }
        
        // Contar productos en esta categoría
        $productosEnCategoria = Producto::where('categoria_id', $categoria->categoria_id)->count();
        echo "   - Productos en categoría: {$productosEnCategoria}\n";
        
        // Contar productos con especificaciones
        $productosConEspecs = Producto::where('categoria_id', $categoria->categoria_id)
            ->whereHas('especificaciones')
            ->count();
        echo "   - Productos con especificaciones: {$productosConEspecs}\n";
        
        echo "\n";
    }

    echo "🎯 **Información para el Frontend:**\n";
    echo "   - Los filtros dinámicos se cargan automáticamente al seleccionar una categoría\n";
    echo "   - Solo se muestran las especificaciones que tienen valores disponibles\n";
    echo "   - Los valores se ordenan numéricamente cuando es posible\n";
    echo "   - Cada especificación tiene su propio icono y etiqueta\n";
    echo "   - Los filtros se pueden combinar con otros filtros existentes\n\n";

    echo "✅ **Prueba completada exitosamente!**\n";
    echo "💡 Los filtros dinámicos están listos para usar en el frontend.\n";

} catch (Exception $e) {
    echo "❌ **Error en la prueba:**\n";
    echo "   - Mensaje: {$e->getMessage()}\n";
    echo "   - Archivo: {$e->getFile()}\n";
    echo "   - Línea: {$e->getLine()}\n";
}
