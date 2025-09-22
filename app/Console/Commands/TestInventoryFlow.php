<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pedido;
use App\Services\StripeService;

class TestInventoryFlow extends Command
{
    protected $signature = 'test:inventory-flow {pedido_id}';
    protected $description = 'Probar el flujo de inventario para un pedido específico';

    public function handle()
    {
        $pedidoId = $this->argument('pedido_id');
        
        $this->info("🧪 Probando flujo de inventario para pedido {$pedidoId}...");
        
        $pedido = Pedido::with(['detalles.producto', 'detalles.variante'])->find($pedidoId);
        
        if (!$pedido) {
            $this->error("❌ Pedido {$pedidoId} no encontrado");
            return 1;
        }
        
        $this->info("✅ Pedido encontrado:");
        $this->info("   - Estado actual: {$pedido->estado_id}");
        $this->info("   - Usuario: " . ($pedido->usuario->nombre ?? 'N/A'));
        $this->info("   - Total: $" . number_format($pedido->total, 0, ',', '.'));
        $this->info("   - Detalles: " . $pedido->detalles->count());
        
        foreach ($pedido->detalles as $detalle) {
            $this->info("   - Producto: {$detalle->producto->nombre_producto}");
            if ($detalle->variante_id) {
                $this->info("     - Variante: {$detalle->variante->nombre}");
            }
            $this->info("     - Cantidad: {$detalle->cantidad}");
        }
        
        // Simular cambio de estado
        $estadoAnterior = $pedido->estado_id;
        $nuevoEstado = 2; // Confirmado
        
        $this->info("🔄 Simulando cambio de estado de {$estadoAnterior} a {$nuevoEstado}...");
        
        // Usar reflexión para acceder al método privado
        $stripeService = new StripeService();
        $reflection = new \ReflectionClass($stripeService);
        $method = $reflection->getMethod('manejarStockReservado');
        $method->setAccessible(true);
        
        try {
            $method->invoke($stripeService, $pedido, $estadoAnterior, $nuevoEstado);
            $this->info("✅ Método manejarStockReservado ejecutado exitosamente");
        } catch (\Exception $e) {
            $this->error("❌ Error al ejecutar manejarStockReservado: " . $e->getMessage());
            $this->error("Trace: " . $e->getTraceAsString());
            return 1;
        }
        
        // Verificar movimientos de inventario
        $movimientosInventario = \App\Models\MovimientoInventario::where('pedido_id', $pedidoId)->count();
        $movimientosVariantes = \App\Models\MovimientoInventario::whereNotNull('variante_id')->whereHas('variante', function($query) use ($pedidoId) {
            $query->whereHas('detallesPedido', function($q) use ($pedidoId) {
                $q->where('pedido_id', $pedidoId);
            });
        })->count();
        
        $this->info("📊 Resultados:");
        $this->info("   - Movimientos de inventario: {$movimientosInventario}");
        $this->info("   - Movimientos de variantes: {$movimientosVariantes}");
        
        return 0;
    }
}
