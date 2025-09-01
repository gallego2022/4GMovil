<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MetodoPago extends Model
{
    protected $table = 'metodos_pago';
    protected $primaryKey = 'metodo_id';
    public $timestamps = true;

    protected $fillable = [
        'nombre',
        'descripcion',
        'tipo',
        'configuracion',
        'estado'
    ];

    protected $casts = [
        'configuracion' => 'array',
        'estado' => 'boolean'
    ];

    public function pagos()
    {
        return $this->hasMany(Pago::class, 'metodo_id');
    }

    // Verificar si el método está habilitado
    public function isEnabled()
    {
        return $this->estado;
    }

    // Obtener configuración específica
    public function getConfig($key, $default = null)
    {
        return data_get($this->configuracion, $key, $default);
    }

    // Verificar si es método Stripe
    public function isStripe()
    {
        return $this->tipo === 'stripe';
    }
}
