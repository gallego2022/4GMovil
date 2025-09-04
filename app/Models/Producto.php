<?php

namespace App\Models;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'productos';
    protected $primaryKey = 'producto_id';

    protected $fillable = [
        'nombre_producto',
        'descripcion',
        'precio',
        'estado',
        'stock',
        'stock_inicial',
        'stock_reservado',
        'stock_disponible',
        'stock_minimo',
        'stock_maximo',
        'codigo_barras',
        'sku',
        'costo_unitario',
        'peso',
        'dimensiones',
        'activo',
        'ultima_actualizacion_stock',
        'notas_inventario',
        'categoria_id',
        'marca_id',
    ];

   public $timestamps = true;

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    public function marca()
    {
        return $this->belongsTo(Marca::class, 'marca_id');
    }

    public function imagenes()
    {
        return $this->hasMany(ImagenProducto::class, 'producto_id')->activas()->ordenadas();
    }

    /**
     * Obtener la imagen principal del producto
     */
    public function imagenPrincipal()
    {
        return $this->hasOne(ImagenProducto::class, 'producto_id')->principal();
    }

    public function detallesPedido()
    {
        return $this->hasMany(DetallePedido::class, 'producto_id');
    }

    public function resenas()
    {
        return $this->hasMany(Resena::class, 'producto_id');
    }

    public function variantes()
    {
        return $this->hasMany(VarianteProducto::class, 'producto_id');
    }

    // Métodos para calcular stock total basado en variantes
    public function getStockTotalVariantesAttribute(): int
    {
        return $this->variantes()->sum('stock');
    }

    public function getStockDisponibleVariantesAttribute(): int
    {
        return $this->variantes()
            ->where('disponible', true)
            ->sum('stock');
    }

    public function tieneStockSuficienteVariantes(int $cantidad): bool
    {
        return $this->stock_disponible_variantes >= $cantidad;
    }

    public function necesitaReposicionVariantes(): bool
    {
        return $this->variantes()
            ->where('disponible', true)
            ->where('stock', '<=', 10) // Usar valor fijo por ahora
            ->exists();
    }

    public function getVariantesConStockBajoAttribute()
    {
        return $this->variantes()
            ->where('disponible', true)
            ->where('stock', '<=', 10) // Usar valor fijo por ahora
            ->get();
    }

    /**
     * Sincronizar el stock del producto con la suma de sus variantes
     * Este método actualiza el campo 'stock' del producto basado en las variantes
     */
    public function sincronizarStockConVariantes(): void
    {
        try {
            // Obtener stock total de variantes usando query directo para evitar bucles
            $stockTotal = $this->variantes()->sum('stock');
            $stockDisponible = $this->variantes()
                ->where('disponible', true)
                ->sum('stock');
            
            // Actualizar solo si hay cambios para evitar bucles infinitos
            if ($this->stock != $stockTotal || $this->stock_disponible != $stockDisponible) {
                // Usar updateQuietly para evitar eventos que puedan causar bucles
                $this->updateQuietly([
                    'stock' => $stockTotal,
                    'stock_disponible' => $stockDisponible,
                    'ultima_actualizacion_stock' => now()
                ]);
                
                Log::info('Stock del producto sincronizado con variantes', [
                    'producto_id' => $this->producto_id,
                    'nombre' => $this->nombre_producto,
                    'stock_total' => $stockTotal,
                    'stock_disponible' => $stockDisponible
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error al sincronizar stock del producto', [
                'producto_id' => $this->producto_id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Verificar si el producto tiene variantes
     */
    public function tieneVariantes(): bool
    {
        return $this->variantes()->exists();
    }

    /**
     * Obtener el stock real del producto (considerando si tiene variantes o no)
     */
    public function getStockRealAttribute(): int
    {
        if ($this->tieneVariantes()) {
            // Si tiene variantes, usar la suma de las variantes
            return $this->getStockDisponibleVariantesAttribute();
        } else {
            // Si no tiene variantes, usar el stock directo del producto
            return $this->getStockDisponibleAttribute();
        }
    }

    /**
     * Verificar si el producto tiene stock suficiente (considerando variantes)
     */
    public function tieneStockSuficienteReal(int $cantidad): bool
    {
        return $this->stock_real >= $cantidad;
    }

    /**
     * Obtener el estado de stock del producto (considerando variantes)
     */
    public function getEstadoStockRealAttribute(): string
    {
        $stockReal = $this->stock_real;
        
        if ($stockReal <= 0) return 'sin_stock';
        
        if ($this->tieneVariantes()) {
            // Para productos con variantes, verificar si alguna necesita reposición
            if ($this->necesitaReposicionVariantes()) {
                return 'necesita_reposicion';
            }
        } else {
            // Para productos sin variantes, usar la lógica original
            if ($this->stockCritico) return 'critico';
            if ($this->stockBajo) return 'bajo';
        }
        
        return 'normal';
    }

    public function movimientosInventario()
    {
        return $this->hasMany(MovimientoInventario::class, 'producto_id');
    }

    public function especificaciones()
    {
        return $this->hasMany(EspecificacionProducto::class, 'producto_id', 'producto_id');
    }

    /**
     * Boot del modelo para configurar eliminación en cascada
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($producto) {
            // Eliminar imágenes del producto
            $producto->imagenes()->delete();
            
            // Eliminar detalles de pedido relacionados
            $producto->detallesPedido()->delete();
            
            // Eliminar reseñas del producto
            $producto->resenas()->delete();
            
            // Eliminar movimientos de inventario
            $producto->movimientosInventario()->delete();
        });

        // Evento después de crear una variante
        static::updated(function ($producto) {
            // Si el producto tiene variantes, sincronizar el stock
            if ($producto->tieneVariantes()) {
                $producto->sincronizarStockConVariantes();
            }
        });
    }

    // Métodos para gestión de inventario
    public function registrarEntrada(int $cantidad, string $motivo, ?int $usuarioId = null, ?string $referencia = null): void
    {
        $stockAnterior = $this->stock;
        $this->stock += $cantidad;
        $this->ultima_actualizacion_stock = now();
        $this->save();

        MovimientoInventario::create([
            'producto_id' => $this->producto_id,
            'tipo_movimiento' => 'entrada',
            'cantidad' => $cantidad,
            'stock_anterior' => $stockAnterior,
            'stock_nuevo' => $this->stock,
            'motivo' => $motivo,
            'usuario_id' => $usuarioId,
            'referencia' => $referencia,
            'costo_unitario' => $this->costo_unitario
        ]);
    }

    public function registrarSalida(int $cantidad, string $motivo, ?int $usuarioId = null, ?int $pedidoId = null): bool
    {
        if ($this->stock < $cantidad) {
            return false; // Stock insuficiente
        }

        $stockAnterior = $this->stock;
        $this->stock -= $cantidad;
        $this->ultima_actualizacion_stock = now();
        $this->save();

        MovimientoInventario::create([
            'producto_id' => $this->producto_id,
            'tipo_movimiento' => 'salida',
            'cantidad' => $cantidad,
            'stock_anterior' => $stockAnterior,
            'stock_nuevo' => $this->stock,
            'motivo' => $motivo,
            'usuario_id' => $usuarioId,
            'pedido_id' => $pedidoId,
            'costo_unitario' => $this->costo_unitario
        ]);

        return true;
    }

    public function ajustarStock(int $nuevoStock, string $motivo, ?int $usuarioId = null): void
    {
        $stockAnterior = $this->stock;
        $diferencia = $nuevoStock - $this->stock;
        $this->stock = $nuevoStock;
        $this->ultima_actualizacion_stock = now();
        $this->save();

        if ($diferencia != 0) {
            MovimientoInventario::create([
                'producto_id' => $this->producto_id,
                'tipo_movimiento' => $diferencia > 0 ? 'ajuste_positivo' : 'ajuste_negativo',
                'cantidad' => abs($diferencia),
                'stock_anterior' => $stockAnterior,
                'stock_nuevo' => $this->stock,
                'motivo' => $motivo,
                'usuario_id' => $usuarioId,
                'costo_unitario' => $this->costo_unitario
            ]);
        }
    }

    // Accessors y Mutators
    public function getStockDisponibleAttribute(): int
    {
        $stockReservado = $this->stock_reservado ?? 0;
        $stockDisponible = $this->stock - $stockReservado;
        return max(0, $stockDisponible);
    }

    public function getStockReservadoAttribute(): int
    {
        return $this->stock_reservado ?? 0;
    }

    public function getStockTotalAttribute(): int
    {
        return $this->stock;
    }

    public function tieneStockSuficiente(int $cantidad): bool
    {
        return $this->stock_disponible >= $cantidad;
    }

    // Mutator para actualizar automáticamente stock_disponible cuando cambie el stock
    public function setStockAttribute($value)
    {
        $this->attributes['stock'] = $value;
        
        // Actualizar stock_disponible si no hay stock reservado o si el stock reservado es menor que el nuevo stock
        $stockReservado = $this->stock_reservado ?? 0;
        $this->attributes['stock_disponible'] = max(0, $value - $stockReservado);
    }

    // Métodos para gestión de stock reservado
    public function reservarStock(int $cantidad, string $motivo, ?int $usuarioId = null, ?int $pedidoId = null): bool
    {
        // Verificar que hay stock disponible suficiente
        $stockDisponible = $this->stock - ($this->stock_reservado ?? 0);
        if ($stockDisponible < $cantidad) {
            return false; // Stock insuficiente
        }

        $stockReservadoAnterior = $this->stock_reservado ?? 0;
        $stockDisponibleAnterior = $this->stock - $stockReservadoAnterior;
        
        $nuevoStockReservado = $stockReservadoAnterior + $cantidad;
        $nuevoStockDisponible = $this->stock - $nuevoStockReservado;
        
        // Usar una consulta directa para actualizar ambos campos
        $resultado = \Illuminate\Support\Facades\DB::table('productos')
            ->where('producto_id', $this->producto_id)
            ->update([
                'stock_reservado' => $nuevoStockReservado,
                'stock_disponible' => $nuevoStockDisponible,
                'ultima_actualizacion_stock' => now()
            ]);
        
        if ($resultado === 0) {
            return false; // No se pudo actualizar
        }
        
        // Actualizar el modelo localmente
        $this->stock_reservado = $nuevoStockReservado;
        $this->stock_disponible = $nuevoStockDisponible;
        $this->ultima_actualizacion_stock = now();

        MovimientoInventario::create([
            'producto_id' => $this->producto_id,
            'tipo_movimiento' => 'reserva',
            'cantidad' => $cantidad,
            'stock_anterior' => $stockDisponibleAnterior,
            'stock_nuevo' => $nuevoStockDisponible,
            'motivo' => $motivo,
            'usuario_id' => $usuarioId,
            'pedido_id' => $pedidoId,
            'costo_unitario' => $this->costo_unitario
        ]);

        return true;
    }

    public function liberarStockReservado(int $cantidad, string $motivo, ?int $usuarioId = null, ?int $pedidoId = null): bool
    {
        if ($this->stock_reservado < $cantidad) {
            return false; // Stock reservado insuficiente
        }

        $stockReservadoAnterior = $this->stock_reservado;
        $stockDisponibleAnterior = $this->stock - $stockReservadoAnterior;
        
        $nuevoStockReservado = $stockReservadoAnterior - $cantidad;
        $nuevoStockDisponible = $this->stock - $nuevoStockReservado;
        
        // Usar una consulta directa para actualizar ambos campos
        $resultado = \Illuminate\Support\Facades\DB::table('productos')
            ->where('producto_id', $this->producto_id)
            ->update([
                'stock_reservado' => $nuevoStockReservado,
                'stock_disponible' => $nuevoStockDisponible,
                'ultima_actualizacion_stock' => now()
            ]);
        
        if ($resultado === 0) {
            return false; // No se pudo actualizar
        }
        
        // Actualizar el modelo localmente
        $this->stock_reservado = $nuevoStockReservado;
        $this->stock_disponible = $nuevoStockDisponible;
        $this->ultima_actualizacion_stock = now();

        MovimientoInventario::create([
            'producto_id' => $this->producto_id,
            'tipo_movimiento' => 'liberacion',
            'cantidad' => $cantidad,
            'stock_anterior' => $stockDisponibleAnterior,
            'stock_nuevo' => $nuevoStockDisponible,
            'motivo' => $motivo,
            'usuario_id' => $usuarioId,
            'pedido_id' => $pedidoId,
            'costo_unitario' => $this->costo_unitario
        ]);

        return true;
    }

    /**
     * Obtener el stock inicial del producto (campo stock_inicial)
     */
    public function getStockInicialAttribute(): int
    {
        return $this->attributes['stock_inicial'] ?? 0;
    }

    /**
     * Calcular el umbral de stock bajo basado en el stock inicial (60%)
     */
    public function getUmbralStockBajoAttribute(): int
    {
        $stockInicial = $this->stock_inicial;
        
        if ($stockInicial <= 0) {
            // Si no hay stock inicial, usar el stock mínimo como fallback
            return $this->stock_minimo ?? 5;
        }
        
        // 60% del stock inicial
        return (int) ceil(($stockInicial * 60) / 100);
    }

    /**
     * Calcular el umbral de stock crítico basado en el stock inicial (20%)
     */
    public function getUmbralStockCriticoAttribute(): int
    {
        $stockInicial = $this->stock_inicial;
        
        if ($stockInicial <= 0) {
            // Si no hay stock inicial, usar el stock mínimo como fallback
            return max(1, (int) ceil(($this->stock_minimo ?? 5) * 0.2));
        }
        
        // 20% del stock inicial
        return (int) ceil(($stockInicial * 20) / 100);
    }

    /**
     * Verificar si el producto tiene stock bajo (60% del stock inicial)
     */
    public function getStockBajoAttribute(): bool
    {
        $umbral = $this->umbral_stock_bajo;
        return $this->stock_disponible > $umbral && $this->stock_disponible <= ($umbral * 1.5);
    }

    /**
     * Verificar si el producto tiene stock crítico (20% del stock inicial)
     */
    public function getStockCriticoAttribute(): bool
    {
        $umbral = $this->umbral_stock_critico;
        return $this->stock_disponible <= $umbral;
    }

    /**
     * Verificar si el producto está sin stock
     */
    public function getSinStockAttribute(): bool
    {
        return $this->stock_disponible <= 0;
    }

    /**
     * Obtener el estado de stock del producto con la nueva lógica
     */
    public function getEstadoStockAttribute(): string
    {
        if ($this->sin_stock) {
            return 'sin_stock';
        }
        
        if ($this->stock_critico) {
            return 'critico';
        }
        
        if ($this->stock_bajo) {
            return 'bajo';
        }
        
        return 'normal';
    }

    /**
     * Obtener información detallada del estado del stock
     */
    public function getInfoEstadoStockAttribute(): array
    {
        $stockInicial = $this->stock_inicial;
        $stockActual = $this->stock_disponible;
        $umbralBajo = $this->umbral_stock_bajo;
        $umbralCritico = $this->umbral_stock_critico;
        
        return [
            'stock_inicial' => $stockInicial,
            'stock_actual' => $stockActual,
            'stock_porcentaje' => $stockInicial > 0 ? round(($stockActual / $stockInicial) * 100, 2) : 0,
            'umbral_bajo' => $umbralBajo,
            'umbral_critico' => $umbralCritico,
            'estado' => $this->estado_stock,
            'necesita_reposicion' => $this->stock_critico || $this->stock_bajo,
            'porcentaje_stock_bajo' => 60.00,
            'porcentaje_stock_critico' => 20.00
        ];
    }

    public function getClaseColorStockAttribute(): string
    {
        return match($this->estado_stock) {
            'sin_stock' => 'text-red-600 bg-red-100',
            'critico' => 'text-red-600 bg-red-100',
            'bajo' => 'text-yellow-600 bg-yellow-100',
            'excesivo' => 'text-blue-600 bg-blue-100',
            default => 'text-green-600 bg-green-100'
        };
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeStockBajo($query)
    {
        // Stock bajo: cuando está por debajo del 60% del stock mínimo (alta rotación)
        return $query->whereRaw('stock <= (stock_minimo * 0.6) AND stock > 0');
    }

    public function scopeStockCritico($query)
    {
        // Stock crítico: cuando está por debajo del 20% del stock mínimo (alta rotación)
        return $query->whereRaw('stock <= (stock_minimo * 0.2) AND stock > 0');
    }

    public function scopeStockExcesivo($query)
    {
        return $query->whereRaw('stock > stock_maximo');
    }

    public function scopeSinStock($query)
    {
        return $query->where('stock', '<=', 0);
    }
}
