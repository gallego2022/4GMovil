<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pedido;
use App\Models\MovimientoInventario;
use App\Models\MovimientoInventarioVariante;

class CheckInventoryMovements extends Command
{
    protected $signature = 'inventory:check-movements {pedido_id}';
    protected $description = 'Verificar movimientos de inventario para un pedido específico';

    public function handle()
    {
        $pedidoId = $this->argument('pedido_id');
        
        $this->info("Verificando movimientos de inventario para pedido #{$pedidoId}");
        
        // Buscar el pedido
        $pedido = Pedido::with(['detalles.producto', 'detalles.variante', 'estado'])->find($pedidoId);
        
        if (!$pedido) {
            $this->error("Pedido #{$pedidoId} no encontrado");
            return 1;
        }
        
        $this->info("Pedido encontrado:");
        $this->info("- ID: {$pedido->pedido_id}");
        $this->info("- Estado: {$pedido->estado->nombre} (ID: {$pedido->estado_id})");
        $this->info("- Total: $" . number_format($pedido->total, 0, ',', '.'));
        $this->info("- Fecha: {$pedido->fecha_pedido}");
        
        // Mostrar detalles del pedido
        $this->info("\nDetalles del pedido:");
        foreach ($pedido->detalles as $detalle) {
            $this->info("- Producto ID: {$detalle->producto_id}");
            $this->info("  - Nombre: " . ($detalle->producto->nombre_producto ?? 'N/A'));
            $this->info("  - Variante ID: " . ($detalle->variante_id ?? 'N/A'));
            $this->info("  - Cantidad: {$detalle->cantidad}");
            $this->info("  - Precio unitario: $" . number_format($detalle->precio_unitario, 0, ',', '.'));
        }
        
        // Verificar movimientos de inventario de productos
        $movimientosProducto = MovimientoInventario::where('pedido_id', $pedidoId)->get();
        $this->info("\nMovimientos de inventario de productos: " . $movimientosProducto->count());
        
        if ($movimientosProducto->count() > 0) {
            foreach ($movimientosProducto as $movimiento) {
                $this->info("- Tipo: {$movimiento->tipo_movimiento}");
                $this->info("  - Cantidad: {$movimiento->cantidad}");
                $this->info("  - Motivo: {$movimiento->motivo}");
                $this->info("  - Fecha: {$movimiento->fecha_movimiento}");
                $this->info("  - Usuario ID: {$movimiento->usuario_id}");
            }
        }
        
        // Verificar movimientos de inventario de variantes
        $movimientosVariante = MovimientoInventarioVariante::whereHas('variante', function($query) use ($pedidoId) {
            $query->whereHas('producto', function($q) use ($pedidoId) {
                $q->whereHas('detallesPedido', function($dq) use ($pedidoId) {
                    $dq->where('pedido_id', $pedidoId);
                });
            });
        })->get();
        
        $this->info("\nMovimientos de inventario de variantes: " . $movimientosVariante->count());
        
        if ($movimientosVariante->count() > 0) {
            foreach ($movimientosVariante as $movimiento) {
                $this->info("- Tipo: {$movimiento->tipo}");
                $this->info("  - Cantidad: {$movimiento->cantidad}");
                $this->info("  - Motivo: {$movimiento->motivo}");
                $this->info("  - Fecha: {$movimiento->fecha_movimiento}");
                $this->info("  - Usuario ID: {$movimiento->usuario_id}");
            }
        }
        
        // Verificar si hay movimientos relacionados con Stripe
        $movimientosStripe = MovimientoInventario::where('motivo', 'like', '%Stripe%')->get();
        $this->info("\nMovimientos relacionados con Stripe: " . $movimientosStripe->count());
        
        if ($movimientosStripe->count() > 0) {
            foreach ($movimientosStripe as $movimiento) {
                $this->info("- Pedido ID: {$movimiento->pedido_id}");
                $this->info("  - Tipo: {$movimiento->tipo_movimiento}");
                $this->info("  - Cantidad: {$movimiento->cantidad}");
                $this->info("  - Motivo: {$movimiento->motivo}");
            }
        }
        
        return 0;
    }
}
