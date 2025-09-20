<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MovimientoInventarioVariante extends Model
{
    protected $table = 'movimientos_inventario_variantes';
    protected $primaryKey = 'movimiento_id';
    public $timestamps = false;

    protected $fillable = [
        'variante_id',
        'tipo',
        'cantidad',
        'stock_anterior',
        'stock_nuevo',
        'motivo',
        'usuario_id',
        'referencia',
        'fecha_movimiento'
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'stock_anterior' => 'integer',
        'stock_nuevo' => 'integer',
        'fecha_movimiento' => 'datetime'
    ];

    // Relaciones
    public function variante(): BelongsTo
    {
        return $this->belongsTo(VarianteProducto::class, 'variante_id', 'variante_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id', 'usuario_id');
    }

    // Scopes para consultas comunes
    public function scopeEntradas($query)
    {
        return $query->where('tipo', 'entrada');
    }

    public function scopeSalidas($query)
    {
        return $query->where('tipo', 'salida');
    }

    public function scopeReservas($query)
    {
        return $query->where('tipo', 'reserva');
    }

    public function scopeVentas($query)
    {
        return $query->where('tipo', 'venta');
    }

    public function scopeLiberaciones($query)
    {
        return $query->where('tipo', 'liberacion_reserva');
    }

    public function scopePorFecha($query, $fechaInicio, $fechaFin = null)
    {
        $query->whereBetween('fecha_movimiento', [$fechaInicio, $fechaFin ?? now()]);
    }

    public function scopePorVariante($query, $varianteId)
    {
        return $query->where('variante_id', $varianteId);
    }

    // Métodos de utilidad
    public function getTipoMovimientoFormateadoAttribute(): string
    {
        return match($this->tipo) {
            'entrada' => 'Entrada',
            'salida' => 'Salida',
            'reserva' => 'Reserva',
            'venta' => 'Venta',
            'venta_confirmada' => 'Venta Confirmada',
            'liberacion_reserva' => 'Liberación',
            'ajuste' => 'Ajuste',
            'transferencia' => 'Transferencia',
            'devolucion' => 'Devolución',
            'merma' => 'Merma',
            'inventario_fisico' => 'Inventario Físico',
            default => ucfirst($this->tipo)
        };
    }

    public function getCantidadFormateadaAttribute(): string
    {
        $signo = match($this->tipo) {
            'entrada', 'liberacion_reserva', 'devolucion' => '+',
            'salida', 'reserva', 'venta', 'venta_confirmada', 'merma' => '-',
            default => ''
        };
        return $signo . $this->cantidad;
    }

    public function getClaseColorTipoAttribute(): string
    {
        return match($this->tipo) {
            'entrada', 'liberacion_reserva', 'devolucion' => 'text-green-600 bg-green-100',
            'salida', 'venta', 'venta_confirmada', 'merma' => 'text-red-600 bg-red-100',
            'reserva' => 'text-blue-600 bg-blue-100',
            'ajuste', 'transferencia', 'inventario_fisico' => 'text-yellow-600 bg-yellow-100',
            default => 'text-gray-600 bg-gray-100'
        };
    }
}
