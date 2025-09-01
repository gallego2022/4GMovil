<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $table = 'pedidos';
    protected $primaryKey = 'pedido_id';

    protected $fillable = [
        'usuario_id', 'direccion_id', 'fecha_pedido',
        'estado_id', 'total'
    ];

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
}

