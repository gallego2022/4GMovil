<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EspecificacionCategoria extends Model
{
    use HasFactory;

    protected $table = 'especificaciones_categoria';
    protected $primaryKey = 'especificacion_id';
    public $timestamps = true;

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
        'activo',
    ];

    protected $casts = [
        'requerido' => 'boolean',
        'activo' => 'boolean',
        'orden' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $attributes = [
        'activo' => true,
        'requerido' => false,
        'orden' => 0,
    ];

    /**
     * Relación con la categoría
     */
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class, 'categoria_id', 'categoria_id');
    }

    /**
     * Scope para especificaciones activas
     */
    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope para especificaciones requeridas
     */
    public function scopeRequeridas($query)
    {
        return $query->where('requerido', true);
    }

    /**
     * Scope para especificaciones por categoría
     */
    public function scopePorCategoria($query, $categoriaId)
    {
        return $query->where('categoria_id', $categoriaId);
    }

    /**
     * Scope para ordenar por orden
     */
    public function scopeOrdenadas($query)
    {
        return $query->orderBy('orden')->orderBy('etiqueta');
    }

    /**
     * Obtener las opciones como array
     */
    public function getOpcionesArrayAttribute()
    {
        if (empty($this->opciones)) {
            return [];
        }

        return array_map('trim', explode(',', (string) $this->opciones));
    }

    /**
     * Verificar si el campo es de tipo select
     */
    public function isSelectType(): bool
    {
        return in_array($this->tipo_campo, ['select', 'radio']);
    }

    /**
     * Verificar si el campo es de tipo checkbox
     */
    public function isCheckboxType(): bool
    {
        return $this->tipo_campo === 'checkbox';
    }

    /**
     * Verificar si el campo es de tipo número
     */
    public function isNumberType(): bool
    {
        return $this->tipo_campo === 'number';
    }

    /**
     * Verificar si el campo es de tipo fecha
     */
    public function isDateType(): bool
    {
        return $this->tipo_campo === 'date';
    }

    /**
     * Verificar si el campo es de tipo email
     */
    public function isEmailType(): bool
    {
        return $this->tipo_campo === 'email';
    }

    /**
     * Verificar si el campo es de tipo URL
     */
    public function isUrlType(): bool
    {
        return $this->tipo_campo === 'url';
    }

    /**
     * Verificar si el campo es de tipo textarea
     */
    public function isTextareaType(): bool
    {
        return $this->tipo_campo === 'textarea';
    }

    /**
     * Obtener el tipo de input HTML
     */
    public function getHtmlInputType(): string
    {
        $typeMap = [
            'text' => 'text',
            'textarea' => 'textarea',
            'number' => 'number',
            'select' => 'select',
            'checkbox' => 'checkbox',
            'radio' => 'radio',
            'date' => 'date',
            'email' => 'email',
            'url' => 'url',
        ];

        return $typeMap[$this->tipo_campo] ?? 'text';
    }

    /**
     * Obtener atributos adicionales para el input HTML
     */
    public function getHtmlAttributes(): array
    {
        $attributes = [];

        if ($this->requerido) {
            $attributes['required'] = 'required';
        }

        if ($this->isNumberType()) {
            $attributes['min'] = '0';
            $attributes['step'] = 'any';
        }

        if ($this->isDateType()) {
            $attributes['max'] = date('Y-m-d');
        }

        if ($this->isEmailType()) {
            $attributes['pattern'] = '[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$';
        }

        if ($this->isUrlType()) {
            $attributes['pattern'] = 'https?://.+';
        }

        return $attributes;
    }

    /**
     * Obtener el placeholder para el campo
     */
    public function getPlaceholder(): string
    {
        if ($this->isSelectType() && !empty($this->opciones)) {
            return 'Selecciona una opción';
        }

        if ($this->isCheckboxType()) {
            return '';
        }

        if ($this->isNumberType()) {
            return 'Ingresa un número';
        }

        if ($this->isDateType()) {
            return 'Selecciona una fecha';
        }

        if ($this->isEmailType()) {
            return 'ejemplo@correo.com';
        }

        if ($this->isUrlType()) {
            return 'https://ejemplo.com';
        }

        return 'Ingresa ' . strtolower($this->etiqueta);
    }

    /**
     * Obtener el label formateado
     */
    public function getFormattedLabel(): string
    {
        $label = $this->etiqueta;
        
        if ($this->requerido) {
            $label .= ' *';
        }
        
        if ($this->unidad) {
            $label .= " ({$this->unidad})";
        }
        
        return $label;
    }

    /**
     * Verificar si la especificación tiene opciones
     */
    public function hasOptions(): bool
    {
        return !empty($this->opciones) && $this->isSelectType();
    }

    /**
     * Obtener la siguiente posición de orden disponible
     */
    public static function getNextOrder($categoriaId): int
    {
        $maxOrder = self::where('categoria_id', $categoriaId)->max('orden');
        return ($maxOrder ?? 0) + 1;
    }

    /**
     * Mover la especificación a una nueva posición
     */
    public function moveToPosition(int $newPosition): bool
    {
        $oldPosition = $this->orden;
        
        if ($oldPosition === $newPosition) {
            return true;
        }

        try {
            if ($oldPosition < $newPosition) {
                // Mover hacia abajo
                self::where('categoria_id', $this->categoria_id)
                    ->where('orden', '>', $oldPosition)
                    ->where('orden', '<=', $newPosition)
                    ->decrement('orden');
            } else {
                // Mover hacia arriba
                self::where('categoria_id', $this->categoria_id)
                    ->where('orden', '>=', $newPosition)
                    ->where('orden', '<', $oldPosition)
                    ->increment('orden');
            }

            $this->orden = $newPosition;
            $this->save();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Boot del modelo
     */
    protected static function boot()
    {
        parent::boot();

        // Antes de crear, asignar orden si no se especifica
        static::creating(function ($especificacion) {
            if (!isset($especificacion->orden)) {
                $especificacion->orden = self::getNextOrder($especificacion->categoria_id);
            }
        });

        // Después de eliminar, reordenar las especificaciones restantes
        static::deleted(function ($especificacion) {
            self::where('categoria_id', $especificacion->categoria_id)
                ->where('orden', '>', $especificacion->orden)
                ->decrement('orden');
        });
    }
}
