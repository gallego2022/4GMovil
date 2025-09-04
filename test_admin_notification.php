<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== PRUEBA DE NOTIFICACIÓN AL ADMINISTRADOR ===\n";

try {
    // Verificar usuario administrador
    $admin = \App\Models\Usuario::where('rol', 'admin')->where('estado', 1)->first();
    if (!$admin) {
        echo "❌ No hay usuario administrador activo\n";
        exit(1);
    }
    
    echo "✅ Usuario administrador encontrado:\n";
    echo "  - ID: {$admin->usuario_id}\n";
    echo "  - Nombre: {$admin->nombre_usuario}\n";
    echo "  - Email: {$admin->correo_electronico}\n";
    
    // Verificar último pedido
    $ultimoPedido = \App\Models\Pedido::orderBy('created_at', 'desc')->first();
    if (!$ultimoPedido) {
        echo "❌ No hay pedidos en el sistema\n";
        exit(1);
    }
    
    echo "\n✅ Último pedido encontrado:\n";
    echo "  - ID: {$ultimoPedido->pedido_id}\n";
    echo "  - Total: $" . number_format($ultimoPedido->total, 0, ',', '.') . "\n";
    echo "  - Fecha: " . ($ultimoPedido->fecha_pedido instanceof \Carbon\Carbon ? 
        $ultimoPedido->fecha_pedido->format('d/m/Y H:i') : 
        \Carbon\Carbon::parse($ultimoPedido->fecha_pedido)->format('d/m/Y H:i')) . "\n";
    
    // Verificar que el campo fecha_pedido sea Carbon
    if ($ultimoPedido->fecha_pedido instanceof \Carbon\Carbon) {
        echo "✅ Campo fecha_pedido es objeto Carbon\n";
    } else {
        echo "⚠️ Campo fecha_pedido NO es objeto Carbon, se convertirá automáticamente\n";
    }
    
    // Probar el servicio de notificación
    echo "\n=== PROBANDO SERVICIO DE NOTIFICACIÓN ===\n";
    
    try {
        $notificationService = new \App\Services\AdminNotificationService();
        $result = $notificationService->notificarPedidoNuevo($ultimoPedido, 'Stripe');
        
        if ($result) {
            echo "✅ Notificación enviada exitosamente\n";
        } else {
            echo "❌ Error al enviar notificación\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Error en servicio de notificación: " . $e->getMessage() . "\n";
    }
    
    echo "\n✅ Prueba completada\n";
    
} catch (Exception $e) {
    echo "❌ Error durante la prueba: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== FIN DE PRUEBA ===\n";
