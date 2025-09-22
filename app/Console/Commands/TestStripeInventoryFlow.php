<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pedido;
use App\Models\MovimientoInventario;
use Illuminate\Support\Facades\Log;

class TestStripeInventoryFlow extends Command
{
    protected $signature = 'test:stripe-inventory-flow {pedido_id}';
    protected $description = 'Probar el flujo de movimientos de inventario para un pedido con Stripe';

    public function handle()
    {
        $pedidoId = $this->argument('pedido_id');
        
        $this->info("Probando flujo de inventario para pedido #{$pedidoId}");
        
        // Buscar el pedido
        $pedido = Pedido::with(['detalles.producto', 'detalles.variante', 'estado'])->find($pedidoId);
        
        if (!$pedido) {
            $this->error("Pedido #{$pedidoId} no encontrado");
            return 1;
        }
        
        $this->info("Pedido encontrado:");
        $this->info("- ID: {$pedido->pedido_id}");
        $this->info("- Estado: {$pedido->estado->nombre} (ID: {$pedido->estado_id})");
        $this->info("- Total detalles: " . $pedido->detalles->count());
        
        // Mostrar detalles del pedido
        foreach ($pedido->detalles as $detalle) {
            $this->info("Detalle:");
            $this->info("  - Producto ID: {$detalle->producto_id}");
            $this->info("  - Variante ID: " . ($detalle->variante_id ?? 'N/A'));
            $this->info("  - Cantidad: {$detalle->cantidad}");
            $this->info("  - Producto cargado: " . ($detalle->producto ? 'Sí' : 'No'));
            $this->info("  - Variante cargada: " . ($detalle->variante ? 'Sí' : 'No'));
        }
        
        // Verificar movimientos existentes
        $movimientosProducto = MovimientoInventario::where('pedido_id', $pedidoId)->get();
        $movimientosVariante = MovimientoInventario::whereNotNull('variante_id')->whereHas('variante', function($query) use ($pedidoId) {
            $query->whereHas('producto', function($q) use ($pedidoId) {
                $q->whereHas('detallesPedido', function($dq) use ($pedidoId) {
                    $dq->where('pedido_id', $pedidoId);
                });
            });
        })->get();
        
        $this->info("Movimientos de inventario existentes:");
        $this->info("- Movimientos de producto: " . $movimientosProducto->count());
        $this->info("- Movimientos de variante: " . $movimientosVariante->count());
        
        if ($movimientosProducto->count() > 0) {
            $this->info("Movimientos de producto:");
            foreach ($movimientosProducto as $movimiento) {
                $this->info("  - Tipo: {$movimiento->tipo_movimiento}");
                $this->info("  - Cantidad: {$movimiento->cantidad}");
                $this->info("  - Motivo: {$movimiento->motivo}");
                $this->info("  - Fecha: {$movimiento->fecha_movimiento}");
            }
        }
        
        if ($movimientosVariante->count() > 0) {
            $this->info("Movimientos de variante:");
            foreach ($movimientosVariante as $movimiento) {
                $this->info("  - Tipo: {$movimiento->tipo}");
                $this->info("  - Cantidad: {$movimiento->cantidad}");
                $this->info("  - Motivo: {$movimiento->motivo}");
                $this->info("  - Fecha: {$movimiento->fecha_movimiento}");
            }
        }
        
        // Simular el flujo de confirmación de Stripe
        if ($pedido->estado_id == 1) { // Pendiente
            $this->info("Simulando confirmación de Stripe...");
            
            $estadoAnterior = $pedido->estado_id;
            $pedido->update(['estado_id' => 2]); // Confirmado
            $pedido->refresh();
            
            $this->info("Estado actualizado a: {$pedido->estado->nombre}");
            
            // Simular el manejo de stock
            foreach ($pedido->detalles as $detalle) {
                $producto = $detalle->producto;
                
                if ($detalle->variante_id) {
                    $variante = $detalle->variante;
                    if ($variante) {
                        $this->info("Registrando salida para variante {$variante->variante_id}...");
                        $resultado = $variante->registrarSalida(
                            $detalle->cantidad,
                            "Venta confirmada - Pedido #{$pedido->pedido_id} - Stripe (Simulado)",
                            auth()->id() ?? 1,
                            "Pedido #{$pedido->pedido_id}"
                        );
                        $this->info("Resultado: " . ($resultado ? 'Exitoso' : 'Falló'));
                    }
                } else {
                    $this->info("Registrando salida para producto {$producto->producto_id}...");
                    $resultado = $producto->registrarSalida(
                        $detalle->cantidad,
                        "Venta confirmada - Pedido #{$pedido->pedido_id} - Stripe (Simulado)",
                        auth()->id() ?? 1,
                        $pedido->pedido_id
                    );
                    $this->info("Resultado: " . ($resultado ? 'Exitoso' : 'Falló'));
                }
            }
            
            // Verificar movimientos después de la simulación
            $movimientosProductoDespues = MovimientoInventario::where('pedido_id', $pedidoId)->get();
            $movimientosVarianteDespues = MovimientoInventario::whereNotNull('variante_id')->whereHas('variante', function($query) use ($pedidoId) {
                $query->whereHas('producto', function($q) use ($pedidoId) {
                    $q->whereHas('detallesPedido', function($dq) use ($pedidoId) {
                        $dq->where('pedido_id', $pedidoId);
                    });
                });
            })->get();
            
            $this->info("Movimientos después de la simulación:");
            $this->info("- Movimientos de producto: " . $movimientosProductoDespues->count());
            $this->info("- Movimientos de variante: " . $movimientosVarianteDespues->count());
        }
        
        return 0;
    }
}
