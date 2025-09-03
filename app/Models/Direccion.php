<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Direccion extends Model
{
    protected $table = 'direcciones';
    protected $primaryKey = 'direccion_id';
    public $timestamps = true;

    protected $fillable = [
        'usuario_id',
        'nombre_destinatario',
        'telefono',
        'calle',
        'numero',
        'piso',
        'departamento',
        'codigo_postal',
        'ciudad',
        'provincia',
        'pais',
        'referencias',
        'predeterminada',
        'activo',
        'tipo_direccion'
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'direccion_id');
    }

    // Accessor para la dirección completa
    public function getDireccionCompletaAttribute()
    {
        $direccion = $this->calle . ' ' . $this->numero;
        
        if ($this->piso) {
            $direccion .= ', Piso ' . $this->piso;
        }
        
        if ($this->departamento) {
            $direccion .= ', Depto ' . $this->departamento;
        }
        
        return $direccion;
    }

    // Accessor para el tipo de dirección (con valor por defecto)
    public function getTipoDireccionAttribute($value)
    {
        return $value ?: 'casa';
    }
}

