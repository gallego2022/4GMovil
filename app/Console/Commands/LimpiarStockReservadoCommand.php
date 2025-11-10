<?php

namespace App\Console\Commands;

use App\Models\Pedido;
use App\Services\ReservaStockService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class LimpiarStockReservadoCommand extends Command
{
    protected $signature = 'inventario:limpiar-stock-reservado 
                            {--dias=7 : Días después de los cuales limpiar stock reservado}
                            {--forzar : Forzar limpieza sin confirmación}';

    protected $description = 'Limpiar stock reservado de pedidos antiguos o cancelados';

    public function handle()
    {
        $dias = (int) $this->option('dias');
        $forzar = $this->option('forzar');

        $this->info('🔍 Iniciando limpieza de stock reservado...');
        $this->info("📅 Limpiando pedidos con más de {$dias} días de antigüedad");

        $fechaLimite = Carbon::now()->subDays($dias);

        // Obtener pedidos pendientes antiguos
        $pedidosAntiguos = Pedido::with(['detalles.producto', 'detalles.variante'])
            ->where('estado_id', 1) // Pendiente
            ->where('fecha_pedido', '<', $fechaLimite)
            ->get();

        // Obtener pedidos cancelados
        $pedidosCancelados = Pedido::with(['detalles.producto', 'detalles.variante'])
            ->whereIn('estado_id', [3]) // Cancelado
            ->get();

        $totalPedidos = $pedidosAntiguos->count() + $pedidosCancelados->count();

        if ($totalPedidos === 0) {
            $this->info('✅ No hay pedidos que requieran limpieza de stock reservado.');

            return 0;
        }

        $this->info("📊 Encontrados {$totalPedidos} pedidos para limpiar:");
        $this->info("   • {$pedidosAntiguos->count()} pedidos pendientes antiguos");
        $this->info("   • {$pedidosCancelados->count()} pedidos cancelados/rechazados");

        if (! $forzar) {
            if (! $this->confirm('¿Deseas continuar con la limpieza?')) {
                $this->info('❌ Operación cancelada.');

                return 0;
            }
        }

        $productosAfectados = [];
        $stockLiberado = 0;
        $reservaStockService = new ReservaStockService;

        // Procesar pedidos antiguos
        foreach ($pedidosAntiguos as $pedido) {
            $this->line("🔄 Procesando pedido #{$pedido->pedido_id} (antiguo)");

            // Verificar si el pedido tiene variantes con reservas activas
            $tieneVariantes = $pedido->detalles->contains(function ($detalle) {
                return $detalle->variante_id !== null;
            });

            if ($tieneVariantes) {
                // Cancelar todas las reservas activas del pedido
                try {
                    $reservaStockService->cancelarReservasPedido(
                        $pedido->pedido_id,
                        null,
                        "Limpieza automática - Pedido antiguo #{$pedido->pedido_id}"
                    );
                    $this->line('   ✅ Reservas de variantes canceladas');
                } catch (\Exception $e) {
                    $this->warn("   ⚠️  Error al cancelar reservas de variantes: {$e->getMessage()}");
                }
            }

            foreach ($pedido->detalles as $detalle) {
                $producto = $detalle->producto;

                // Solo procesar productos sin variantes (las variantes ya se procesaron arriba)
                if (! $detalle->variante_id) {
                    $cantidadReservada = $producto->stock_reservado ?? 0;

                    if ($cantidadReservada > 0) {
                        $producto->liberarStockReservado(
                            $detalle->cantidad,
                            "Limpieza automática - Pedido antiguo #{$pedido->pedido_id}",
                            null,
                            $pedido->pedido_id
                        );

                        $productosAfectados[$producto->producto_id] = $producto->nombre_producto;
                        $stockLiberado += $detalle->cantidad;

                        $this->line("   ✅ Liberado {$detalle->cantidad} unidades de {$producto->nombre_producto}");
                    }
                } else {
                    // Para variantes, ya se cancelaron las reservas arriba
                    $productosAfectados[$producto->producto_id] = $producto->nombre_producto;
                    $stockLiberado += $detalle->cantidad;
                }
            }

            // Cambiar estado a cancelado
            $pedido->estado_id = 3; // Cancelado
            $pedido->save();
            $this->line('   📝 Estado cambiado a Cancelado');
        }

        // Procesar pedidos cancelados/rechazados
        foreach ($pedidosCancelados as $pedido) {
            $this->line("🔄 Procesando pedido #{$pedido->pedido_id} (cancelado)");

            // Verificar si el pedido tiene variantes con reservas activas
            $tieneVariantes = $pedido->detalles->contains(function ($detalle) {
                return $detalle->variante_id !== null;
            });

            if ($tieneVariantes) {
                // Cancelar todas las reservas activas del pedido
                try {
                    $reservaStockService->cancelarReservasPedido(
                        $pedido->pedido_id,
                        null,
                        "Limpieza automática - Pedido cancelado #{$pedido->pedido_id}"
                    );
                    $this->line('   ✅ Reservas de variantes canceladas');
                } catch (\Exception $e) {
                    $this->warn("   ⚠️  Error al cancelar reservas de variantes: {$e->getMessage()}");
                }
            }

            foreach ($pedido->detalles as $detalle) {
                $producto = $detalle->producto;

                // Solo procesar productos sin variantes (las variantes ya se procesaron arriba)
                if (! $detalle->variante_id) {
                    $cantidadReservada = $producto->stock_reservado ?? 0;

                    if ($cantidadReservada > 0) {
                        $producto->liberarStockReservado(
                            $detalle->cantidad,
                            "Limpieza automática - Pedido cancelado #{$pedido->pedido_id}",
                            null,
                            $pedido->pedido_id
                        );

                        $productosAfectados[$producto->producto_id] = $producto->nombre_producto;
                        $stockLiberado += $detalle->cantidad;

                        $this->line("   ✅ Liberado {$detalle->cantidad} unidades de {$producto->nombre_producto}");
                    }
                } else {
                    // Para variantes, ya se cancelaron las reservas arriba
                    $productosAfectados[$producto->producto_id] = $producto->nombre_producto;
                    $stockLiberado += $detalle->cantidad;
                }
            }
        }

        $this->newLine();
        $this->info('📊 RESUMEN DE LIMPIEZA');
        $this->info('======================');
        $this->info("• Pedidos procesados: {$totalPedidos}");
        $this->info('• Productos afectados: '.count($productosAfectados));
        $this->info("• Stock liberado: {$stockLiberado} unidades");

        if (! empty($productosAfectados)) {
            $this->info('• Productos afectados:');
            foreach ($productosAfectados as $productoId => $nombre) {
                $this->line("  - {$nombre} (ID: {$productoId})");
            }
        }

        $this->info('✅ Limpieza de stock reservado completada.');

        Log::info('Limpieza de stock reservado completada', [
            'pedidos_procesados' => $totalPedidos,
            'productos_afectados' => count($productosAfectados),
            'stock_liberado' => $stockLiberado,
            'dias_limite' => $dias,
        ]);

        return 0;
    }

    /**
     * Manejar el stock reservado según el cambio de estado del pedido
     */
    private function manejarStockReservado(Pedido $pedido, int $estadoAnterior, int $nuevoEstado): void
    {
        // Estados que confirman la venta (liberan stock reservado y registran salida)
        $estadosConfirmados = [2]; // Confirmado

        // Estados que cancelan la venta (liberan stock reservado sin registrar salida)
        $estadosCancelados = [3]; // Cancelado

        // Estados pendientes (mantienen stock reservado)
        $estadosPendientes = [1]; // Pendiente

        Log::info('Manejando stock reservado', [
            'pedido_id' => $pedido->pedido_id,
            'estado_anterior' => $estadoAnterior,
            'nuevo_estado' => $nuevoEstado,
        ]);

        foreach ($pedido->detalles as $detalle) {
            $producto = $detalle->producto;

            // Si el pedido se confirma (pasa de pendiente a confirmado)
            if (in_array($estadoAnterior, $estadosPendientes) && in_array($nuevoEstado, $estadosConfirmados)) {
                // Liberar stock reservado y registrar salida real
                $producto->liberarStockReservado(
                    $detalle->cantidad,
                    "Confirmación de pedido #{$pedido->pedido_id}",
                    \Illuminate\Support\Facades\Auth::id(),
                    $pedido->pedido_id
                );

                $producto->registrarSalida(
                    $detalle->cantidad,
                    "Venta confirmada - Pedido #{$pedido->pedido_id}",
                    \Illuminate\Support\Facades\Auth::id(),
                    $pedido->pedido_id
                );

                Log::info('Stock confirmado y registrada salida', [
                    'producto_id' => $producto->producto_id,
                    'cantidad' => $detalle->cantidad,
                    'pedido_id' => $pedido->pedido_id,
                ]);
            }
            // Si el pedido se cancela (pasa de pendiente a cancelado)
            elseif (in_array($estadoAnterior, $estadosPendientes) && in_array($nuevoEstado, $estadosCancelados)) {
                // Solo liberar stock reservado sin registrar salida
                $producto->liberarStockReservado(
                    $detalle->cantidad,
                    "Cancelación de pedido #{$pedido->pedido_id}",
                    \Illuminate\Support\Facades\Auth::id(),
                    $pedido->pedido_id
                );

                Log::info('Stock liberado por cancelación', [
                    'producto_id' => $producto->producto_id,
                    'cantidad' => $detalle->cantidad,
                    'pedido_id' => $pedido->pedido_id,
                ]);
            }
            // Si el pedido se cancela después de estar confirmado
            elseif (in_array($estadoAnterior, $estadosConfirmados) && in_array($nuevoEstado, $estadosCancelados)) {
                // Registrar entrada para compensar la salida ya registrada
                $producto->registrarEntrada(
                    $detalle->cantidad,
                    "Devolución por cancelación - Pedido #{$pedido->pedido_id}",
                    \Illuminate\Support\Facades\Auth::id(),
                    "Pedido #{$pedido->pedido_id}"
                );

                Log::info('Stock devuelto por cancelación post-confirmación', [
                    'producto_id' => $producto->producto_id,
                    'cantidad' => $detalle->cantidad,
                    'pedido_id' => $pedido->pedido_id,
                ]);
            }
        }
    }
}
