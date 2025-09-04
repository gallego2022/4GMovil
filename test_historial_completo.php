<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== PRUEBA DE HISTORIAL COMPLETO ===\n";

try {
    // Crear una instancia del controlador
    $pedidoService = new \App\Services\Business\PedidoService();
    $controller = new \App\Http\Controllers\Admin\PedidoController($pedidoService);
    
    // Crear una request simulada
    $request = new \Illuminate\Http\Request();
    
    // Llamar al método historial
    echo "✅ Llamando al método historial...\n";
    $response = $controller->historial($request);
    
    echo "✅ Método historial ejecutado correctamente\n";
    echo "Tipo de respuesta: " . get_class($response) . "\n";
    
    // Probar también el método index
    echo "\n✅ Llamando al método index...\n";
    $responseIndex = $controller->index($request);
    
    echo "✅ Método index ejecutado correctamente\n";
    echo "Tipo de respuesta: " . get_class($responseIndex) . "\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== FIN DE PRUEBA ===\n";
