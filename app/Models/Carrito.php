<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Carrito extends Model
{
    protected $table = 'carritos';
    protected $primaryKey = 'id';

    protected $fillable = [
        'usuario_id',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación con el usuario propietario del carrito
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    /**
     * Relación con los items del carrito
     */
    public function items(): HasMany
    {
        return $this->hasMany(CarritoItem::class, 'carrito_id');
    }

    /**
     * Obtener el total de items en el carrito
     */
    public function getTotalItemsAttribute(): int
    {
        return $this->items->sum('cantidad');
    }

    /**
     * Obtener el total del precio del carrito
     */
    public function getTotalPrecioAttribute(): float
    {
        return $this->items->sum(function ($item) {
            $precio = $item->producto->precio;
            if ($item->variante) {
                $precio += $item->variante->precio_adicional ?? 0;
            }
            return $precio * $item->cantidad;
        });
    }
}
