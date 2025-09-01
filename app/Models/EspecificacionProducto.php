<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EspecificacionProducto extends Model
{
    use HasFactory;

    protected $table = 'especificaciones_producto';
    protected $primaryKey = 'especificacion_producto_id';

    protected $fillable = [
        'producto_id',
        'especificacion_id',
        'valor'
    ];

    // Relaciones
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id', 'producto_id');
    }

    public function especificacionCategoria()
    {
        return $this->belongsTo(EspecificacionCategoria::class, 'especificacion_id', 'especificacion_id');
    }
}
