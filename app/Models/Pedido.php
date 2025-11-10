<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Pedido extends Model
{
    protected $table = 'pedidos';
    protected $primaryKey = 'pedido_id';

    protected $fillable = [
        'usuario_id', 'direccion_id', 'fecha_pedido',
        'estado_id', 'total', 'numero_pedido'
    ];

    protected $dates = ['fecha_pedido'];

    public $timestamps = false;

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'pedido_id';
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function direccion()
    {
        return $this->belongsTo(Direccion::class, 'direccion_id');
    }

    public function estado()
    {
        return $this->belongsTo(EstadoPedido::class, 'estado_id');
    }

    public function detalles()
    {
        return $this->hasMany(DetallePedido::class, 'pedido_id');
    }

    public function pago()
    {
        return $this->hasOne(Pago::class, 'pedido_id');
    }

    public function resenas()
    {
        return $this->hasMany(Resena::class, 'pedido_id');
    }

    /**
     * Verificar si el pedido está confirmado
     */
    public function isConfirmado(): bool
    {
        return $this->estado_id === 2; // ID 2 = Confirmado
    }

    /**
     * Verificar si el pedido puede ser calificado
     * Permite calificar si está confirmado y no todos los productos tienen reseñas
     */
    public function puedeCalificar(): bool
    {
        if (!$this->isConfirmado()) {
            return false;
        }
        
        // Verificar si hay productos sin calificar
        $productosConResena = $this->resenas->pluck('producto_id')->unique();
        $productosEnPedido = $this->detalles->pluck('producto_id')->unique();
        
        // Si hay productos sin reseña, se puede calificar
        return $productosConResena->count() < $productosEnPedido->count();
    }
}

