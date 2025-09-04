<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== PRUEBA DE PEDIDO Y DIRECCIÓN ===\n";

try {
    // Obtener el último pedido
    $pedido = \App\Models\Pedido::with([
        'usuario', 
        'estado', 
        'pago.metodoPago', 
        'direccion',
        'detalles.producto'
    ])->orderBy('pedido_id', 'desc')->first();
    
    if (!$pedido) {
        echo "❌ No hay pedidos en el sistema\n";
        exit(1);
    }
    
    echo "✅ Pedido encontrado (ID: {$pedido->pedido_id})\n";
    echo "  - Total: $" . number_format($pedido->total, 0, ',', '.') . "\n";
    echo "  - Estado: {$pedido->estado->nombre}\n";
    echo "  - Usuario: {$pedido->usuario->nombre_usuario}\n";
    
    // Verificar dirección
    echo "\n=== VERIFICANDO DIRECCIÓN ===\n";
    if ($pedido->direccion) {
        echo "✅ Dirección encontrada:\n";
        echo "  - ID: {$pedido->direccion->direccion_id}\n";
        echo "  - Tipo: {$pedido->direccion->tipo_direccion}\n";
        echo "  - Dirección: {$pedido->direccion->direccion}\n";
        echo "  - Barrio: {$pedido->direccion->barrio}\n";
        echo "  - Ciudad: {$pedido->direccion->ciudad}\n";
        echo "  - Departamento: {$pedido->direccion->departamento}\n";
        echo "  - Código Postal: {$pedido->direccion->codigo_postal}\n";
        echo "  - Instrucciones: " . ($pedido->direccion->instrucciones ?? 'Sin instrucciones') . "\n";
    } else {
        echo "❌ No hay dirección asociada al pedido\n";
        echo "  - direccion_id en pedido: {$pedido->direccion_id}\n";
        
        // Verificar si existe la dirección en la tabla
        if ($pedido->direccion_id) {
            $direccion = \App\Models\Direccion::find($pedido->direccion_id);
            if ($direccion) {
                echo "✅ Dirección existe en la tabla:\n";
                echo "  - Tipo: {$direccion->tipo_direccion}\n";
                echo "  - Dirección: {$direccion->direccion}\n";
                echo "  - Barrio: {$direccion->barrio}\n";
            } else {
                echo "❌ Dirección no encontrada en la tabla con ID: {$pedido->direccion_id}\n";
            }
        }
    }
    
    // Verificar relaciones cargadas
    echo "\n=== VERIFICANDO RELACIONES ===\n";
    echo "  - Usuario cargado: " . ($pedido->relationLoaded('usuario') ? '✅' : '❌') . "\n";
    echo "  - Estado cargado: " . ($pedido->relationLoaded('estado') ? '✅' : '❌') . "\n";
    echo "  - Dirección cargada: " . ($pedido->relationLoaded('direccion') ? '✅' : '❌') . "\n";
    echo "  - Pago cargado: " . ($pedido->relationLoaded('pago') ? '✅' : '❌') . "\n";
    echo "  - Detalles cargados: " . ($pedido->relationLoaded('detalles') ? '✅' : '❌') . "\n";
    
    // Verificar datos del pedido
    echo "\n=== DATOS DEL PEDIDO ===\n";
    echo "  - direccion_id: {$pedido->direccion_id}\n";
    echo "  - usuario_id: {$pedido->usuario_id}\n";
    echo "  - estado_id: {$pedido->estado_id}\n";
    echo "  - fecha_pedido: {$pedido->fecha_pedido}\n";
    
} catch (Exception $e) {
    echo "❌ Error durante la prueba: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== FIN DE PRUEBA ===\n";
