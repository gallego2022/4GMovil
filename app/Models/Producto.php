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
        return $this->hasMany(ImagenProducto::class, 'producto_id');
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
        return $this->hasMany(VarianteProducto::class, 'producto_id')->orderBy('orden');
    }

    // Métodos para calcular stock total basado en variantes
    public function getStockTotalVariantesAttribute(): int
    {
        return $this->variantes()->sum('stock_disponible');
    }

    public function getStockDisponibleVariantesAttribute(): int
    {
        return $this->variantes()
            ->where('disponible', true)
            ->sum('stock_disponible');
    }

    public function tieneStockSuficienteVariantes(int $cantidad): bool
    {
        return $this->stock_disponible_variantes >= $cantidad;
    }

    public function necesitaReposicionVariantes(): bool
    {
        return $this->variantes()
            ->where('disponible', true)
            ->whereRaw('stock_disponible <= stock_minimo')
            ->exists();
    }

    public function getVariantesConStockBajoAttribute()
    {
        return $this->variantes()
            ->where('disponible', true)
            ->whereRaw('stock_disponible <= stock_minimo')
            ->get();
    }

    /**
     * Sincronizar el stock del producto con la suma de sus variantes
     * Este método actualiza el campo 'stock' del producto basado en las variantes
     */
    public function sincronizarStockConVariantes(): void
    {
        $stockTotal = $this->getStockTotalVariantesAttribute();
        $stockDisponible = $this->getStockDisponibleVariantesAttribute();
        
        // Actualizar el stock total del producto
        $this->update([
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

    // Métodos para configurar los umbrales de alerta
    public static function getUmbralStockBajo(): float
    {
        return config('inventario.alertas.stock_bajo', 0.6);
    }

    public static function getUmbralStockCritico(): float
    {
        return config('inventario.alertas.stock_critico', 0.2);
    }

    // Métodos actualizados para usar los umbrales configurables
    public function getStockBajoAttribute(): bool
    {
        return $this->stock <= ($this->stock_minimo * self::getUmbralStockBajo()) && $this->stock > 0;
    }

    public function getStockCriticoAttribute(): bool
    {
        return $this->stock <= ($this->stock_minimo * self::getUmbralStockCritico()) && $this->stock > 0;
    }

    public function getStockExcesivoAttribute(): bool
    {
        return $this->stock > $this->stock_maximo;
    }

    public function getEstadoStockAttribute(): string
    {
        if ($this->stock <= 0) return 'sin_stock';
        if ($this->stockCritico) return 'critico';
        if ($this->stockBajo) return 'bajo';
        if ($this->stockExcesivo) return 'excesivo';
        return 'normal';
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
