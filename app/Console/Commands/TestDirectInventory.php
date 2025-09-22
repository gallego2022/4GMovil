<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pedido;
use App\Models\Pago;
use App\Services\StripeService;

class TestDirectInventory extends Command
{
    protected $signature = 'test:direct-inventory {pedido_id}';
    protected $description = 'Probar directamente el manejo de inventario';

    public function handle()
    {
        $pedidoId = $this->argument('pedido_id');
        
        $this->info("🧪 Probando manejo directo de inventario para pedido {$pedidoId}...");
        
        $pedido = Pedido::with(['detalles.producto', 'detalles.variante'])->find($pedidoId);
        
        if (!$pedido) {
            $this->error("❌ Pedido {$pedidoId} no encontrado");
            return 1;
        }
        
        $this->info("✅ Pedido encontrado:");
        $this->info("   - Estado actual: {$pedido->estado_id}");
        $this->info("   - Detalles: " . $pedido->detalles->count());
        
        foreach ($pedido->detalles as $detalle) {
            $this->info("   - Producto: {$detalle->producto->nombre_producto}");
            if ($detalle->variante_id) {
                $this->info("     - Variante: {$detalle->variante->nombre}");
            }
            $this->info("     - Cantidad: {$detalle->cantidad}");
        }
        
        // Llamar directamente al método manejarStockReservado
        $stripeService = new StripeService();
        $reflection = new \ReflectionClass($stripeService);
        $method = $reflection->getMethod('manejarStockReservado');
        $method->setAccessible(true);
        
        try {
            $this->info("🔄 Llamando directamente a manejarStockReservado...");
            $method->invoke($stripeService, $pedido, 1, 2); // De pendiente a confirmado
            $this->info("✅ manejarStockReservado ejecutado exitosamente");
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
