<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImagenProducto extends Model
{
    protected $table = 'imagenes_productos';
    protected $primaryKey = 'imagen_id';

    protected $fillable = [
        'producto_id',
        'ruta_imagen',
        'alt_text',
        'titulo',
        'orden',
        'principal',
        'activo'
    ];

    protected $casts = [
        'principal' => 'boolean',
        'activo' => 'boolean',
        'orden' => 'integer'
    ];

    /**
     * Relación con el producto
     */
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    /**
     * Scope para imágenes activas
     */
    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope para imagen principal
     */
    public function scopePrincipal($query)
    {
        return $query->where('principal', true);
    }

    /**
     * Scope para ordenar por orden
     */
    public function scopeOrdenadas($query)
    {
        return $query->orderBy('orden', 'asc');
    }

    /**
     * Obtener la URL completa de la imagen
     */
    public function getUrlCompletaAttribute()
    {
        if (str_starts_with($this->ruta_imagen, 'http')) {
            return $this->ruta_imagen;
        }
        
        return asset('storage/' . $this->ruta_imagen);
    }

    /**
     * Verificar si la imagen es principal
     */
    public function esPrincipal(): bool
    {
        return $this->principal;
    }

    /**
     * Marcar como imagen principal
     */
    public function marcarComoPrincipal(): void
    {
        // Desmarcar otras imágenes del mismo producto como principales
        $this->producto->imagenes()
            ->where('imagen_id', '!=', $this->imagen_id)
            ->update(['principal' => false]);
        
        // Marcar esta imagen como principal
        $this->update(['principal' => true]);
    }
}
