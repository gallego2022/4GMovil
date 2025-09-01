<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $table = 'categorias';
    protected $primaryKey = 'categoria_id';
    public $timestamps = false;

    protected $fillable = ['nombre_categoria'];

    public function productos()
    {
        return $this->hasMany(Producto::class, 'categoria_id');
    }

    public function especificaciones()
    {
        return $this->hasMany(EspecificacionCategoria::class, 'categoria_id', 'categoria_id');
    }
}
