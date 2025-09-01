<?php

namespace App\Services;

use App\Models\ReservaStockVariante;
use App\Models\VarianteProducto;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ReservaStockService
{
    /**
     * Crear reservas para un carrito de compras
     */
    public function crearReservasCarrito(array $carrito, int $usuarioId, ?string $pedidoId = null): array
    {
        $reservas = [];
        $errores = [];

        DB::beginTransaction();
        try {
            foreach ($carrito as $item) {
                $productoId = $item['id'];
                $varianteId = $item['variante_id'] ?? null;
                $cantidad = $item['quantity'];

                if ($varianteId) {
                    // Producto con variante
                    $reserva = $this->crearReservaVariante(
                        $varianteId,
                        $usuarioId,
                        $cantidad,
                        $pedidoId,
                        "Reserva de compra - Producto #{$productoId}"
                    );

                    if ($reserva) {
                        $reservas[] = $reserva;
                    } else {
                        $variante = VarianteProducto::find($varianteId);
                        $errores[] = "Stock insuficiente para la variante '{$variante->nombre}' del producto #{$productoId}";
                    }
                } else {
                    // Producto sin variante (usar el sistema actual)
                    $producto = Producto::find($productoId);
                    if (!$producto) {
                        $errores[] = "Producto #{$productoId} no encontrado";
                        continue;
                    }

                    if (!$producto->tieneStockSuficiente($cantidad)) {
                        $errores[] = "Stock insuficiente para el producto '{$producto->nombre_producto}'";
                        continue;
                    }

                    // Usar el sistema de reserva existente del producto
                    // Solo pasar pedido_id si es un número entero
                    $pedidoIdInt = is_numeric($pedidoId) ? (int) $pedidoId : null;
                    $reservaExitosa = $producto->reservarStock(
                        $cantidad,
                        "Reserva - Pedido #{$pedidoId}",
                        $usuarioId,
                        $pedidoIdInt
                    );

                    if (!$reservaExitosa) {
                        $errores[] = "No se pudo reservar stock para el producto '{$producto->nombre_producto}'";
                    }
                }
            }

            if (!empty($errores)) {
                DB::rollBack();
                return ['reservas' => [], 'errores' => $errores];
            }

            DB::commit();
            return ['reservas' => $reservas, 'errores' => []];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear reservas de carrito: ' . $e->getMessage(), [
                'carrito' => $carrito,
                'usuario_id' => $usuarioId,
                'pedido_id' => $pedidoId
            ]);
            return ['reservas' => [], 'errores' => ['Error interno del sistema']];
        }
    }

    /**
     * Crear reserva para una variante específica
     */
    public function crearReservaVariante(
        int $varianteId,
        int $usuarioId,
        int $cantidad,
        ?string $pedidoId = null,
        string $motivo = 'Reserva de compra',
        int $minutosExpiracion = 30
    ): ?ReservaStockVariante {
        return ReservaStockVariante::crearReserva(
            $varianteId,
            $usuarioId,
            $cantidad,
            $pedidoId,
            $motivo,
            $minutosExpiracion
        );
    }

    /**
     * Confirmar reservas de un pedido
     */
    public function confirmarReservasPedido(string $pedidoId, int $usuarioId): bool
    {
        return DB::transaction(function () use ($pedidoId, $usuarioId) {
            $reservas = ReservaStockVariante::where('referencia_pedido', $pedidoId)
                ->where('estado', ReservaStockVariante::ESTADO_ACTIVA)
                ->get();

            foreach ($reservas as $reserva) {
                if (!$reserva->confirmar()) {
                    Log::error('No se pudo confirmar reserva', [
                        'reserva_id' => $reserva->reserva_id,
                        'pedido_id' => $pedidoId
                    ]);
                    throw new \Exception("No se pudo confirmar la reserva #{$reserva->reserva_id}");
                }
            }

            Log::info('Reservas confirmadas para pedido', [
                'pedido_id' => $pedidoId,
                'reservas_count' => $reservas->count()
            ]);

            return true;
        });
    }

    /**
     * Cancelar reservas de un pedido
     */
    public function cancelarReservasPedido(string $pedidoId, int $usuarioId, string $motivo = 'Cancelación de pedido'): bool
    {
        return DB::transaction(function () use ($pedidoId, $usuarioId, $motivo) {
            $reservas = ReservaStockVariante::where('referencia_pedido', $pedidoId)
                ->where('estado', ReservaStockVariante::ESTADO_ACTIVA)
                ->get();

            foreach ($reservas as $reserva) {
                if (!$reserva->cancelar($motivo)) {
                    Log::error('No se pudo cancelar reserva', [
                        'reserva_id' => $reserva->reserva_id,
                        'pedido_id' => $pedidoId
                    ]);
                    throw new \Exception("No se pudo cancelar la reserva #{$reserva->reserva_id}");
                }
            }

            Log::info('Reservas canceladas para pedido', [
                'pedido_id' => $pedidoId,
                'reservas_count' => $reservas->count(),
                'motivo' => $motivo
            ]);

            return true;
        });
    }

    /**
     * Expirar reservas automáticamente
     */
    public function expirarReservasAutomaticamente(): int
    {
        $reservasExpiradas = ReservaStockVariante::expiradas()->get();
        $contador = 0;

        foreach ($reservasExpiradas as $reserva) {
            if ($reserva->expirar()) {
                $contador++;
            }
        }

        if ($contador > 0) {
            Log::info('Reservas expiradas automáticamente', ['count' => $contador]);
        }

        return $contador;
    }

    /**
     * Obtener reservas activas de un usuario
     */
    public function obtenerReservasUsuario(int $usuarioId): \Illuminate\Database\Eloquent\Collection
    {
        return ReservaStockVariante::with(['variante.producto'])
            ->porUsuario($usuarioId)
            ->activas()
            ->orderBy('fecha_expiracion', 'asc')
            ->get();
    }

    /**
     * Verificar stock disponible para variantes en un carrito
     */
    public function verificarStockCarrito(array $carrito): array
    {
        $resultado = [
            'disponible' => true,
            'errores' => [],
            'productos_info' => []
        ];

        foreach ($carrito as $item) {
            $productoId = $item['id'];
            $varianteId = $item['variante_id'] ?? null;
            $cantidad = $item['quantity'];

            if ($varianteId) {
                // Verificar stock de variante
                $variante = VarianteProducto::with('producto')->find($varianteId);
                
                if (!$variante) {
                    $resultado['errores'][] = "Variante #{$varianteId} no encontrada";
                    $resultado['disponible'] = false;
                    continue;
                }

                $tieneStock = $variante->tieneStockSuficiente($cantidad);
                $disponible = $tieneStock && $variante->disponible;

                $resultado['productos_info'][] = [
                    'id' => $productoId,
                    'variante_id' => $varianteId,
                    'nombre' => $variante->producto->nombre_producto . ' (' . $variante->nombre . ')',
                    'stock_disponible' => $variante->stock_disponible,
                    'cantidad_solicitada' => $cantidad,
                    'disponible' => $disponible,
                    'activo' => $variante->disponible
                ];

                if (!$disponible) {
                    $resultado['disponible'] = false;
                    if (!$variante->disponible) {
                        $resultado['errores'][] = "La variante '{$variante->nombre}' del producto '{$variante->producto->nombre_producto}' no está disponible";
                    } elseif (!$tieneStock) {
                        $resultado['errores'][] = "Stock insuficiente para la variante '{$variante->nombre}' del producto '{$variante->producto->nombre_producto}'. Disponible: {$variante->stock_disponible}, Solicitado: {$cantidad}";
                    }
                }
            } else {
                // Verificar stock de producto sin variante
                $producto = Producto::find($productoId);
                
                if (!$producto) {
                    $resultado['errores'][] = "Producto #{$productoId} no encontrado";
                    $resultado['disponible'] = false;
                    continue;
                }

                $tieneStock = $producto->tieneStockSuficiente($cantidad);
                $disponible = $tieneStock && $producto->activo;

                $resultado['productos_info'][] = [
                    'id' => $productoId,
                    'variante_id' => null,
                    'nombre' => $producto->nombre_producto,
                    'stock_disponible' => $producto->stock_disponible,
                    'cantidad_solicitada' => $cantidad,
                    'disponible' => $disponible,
                    'activo' => $producto->activo
                ];

                if (!$disponible) {
                    $resultado['disponible'] = false;
                    if (!$producto->activo) {
                        $resultado['errores'][] = "El producto '{$producto->nombre_producto}' no está disponible para la venta";
                    } elseif (!$tieneStock) {
                        $resultado['errores'][] = "Stock insuficiente para '{$producto->nombre_producto}'. Disponible: {$producto->stock_disponible}, Solicitado: {$cantidad}";
                    }
                }
            }
        }

        return $resultado;
    }

    /**
     * Limpiar reservas expiradas (comando para programar)
     */
    public function limpiarReservasExpiradas(): int
    {
        $reservasExpiradas = ReservaStockVariante::expiradas()->get();
        $contador = 0;

        foreach ($reservasExpiradas as $reserva) {
            if ($reserva->expirar()) {
                $contador++;
            }
        }

        Log::info('Reservas expiradas limpiadas', ['count' => $contador]);
        return $contador;
    }
}
