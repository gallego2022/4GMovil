<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoPedido extends Model
{
    protected $table = 'estados_pedido';
    protected $primaryKey = 'estado_id';
    public $timestamps = false;

    protected $fillable = ['nombre_estado'];

    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'estado_id');
    }
}
