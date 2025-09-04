<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImagenVariante extends Model
{
    protected $table = 'imagenes_variantes';
    protected $primaryKey = 'imagen_id';

    protected $fillable = [
        'variante_id',
        'url_imagen',
        'alt_text',
        'orden',
        'principal'
    ];

    protected $casts = [
        'principal' => 'boolean',
        'orden' => 'integer'
    ];

    public function variante()
    {
        return $this->belongsTo(VarianteProducto::class, 'variante_id');
    }

    /**
     * Obtener la URL pública de la imagen
     */
    public function getUrlAttribute()
    {
        return $this->url_imagen;
    }

    /**
     * Obtener la URL completa de la imagen (para compatibilidad)
     */
    public function getUrlCompletaAttribute()
    {
        if (str_starts_with($this->url_imagen, 'http')) {
            return $this->url_imagen;
        }
        
        return asset('storage/' . $this->url_imagen);
    }



    /**
     * Scope para obtener solo imágenes principales
     */
    public function scopePrincipales($query)
    {
        return $query->where('principal', true);
    }

    /**
     * Scope para ordenar por orden
     */
    public function scopeOrdenadas($query)
    {
        return $query->orderBy('orden');
    }
}
