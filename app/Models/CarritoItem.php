<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarritoItem extends Model
{
    protected $table = 'carrito_items';
    protected $primaryKey = 'id';

    protected $fillable = [
        'carrito_id',
        'producto_id',
        'variante_id',
        'cantidad',
        'precio_unitario',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'precio_unitario' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación con el carrito
     */
    public function carrito(): BelongsTo
    {
        return $this->belongsTo(Carrito::class, 'carrito_id');
    }

    /**
     * Relación con el producto
     */
    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    /**
     * Relación con la variante del producto
     */
    public function variante(): BelongsTo
    {
        return $this->belongsTo(VarianteProducto::class, 'variante_id');
    }

    /**
     * Obtener el subtotal del item
     */
    public function getSubtotalAttribute(): float
    {
        $precio = $this->producto->precio;
        if ($this->variante) {
            $precio += $this->variante->precio_adicional ?? 0;
        }
        return $precio * $this->cantidad;
    }
}
