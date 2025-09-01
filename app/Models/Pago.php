<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Pago extends Model
{
    protected $table = 'pagos';
    protected $primaryKey = 'pago_id';
    public $timestamps = false;

    protected $fillable = [
        'pedido_id',
        'monto',
        'metodo_id',
        'fecha_pago',
        'estado'
    ];

    protected $dates = ['fecha_pago'];

    // Mutador para asegurar que fecha_pago siempre sea un objeto Carbon
    public function setFechaPagoAttribute($value)
    {
        $this->attributes['fecha_pago'] = $value instanceof Carbon ? $value : Carbon::parse($value);
    }

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }

    public function metodoPago()
    {
        return $this->belongsTo(MetodoPago::class, 'metodo_id', 'metodo_id');
    }
}
