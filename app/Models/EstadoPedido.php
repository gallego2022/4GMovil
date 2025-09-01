<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoPedido extends Model
{
    protected $table = 'estados_pedido';
    protected $primaryKey = 'estado_id';
    public $timestamps = true;

    protected $fillable = [
        'nombre',
        'descripcion',
        'color',
        'orden',
        'estado'
    ];

    protected $casts = [
        'estado' => 'boolean',
        'orden' => 'integer'
    ];

    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'estado_id');
    }

    // Verificar si el estado está activo
    public function isActive()
    {
        return $this->estado;
    }

    // Obtener el color del estado
    public function getColor()
    {
        return $this->color ?: '#3b82f6';
    }
}
