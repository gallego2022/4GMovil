<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetallePedido extends Model
{
    protected $table = 'detalles_pedido';
    protected $primaryKey = 'detalle_id';
    public $timestamps = false;

    protected $fillable = [
        'pedido_id', 'producto_id', 'variante_id', 'cantidad', 'precio_unitario', 'subtotal'
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
}
