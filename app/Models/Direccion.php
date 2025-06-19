<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Direccion extends Model
{
    protected $table = 'direcciones';
    protected $primaryKey = 'direccion_id';
    public $timestamps = false;

    protected $fillable = [
        'usuario_id',
        'tipo_direccion',
        'departamento',
        'ciudad',
        'barrio',
        'direccion',
        'codigo_postal',
        'telefono',
        'instrucciones'
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'direccion_id');
    }
}

