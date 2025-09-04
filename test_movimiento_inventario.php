<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== PRUEBA DEL MODELO MOVIMIENTO INVENTARIO CORREGIDO ===\n";

try {
    // Verificar que la tabla existe
    if (\Illuminate\Support\Facades\Schema::hasTable('movimientos_inventario')) {
        echo "✅ Tabla movimientos_inventario existe\n";
        $columns = \Illuminate\Support\Facades\Schema::getColumnListing('movimientos_inventario');
        echo "Columnas disponibles:\n";
        foreach ($columns as $column) {
            echo "  - $column\n";
        }
    } else {
        echo "❌ Tabla movimientos_inventario NO existe\n";
        exit(1);
    }
    
    // Verificar que el modelo se puede instanciar
    $movimiento = new \App\Models\MovimientoInventario();
    echo "\n✅ Modelo MovimientoInventario instanciado correctamente\n";
    
    // Verificar campos fillable
    $fillable = $movimiento->getFillable();
    echo "Campos fillable:\n";
    foreach ($fillable as $campo) {
        echo "  - $campo\n";
    }
    
    // Verificar que se puede crear un movimiento
    echo "\n=== PROBANDO CREACIÓN DE MOVIMIENTO ===\n";
    
    try {
        $nuevoMovimiento = \App\Models\MovimientoInventario::create([
            'producto_id' => 1,
            'tipo' => 'salida',
            'cantidad' => 1,
            'motivo' => 'Prueba de sistema',
            'usuario_id' => 1,
            'referencia' => 'TEST_' . time(),
            'fecha_movimiento' => now()
        ]);
        
        echo "✅ Movimiento creado exitosamente (ID: {$nuevoMovimiento->movimiento_id})\n";
        
        // Verificar que se puede recuperar
        $movimientoRecuperado = \App\Models\MovimientoInventario::find($nuevoMovimiento->movimiento_id);
        if ($movimientoRecuperado) {
            echo "✅ Movimiento recuperado correctamente\n";
            echo "  - Tipo: {$movimientoRecuperado->tipo}\n";
            echo "  - Cantidad: {$movimientoRecuperado->cantidad}\n";
            echo "  - Motivo: {$movimientoRecuperado->motivo}\n";
        } else {
            echo "❌ No se pudo recuperar el movimiento\n";
        }
        
        // Limpiar el movimiento de prueba
        $nuevoMovimiento->delete();
        echo "✅ Movimiento de prueba eliminado\n";
        
    } catch (Exception $e) {
        echo "❌ Error al crear movimiento: " . $e->getMessage() . "\n";
    }
    
    echo "\n✅ Prueba del modelo completada exitosamente\n";
    
} catch (Exception $e) {
    echo "❌ Error durante la prueba: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== FIN DE PRUEBA ===\n";
