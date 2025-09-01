<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EspecificacionCategoria extends Model
{
    use HasFactory;

    protected $table = 'especificaciones_categoria';
    protected $primaryKey = 'especificacion_id';

    protected $fillable = [
        'categoria_id',
        'nombre_campo',
        'etiqueta',
        'tipo_campo',
        'opciones',
        'unidad',
        'descripcion',
        'requerido',
        'orden',
        'activo'
    ];

    protected $casts = [
        'opciones' => 'array',
        'requerido' => 'boolean',
        'activo' => 'boolean',
        'orden' => 'integer'
    ];

    // Relaciones
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id', 'categoria_id');
    }

    public function especificacionesProducto()
    {
        return $this->hasMany(EspecificacionProducto::class, 'especificacion_id', 'especificacion_id');
    }

    // Scopes
    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }

    public function scopePorCategoria($query, $categoriaId)
    {
        return $query->where('categoria_id', $categoriaId);
    }

    public function scopeOrdenadas($query)
    {
        return $query->orderBy('orden', 'asc');
    }

    // Métodos
    public function getOpcionesArrayAttribute()
    {
        if (is_string($this->opciones)) {
            return json_decode($this->opciones, true) ?? [];
        }
        return $this->opciones ?? [];
    }

    public function getEtiquetaCompletaAttribute()
    {
        if ($this->unidad) {
            return $this->etiqueta . ' (' . $this->unidad . ')';
        }
        return $this->etiqueta;
    }
}
