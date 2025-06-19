<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MetodoPago extends Model
{
    protected $table = 'metodos_pago';
    protected $primaryKey = 'metodo_id';
    public $timestamps = true;

    protected $fillable = ['nombre_metodo'];

    public function pagos()
    {
        return $this->hasMany(Pago::class, 'metodo_id');
    }
}
