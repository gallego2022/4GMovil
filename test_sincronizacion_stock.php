<?php

/**
 * Script de Prueba: Sincronización de Stock entre Productos y Variantes
 * 
 * Este script demuestra cómo funciona el sistema de sincronización automática
 * del stock entre productos y sus variantes.
 */

require_once 'vendor/autoload.php';

use App\Models\Producto;
use App\Models\VarianteProducto;
use App\Services\StockSincronizacionService;

// Inicializar Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 PRUEBA DE SINCRONIZACIÓN DE STOCK\n";
echo "=====================================\n\n";

try {
    // 1. Obtener un producto con variantes para la prueba
    $producto = Producto::with('variantes')->first();
    
    if (!$producto) {
        echo "❌ No se encontraron productos en la base de datos\n";
        exit(1);
    }
    
    echo "📦 Producto seleccionado: {$producto->nombre_producto}\n";
    echo "   ID: {$producto->producto_id}\n";
    echo "   Stock actual: {$producto->stock}\n";
    echo "   Variantes: {$producto->variantes->count()}\n\n";
    
    // Mostrar variantes actuales
    echo "🎨 Variantes actuales:\n";
    foreach ($producto->variantes as $variante) {
        echo "   • {$variante->nombre}: {$variante->stock_disponible} unidades\n";
    }
    echo "\n";
    
    // 2. Sincronizar stock inicial
    echo "🔄 Sincronizando stock inicial...\n";
    $producto->sincronizarStockConVariantes();
    $stockDespuesSincronizacion = $producto->fresh()->stock;
    echo "   Stock después de sincronización: {$stockDespuesSincronizacion}\n\n";
    
    // 3. Simular venta de una variante
    $varianteParaVender = $producto->variantes->first();
    if ($varianteParaVender && $varianteParaVender->stock_disponible > 0) {
        $cantidadVenta = min(2, $varianteParaVender->stock_disponible);
        
        echo "💰 Simulando venta de {$cantidadVenta} unidades de {$varianteParaVender->nombre}...\n";
        echo "   Stock antes de venta: {$varianteParaVender->stock_disponible}\n";
        
        // Registrar venta (esto automáticamente sincroniza el producto padre)
        $varianteParaVender->registrarSalida($cantidadVenta, 'Venta de prueba', 1);
        
        echo "   Stock después de venta: {$varianteParaVender->fresh()->stock_disponible}\n";
        echo "   Stock del producto padre: {$producto->fresh()->stock}\n\n";
    }
    
    // 4. Simular entrada de stock
    echo "📥 Simulando entrada de stock...\n";
    $varianteParaEntrada = $producto->variantes->first();
    $cantidadEntrada = 5;
    
    echo "   Agregando {$cantidadEntrada} unidades a {$varianteParaEntrada->nombre}...\n";
    echo "   Stock antes de entrada: {$varianteParaEntrada->fresh()->stock_disponible}\n";
    
    // Registrar entrada (esto automáticamente sincroniza el producto padre)
    $varianteParaEntrada->registrarEntrada($cantidadEntrada, 'Compra de prueba', 1);
    
    echo "   Stock después de entrada: {$varianteParaEntrada->fresh()->stock_disponible}\n";
    echo "   Stock del producto padre: {$producto->fresh()->stock}\n\n";
    
    // 5. Probar el servicio de sincronización
    echo "🔧 Probando servicio de sincronización...\n";
    $service = new StockSincronizacionService();
    
    // Verificar integridad
    $integridad = $service->verificarIntegridadStock();
    echo "   Problemas detectados: {$integridad['total_problemas']}\n";
    echo "   Advertencias: {$integridad['total_advertencias']}\n\n";
    
    // Obtener reporte
    $reporte = $service->obtenerReporteSincronizacion();
    echo "📊 Reporte de sincronización:\n";
    echo "   Total productos: {$reporte['total_productos']}\n";
    echo "   Productos con variantes: {$reporte['productos_con_variantes']}\n";
    echo "   Productos sin variantes: {$reporte['productos_sin_variantes']}\n";
    echo "   Stock total del sistema: {$reporte['stock_total_sistema']}\n";
    echo "   Stock total de variantes: {$reporte['stock_total_variantes']}\n";
    echo "   Productos desincronizados: " . count($reporte['productos_desincronizados']) . "\n\n";
    
    // 6. Probar métodos del modelo
    echo "🧪 Probando métodos del modelo:\n";
    echo "   ¿Tiene variantes?: " . ($producto->tieneVariantes() ? 'Sí' : 'No') . "\n";
    echo "   Stock real: {$producto->stock_real}\n";
    echo "   Stock total variantes: {$producto->stock_total_variantes}\n";
    echo "   Stock disponible variantes: {$producto->stock_disponible_variantes}\n";
    echo "   ¿Necesita reposición?: " . ($producto->necesitaReposicionVariantes() ? 'Sí' : 'No') . "\n";
    echo "   Estado stock real: {$producto->estado_stock_real}\n\n";
    
    // 7. Mostrar variantes con stock bajo
    $variantesStockBajo = $producto->variantes_con_stock_bajo;
    if ($variantesStockBajo->count() > 0) {
        echo "⚠️  Variantes con stock bajo:\n";
        foreach ($variantesStockBajo as $variante) {
            echo "   • {$variante->nombre}: {$variante->stock_disponible} (mínimo: {$variante->stock_minimo})\n";
        }
        echo "\n";
    }
    
    // 8. Probar verificación de stock suficiente
    echo "✅ Probando verificación de stock:\n";
    $cantidadPrueba = 3;
    echo "   ¿Puede vender {$cantidadPrueba} unidades del producto?: " . 
         ($producto->tieneStockSuficienteReal($cantidadPrueba) ? 'Sí' : 'No') . "\n";
    
    if ($varianteParaVender) {
        echo "   ¿Puede vender {$cantidadPrueba} unidades de {$varianteParaVender->nombre}?: " . 
             ($varianteParaVender->tieneStockSuficiente($cantidadPrueba) ? 'Sí' : 'No') . "\n";
    }
    echo "\n";
    
    // 9. Resumen final
    echo "📋 RESUMEN FINAL:\n";
    echo "==================\n";
    echo "Producto: {$producto->nombre_producto}\n";
    echo "Stock final: {$producto->fresh()->stock}\n";
    echo "Variantes: {$producto->variantes->count()}\n";
    echo "Estado: {$producto->estado_stock_real}\n\n";
    
    echo "🎉 ¡Prueba completada exitosamente!\n";
    echo "El sistema de sincronización está funcionando correctamente.\n";
    
} catch (Exception $e) {
    echo "❌ Error durante la prueba: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
