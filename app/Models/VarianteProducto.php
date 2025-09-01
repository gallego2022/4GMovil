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
        'stock_disponible',
        'stock_minimo',
        'stock_maximo',
        'precio_adicional',
        'descripcion',
        'disponible',
        'orden'
    ];

    protected $casts = [
        'disponible' => 'boolean',
        'precio_adicional' => 'decimal:2',
        'stock_disponible' => 'integer',
        'stock_minimo' => 'integer',
        'stock_maximo' => 'integer'
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

    public function movimientosInventario(): HasMany
    {
        return $this->hasMany(MovimientoInventarioVariante::class, 'variante_id', 'variante_id');
    }

    // Métodos para gestión de inventario
    public function registrarEntrada(int $cantidad, string $motivo, int $usuarioId, ?string $referencia = null): bool
    {
        return DB::transaction(function () use ($cantidad, $motivo, $usuarioId, $referencia) {
            $stockAnterior = $this->stock_disponible;
            $stockNuevo = $stockAnterior + $cantidad;

            // Actualizar stock
            $this->update(['stock_disponible' => $stockNuevo]);

            // Sincronizar stock del producto padre
            $this->sincronizarStockProducto();

            // Registrar movimiento
            MovimientoInventarioVariante::create([
                'variante_id' => $this->variante_id,
                'tipo_movimiento' => 'entrada',
                'cantidad' => $cantidad,
                'stock_anterior' => $stockAnterior,
                'stock_nuevo' => $stockNuevo,
                'motivo' => $motivo,
                'usuario_id' => $usuarioId,
                'referencia' => $referencia,
                'fecha_movimiento' => now()
            ]);

            Log::info('Entrada de stock registrada para variante', [
                'variante_id' => $this->variante_id,
                'color' => $this->nombre,
                'cantidad' => $cantidad,
                'stock_anterior' => $stockAnterior,
                'stock_nuevo' => $stockNuevo,
                'motivo' => $motivo
            ]);

            // Verificar si se debe enviar alerta de reposición
            $this->verificarAlertaReposicion($stockAnterior, $stockNuevo);

            return true;
        });
    }

    public function registrarSalida(int $cantidad, string $motivo, int $usuarioId, ?string $referencia = null): bool
    {
        return DB::transaction(function () use ($cantidad, $motivo, $usuarioId, $referencia) {
            $stockAnterior = $this->stock_disponible;
            
            // Verificar stock disponible
            if ($stockAnterior < $cantidad) {
                throw new \Exception("Stock insuficiente. Disponible: {$stockAnterior}, Solicitado: {$cantidad}");
            }

            $stockNuevo = $stockAnterior - $cantidad;

            // Actualizar stock
            $this->update(['stock_disponible' => $stockNuevo]);

            // Sincronizar stock del producto padre
            $this->sincronizarStockProducto();

            // Registrar movimiento
            MovimientoInventarioVariante::create([
                'variante_id' => $this->variante_id,
                'tipo_movimiento' => 'salida',
                'cantidad' => $cantidad,
                'stock_anterior' => $stockAnterior,
                'stock_nuevo' => $stockNuevo,
                'motivo' => $motivo,
                'usuario_id' => $usuarioId,
                'referencia' => $referencia,
                'fecha_movimiento' => now()
            ]);

            Log::info('Salida de stock registrada para variante', [
                'variante_id' => $this->variante_id,
                'color' => $this->nombre,
                'cantidad' => $cantidad,
                'stock_anterior' => $stockAnterior,
                'stock_nuevo' => $stockNuevo,
                'motivo' => $motivo
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
            $stockAnterior = $this->stock_disponible;
            
            // Verificar stock disponible
            if ($stockAnterior < $cantidad) {
                Log::warning('Stock insuficiente para reserva', [
                    'variante_id' => $this->variante_id,
                    'color' => $this->nombre,
                    'stock_disponible' => $stockAnterior,
                    'cantidad_solicitada' => $cantidad
                ]);
                return false;
            }

            $stockNuevo = $stockAnterior - $cantidad;

            // Actualizar stock (reserva = salida temporal)
            $this->update(['stock_disponible' => $stockNuevo]);

            // Sincronizar stock del producto padre
            $this->sincronizarStockProducto();

            // Registrar movimiento de reserva
            MovimientoInventarioVariante::create([
                'variante_id' => $this->variante_id,
                'tipo_movimiento' => 'reserva',
                'cantidad' => $cantidad,
                'stock_anterior' => $stockAnterior,
                'stock_nuevo' => $stockNuevo,
                'motivo' => $motivo,
                'usuario_id' => $usuarioId,
                'referencia' => $referencia,
                'fecha_movimiento' => now()
            ]);

            Log::info('Stock reservado para variante', [
                'variante_id' => $this->variante_id,
                'color' => $this->nombre,
                'cantidad' => $cantidad,
                'stock_anterior' => $stockAnterior,
                'stock_nuevo' => $stockNuevo,
                'motivo' => $motivo
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
            MovimientoInventarioVariante::create([
                'variante_id' => $this->variante_id,
                'tipo_movimiento' => 'venta',
                'cantidad' => $cantidad,
                'stock_anterior' => $this->stock_disponible,
                'stock_nuevo' => $this->stock_disponible, // No cambia porque ya se reservó
                'motivo' => $motivo,
                'usuario_id' => $usuarioId,
                'referencia' => $referencia,
                'fecha_movimiento' => now()
            ]);

            Log::info('Venta confirmada para variante', [
                'variante_id' => $this->variante_id,
                'color' => $this->nombre,
                'cantidad' => $cantidad,
                'motivo' => $motivo
            ]);

            return true;
        });
    }

    public function liberarReserva(int $cantidad, string $motivo, int $usuarioId, ?string $referencia = null): bool
    {
        return DB::transaction(function () use ($cantidad, $motivo, $usuarioId, $referencia) {
            $stockAnterior = $this->stock_disponible;
            $stockNuevo = $stockAnterior + $cantidad;

            // Restaurar stock (liberar reserva)
            $this->update(['stock_disponible' => $stockNuevo]);

            // Sincronizar stock del producto padre
            $this->sincronizarStockProducto();

            // Registrar movimiento de liberación
            MovimientoInventarioVariante::create([
                'variante_id' => $this->variante_id,
                'tipo_movimiento' => 'liberacion',
                'cantidad' => $cantidad,
                'stock_anterior' => $stockAnterior,
                'stock_nuevo' => $stockNuevo,
                'motivo' => $motivo,
                'usuario_id' => $usuarioId,
                'referencia' => $referencia,
                'fecha_movimiento' => now()
            ]);

            Log::info('Reserva liberada para variante', [
                'variante_id' => $this->variante_id,
                'color' => $this->nombre,
                'cantidad' => $cantidad,
                'stock_anterior' => $stockAnterior,
                'stock_nuevo' => $stockNuevo,
                'motivo' => $motivo
            ]);

            return true;
        });
    }

    // Métodos para alertas automáticas
    private function verificarAlertaStockBajo(int $stockAnterior, int $stockNuevo): void
    {
        // Solo enviar alerta si el stock anterior estaba por encima del umbral y ahora está por debajo
        if ($stockAnterior > $this->stock_minimo && $stockNuevo <= $this->stock_minimo) {
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
        // Si el stock anterior estaba por debajo del mínimo y ahora está por encima, enviar alerta de reposición
        if ($stockAnterior <= $this->stock_minimo && $stockNuevo > $this->stock_minimo) {
            Log::info('Stock repuesto para variante', [
                'variante_id' => $this->variante_id,
                'color' => $this->nombre,
                'stock_anterior' => $stockAnterior,
                'stock_nuevo' => $stockNuevo
            ]);
        }
    }

    private function determinarTipoAlerta(int $stockActual): ?string
    {
        if ($stockActual <= 0) {
            return 'agotado';
        }
        
        $porcentaje = $this->stock_minimo > 0 ? ($stockActual / $this->stock_minimo) * 100 : 0;
        
        if ($porcentaje <= 20) {
            return 'critico';
        } elseif ($porcentaje <= 60) {
            return 'bajo';
        }
        
        return null; // No enviar alerta
    }

    // Métodos de consulta
    public function tieneStockSuficiente(int $cantidad): bool
    {
        return $this->stock_disponible >= $cantidad;
    }

    public function necesitaReposicion(): bool
    {
        return $this->stock_disponible <= $this->stock_minimo;
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
        return $query->where('stock_disponible', '>', 0);
    }

    public function scopeNecesitaReposicion($query)
    {
        return $query->whereRaw('stock_disponible <= stock_minimo');
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
                'error' => $e->getMessage()
            ]);
        }
    }
}
