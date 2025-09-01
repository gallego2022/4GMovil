<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImagenProducto extends Model
{
    protected $table = 'imagenes_productos';
    protected $primaryKey = 'imagen_id';
    public $timestamps = false;

    protected $fillable = ['producto_id', 'ruta_imagen'];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
}
