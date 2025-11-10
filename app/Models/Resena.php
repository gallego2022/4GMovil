<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resena extends Model
{
    protected $table = 'resenas';
    protected $primaryKey = 'resena_id';
    public $timestamps = true;

    protected $fillable = [
        'usuario_id', 'producto_id', 'pedido_id', 'calificacion',
        'comentario', 'verificada', 'activa'
    ];

    protected $casts = [
        'verificada' => 'boolean',
        'activa' => 'boolean',
        'calificacion' => 'integer',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }
}
