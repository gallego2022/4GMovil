<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ImagenVariante extends Model
{
    protected $table = 'imagenes_variantes';
    protected $primaryKey = 'imagen_variante_id';

    protected $fillable = [
        'variante_id',
        'ruta_imagen',
        'nombre_archivo',
        'tipo_mime',
        'tamaño_bytes',
        'orden',
        'es_principal'
    ];

    protected $casts = [
        'es_principal' => 'boolean',
        'orden' => 'integer',
        'tamaño_bytes' => 'integer'
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
        return Storage::url($this->ruta_imagen);
    }

    /**
     * Obtener el tamaño formateado del archivo
     */
    public function getTamañoFormateadoAttribute()
    {
        $bytes = $this->tamaño_bytes;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Scope para obtener solo imágenes principales
     */
    public function scopePrincipales($query)
    {
        return $query->where('es_principal', true);
    }

    /**
     * Scope para ordenar por orden
     */
    public function scopeOrdenadas($query)
    {
        return $query->orderBy('orden');
    }
}
