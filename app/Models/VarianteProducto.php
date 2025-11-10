<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VarianteProducto extends Model
{
    protected $table = 'variantes_producto';

    protected $primaryKey = 'variante_id';

    protected $fillable = [
        'producto_id',
        'nombre',
        'codigo_color',
        'descripcion',
        'precio_adicional',
        'stock',
        'stock_reservado',
        'disponible',
        'sku',
        'referencia',
    ];

    protected $casts = [
        'disponible' => 'boolean',
        'precio_adicional' => 'decimal:2',
        'stock' => 'integer',
        'stock_reservado' => 'integer',
    ];

    // Relaciones
    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'producto_id', 'producto_id');
    }

    public function imagenes(): HasMany
    {
        return $this->hasMany(ImagenVariante::class, 'variante_id', 'variante_id');
    }

    public function detallesPedido(): HasMany
    {
        return $this->hasMany(DetallePedido::class, 'variante_id', 'variante_id');
    }

    public function movimientosInventario(): HasMany
    {
        return $this->hasMany(MovimientoInventario::class, 'variante_id', 'variante_id');
    }

    // Métodos para gestión de inventario
    public function registrarEntrada(int $cantidad, string $motivo, int $usuarioId, ?string $referencia = null): bool
    {
        return DB::transaction(function () use ($cantidad, $motivo, $usuarioId, $referencia) {
            $stockAnterior = $this->stock;
            $stockNuevo = $stockAnterior + $cantidad;

            // Actualizar stock
            $this->update(['stock' => $stockNuevo]);

            // Sincronizar stock del producto padre
            $this->sincronizarStockProducto();

            // Registrar movimiento en tabla unificada
            MovimientoInventario::create([
                'variante_id' => $this->variante_id,
                'tipo_movimiento' => 'entrada',
                'cantidad' => $cantidad,
                'stock_anterior' => $stockAnterior,
                'stock_nuevo' => $stockNuevo,
                'motivo' => $motivo,
                'usuario_id' => $usuarioId,
                'referencia' => $referencia,
                'fecha_movimiento' => now(),
            ]);

            Log::info('Entrada de stock registrada para variante', [
                'variante_id' => $this->variante_id,
                'color' => $this->nombre,
                'cantidad' => $cantidad,
                'stock_anterior' => $stockAnterior,
                'stock_nuevo' => $stockNuevo,
                'motivo' => $motivo,
            ]);

            // Verificar si se debe enviar alerta de reposición
            $this->verificarAlertaReposicion($stockAnterior, $stockNuevo);

            return true;
        });
    }

    public function registrarSalida(int $cantidad, string $motivo, int $usuarioId, ?string $referencia = null): bool
    {
        return DB::transaction(function () use ($cantidad, $motivo, $usuarioId, $referencia) {
            $stockAnterior = $this->stock;

            // Verificar stock disponible
            if ($stockAnterior < $cantidad) {
                throw new \Exception("Stock insuficiente. Disponible: {$stockAnterior}, Solicitado: {$cantidad}");
            }

            $stockNuevo = $stockAnterior - $cantidad;

            // Actualizar stock
            $this->update(['stock' => $stockNuevo]);

            // Sincronizar stock del producto padre
            $this->sincronizarStockProducto();

            // Registrar movimiento
            MovimientoInventario::create([
                'variante_id' => $this->variante_id,
                'tipo_movimiento' => 'salida',
                'cantidad' => $cantidad,
                'stock_anterior' => $stockAnterior,
                'stock_nuevo' => $stockNuevo,
                'motivo' => $motivo,
                'usuario_id' => $usuarioId,
                'referencia' => $referencia,
                'fecha_movimiento' => now(),
            ]);

            Log::info('Salida de stock registrada para variante', [
                'variante_id' => $this->variante_id,
                'color' => $this->nombre,
                'cantidad' => $cantidad,
                'stock_anterior' => $stockAnterior,
                'stock_nuevo' => $stockNuevo,
                'motivo' => $motivo,
            ]);

            // Verificar si se debe enviar alerta de stock bajo
            $this->verificarAlertaStockBajo($stockAnterior, $stockNuevo);

            return true;
        });
    }

    // Métodos para reserva de stock en el proceso de compra
    public function reservarStock(int $cantidad, string $motivo, int $usuarioId, ?string $referencia = null): bool
    {
        return DB::transaction(function () use ($cantidad, $motivo, $usuarioId, $referencia) {
            $stockAnterior = $this->stock;

            // Verificar stock disponible
            if ($stockAnterior < $cantidad) {
                Log::warning('Stock insuficiente para reserva', [
                    'variante_id' => $this->variante_id,
                    'color' => $this->nombre,
                    'stock' => $stockAnterior,
                    'cantidad_solicitada' => $cantidad,
                ]);

                return false;
            }

            $stockNuevo = $stockAnterior - $cantidad;

            // Incrementar stock_reservado
            $stockReservadoAnterior = $this->stock_reservado ?? 0;
            $nuevoStockReservado = $stockReservadoAnterior + $cantidad;

            // Actualizar stock (reserva = salida temporal) y stock_reservado usando consulta directa
            \Illuminate\Support\Facades\DB::table('variantes_producto')
                ->where('variante_id', $this->variante_id)
                ->update([
                    'stock' => $stockNuevo,
                    'stock_reservado' => $nuevoStockReservado,
                ]);

            // Actualizar el modelo localmente
            $this->stock = $stockNuevo;
            $this->stock_reservado = $nuevoStockReservado;

            // Sincronizar stock del producto padre
            $this->sincronizarStockProducto();

            // Registrar movimiento de reserva
            MovimientoInventario::create([
                'variante_id' => $this->variante_id,
                'tipo_movimiento' => 'reserva',
                'cantidad' => $cantidad,
                'stock_anterior' => $stockAnterior,
                'stock_nuevo' => $stockNuevo,
                'motivo' => $motivo,
                'usuario_id' => $usuarioId,
                'referencia' => $referencia,
                'fecha_movimiento' => now(),
            ]);

            Log::info('Stock reservado para variante', [
                'variante_id' => $this->variante_id,
                'color' => $this->nombre,
                'cantidad' => $cantidad,
                'stock_anterior' => $stockAnterior,
                'stock_nuevo' => $stockNuevo,
                'motivo' => $motivo,
            ]);

            // Verificar si se debe enviar alerta de stock bajo
            $this->verificarAlertaStockBajo($stockAnterior, $stockNuevo);

            return true;
        });
    }

    public function confirmarReserva(int $cantidad, string $motivo, int $usuarioId, ?string $referencia = null): bool
    {
        // La reserva ya se hizo, solo registrar como venta confirmada
        return DB::transaction(function () use ($cantidad, $motivo, $usuarioId, $referencia) {
            // Obtener el stock_reservado actual desde la base de datos para evitar inconsistencias
            $varianteActual = DB::table('variantes_producto')
                ->where('variante_id', $this->variante_id)
                ->first();

            $stockReservadoAnterior = $varianteActual->stock_reservado ?? 0;
            $nuevoStockReservado = max(0, $stockReservadoAnterior - $cantidad);

            // Actualizar stock_reservado usando consulta directa para asegurar actualización
            DB::table('variantes_producto')
                ->where('variante_id', $this->variante_id)
                ->update(['stock_reservado' => $nuevoStockReservado]);

            // Actualizar el modelo localmente
            $this->stock_reservado = $nuevoStockReservado;

            // Refrescar el modelo para obtener los valores actualizados
            $this->refresh();

            MovimientoInventario::create([
                'variante_id' => $this->variante_id,
                'tipo_movimiento' => 'salida',
                'cantidad' => $cantidad,
                'stock_anterior' => $this->stock,
                'stock_nuevo' => $this->stock, // No cambia porque ya se reservó
                'motivo' => $motivo,
                'usuario_id' => $usuarioId,
                'referencia' => $referencia,
                'fecha_movimiento' => now(),
            ]);

            Log::info('Venta confirmada para variante', [
                'variante_id' => $this->variante_id,
                'color' => $this->nombre,
                'cantidad' => $cantidad,
                'stock_reservado_anterior' => $stockReservadoAnterior,
                'stock_reservado_nuevo' => $nuevoStockReservado,
                'stock_actual' => $this->stock,
                'motivo' => $motivo,
            ]);

            return true;
        });
    }

    public function liberarReserva(int $cantidad, string $motivo, int $usuarioId, ?string $referencia = null): bool
    {
        return DB::transaction(function () use ($cantidad, $motivo, $usuarioId, $referencia) {
            $stockAnterior = $this->stock;
            $stockNuevo = $stockAnterior + $cantidad;

            // Decrementar stock_reservado
            $stockReservadoAnterior = $this->stock_reservado ?? 0;
            $nuevoStockReservado = max(0, $stockReservadoAnterior - $cantidad);

            // Restaurar stock (liberar reserva) y decrementar stock_reservado usando consulta directa
            \Illuminate\Support\Facades\DB::table('variantes_producto')
                ->where('variante_id', $this->variante_id)
                ->update([
                    'stock' => $stockNuevo,
                    'stock_reservado' => $nuevoStockReservado,
                ]);

            // Actualizar el modelo localmente
            $this->stock = $stockNuevo;
            $this->stock_reservado = $nuevoStockReservado;

            // Sincronizar stock del producto padre
            $this->sincronizarStockProducto();

            // Registrar movimiento de liberación
            MovimientoInventario::create([
                'variante_id' => $this->variante_id,
                'tipo_movimiento' => 'liberacion_reserva',
                'cantidad' => $cantidad,
                'stock_anterior' => $stockAnterior,
                'stock_nuevo' => $stockNuevo,
                'motivo' => $motivo,
                'usuario_id' => $usuarioId,
                'referencia' => $referencia,
                'fecha_movimiento' => now(),
            ]);

            Log::info('Reserva liberada para variante', [
                'variante_id' => $this->variante_id,
                'color' => $this->nombre,
                'cantidad' => $cantidad,
                'stock_anterior' => $stockAnterior,
                'stock_nuevo' => $stockNuevo,
                'motivo' => $motivo,
            ]);

            return true;
        });
    }

    // Métodos para alertas automáticas
    private function verificarAlertaStockBajo(int $stockAnterior, int $stockNuevo): void
    {
        // Obtener umbrales del producto padre
        $producto = $this->producto;
        $stockMinimo = $producto->stock_minimo ?? null;
        if ($stockMinimo === null) {
            $stockInicial = $producto->stock_inicial ?? 0;
            $stockMinimo = $stockInicial > 0 ? (int) ceil(($stockInicial * 20) / 100) : 10;
        }

        // Solo enviar alerta si el stock anterior estaba por encima del umbral y ahora está por debajo
        if ($stockAnterior > $stockMinimo && $stockNuevo <= $stockMinimo) {
            $tipoAlerta = $this->determinarTipoAlerta($stockNuevo);

            if ($tipoAlerta) {
                dispatch(new \App\Jobs\ProcesarAlertaStockVariante(
                    $this,
                    $tipoAlerta,
                    $stockAnterior,
                    $stockNuevo
                ));
            }
        }
    }

    private function verificarAlertaReposicion(int $stockAnterior, int $stockNuevo): void
    {
        // Obtener umbrales del producto padre
        $producto = $this->producto;
        $stockMinimo = $producto->stock_minimo ?? null;
        if ($stockMinimo === null) {
            $stockInicial = $producto->stock_inicial ?? 0;
            $stockMinimo = $stockInicial > 0 ? (int) ceil(($stockInicial * 20) / 100) : 10;
        }

        // Si el stock anterior estaba por debajo del mínimo y ahora está por encima, enviar alerta de reposición
        if ($stockAnterior <= $stockMinimo && $stockNuevo > $stockMinimo) {
            Log::info('Stock repuesto para variante', [
                'variante_id' => $this->variante_id,
                'color' => $this->nombre,
                'stock_anterior' => $stockAnterior,
                'stock_nuevo' => $stockNuevo,
            ]);
        }
    }

    private function determinarTipoAlerta(int $stockActual): ?string
    {
        if ($stockActual <= 0) {
            return 'agotado';
        }

        // Obtener umbrales del producto padre
        $producto = $this->producto;
        $stockMinimo = $producto->stock_minimo ?? null;
        $stockMaximo = $producto->stock_maximo ?? null;

        if ($stockMinimo === null || $stockMaximo === null) {
            $stockInicial = $producto->stock_inicial ?? 0;
            if ($stockInicial > 0) {
                $stockMinimo = $stockMinimo ?? (int) ceil(($stockInicial * 20) / 100);
                $stockMaximo = $stockMaximo ?? (int) ceil(($stockInicial * 60) / 100);
            } else {
                $stockMinimo = $stockMinimo ?? 5;
                $stockMaximo = $stockMaximo ?? 10;
            }
        }

        // Verificar tipo de alerta usando umbrales del producto
        if ($stockActual <= $stockMinimo) {
            return 'critico';
        } elseif ($stockActual <= $stockMaximo) {
            return 'bajo';
        }

        return null; // No enviar alerta
    }

    // Accessors y Mutators
    public function getStockDisponibleAttribute(): int
    {
        $stockReservado = $this->stock_reservado ?? 0;
        $stockDisponible = $this->stock - $stockReservado;

        return max(0, $stockDisponible);
    }

    // Métodos de consulta
    public function tieneStockSuficiente(int $cantidad): bool
    {
        return $this->stock_disponible >= $cantidad;
    }

    public function necesitaReposicion(): bool
    {
        // Obtener umbral crítico del producto padre
        $producto = $this->producto;
        $stockMinimo = $producto->stock_minimo ?? null;

        if ($stockMinimo === null) {
            $stockInicial = $producto->stock_inicial ?? 0;
            $stockMinimo = $stockInicial > 0 ? (int) ceil(($stockInicial * 20) / 100) : 10;
        }

        return $this->stock <= $stockMinimo;
    }

    public function getPrecioFinalAttribute(): float
    {
        return $this->producto->precio + $this->precio_adicional;
    }

    // Scopes para consultas comunes
    public function scopeDisponibles($query)
    {
        return $query->where('disponible', true);
    }

    public function scopeConStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    public function scopeNecesitaReposicion($query)
    {
        return $query->where('stock', '<=', 10); // Valor por defecto
    }

    /**
     * Sincronizar el stock del producto padre con la suma de todas sus variantes
     */
    private function sincronizarStockProducto(): void
    {
        try {
            $producto = $this->producto;
            if ($producto) {
                $producto->sincronizarStockConVariantes();
            }
        } catch (\Exception $e) {
            Log::error('Error al sincronizar stock del producto padre', [
                'variante_id' => $this->variante_id,
                'producto_id' => $this->producto_id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Boot del modelo para sincronización automática
     */
    protected static function boot()
    {
        parent::boot();

        // Después de crear, actualizar o eliminar una variante, sincronizar el producto padre
        static::created(function ($variante) {
            $variante->sincronizarStockProducto();
        });

        static::updated(function ($variante) {
            $variante->sincronizarStockProducto();
        });

        static::deleted(function ($variante) {
            $variante->sincronizarStockProducto();
        });
    }

    /**
     * Confirmar venta de una variante (registra movimiento de venta confirmada)
     */
    public function confirmarVenta(int $cantidad, string $motivo, int $usuarioId, ?string $referencia = null): bool
    {
        return DB::transaction(function () use ($cantidad, $motivo, $usuarioId, $referencia) {
            // Registrar movimiento de venta confirmada
            MovimientoInventario::create([
                'variante_id' => $this->variante_id,
                'tipo_movimiento' => 'salida',
                'cantidad' => $cantidad,
                'stock_anterior' => $this->stock,
                'stock_nuevo' => $this->stock, // No cambia el stock físico
                'motivo' => $motivo,
                'usuario_id' => $usuarioId,
                'referencia' => $referencia,
                'fecha_movimiento' => now(),
            ]);

            Log::info('Venta confirmada para variante', [
                'variante_id' => $this->variante_id,
                'color' => $this->nombre,
                'cantidad' => $cantidad,
                'motivo' => $motivo,
            ]);

            return true;
        });
    }
}
