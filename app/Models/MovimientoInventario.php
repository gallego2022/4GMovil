<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Usuario;

class MovimientoInventario extends Model
{
    protected $table = 'movimientos_inventario';
    protected $primaryKey = 'movimiento_id';
    public $timestamps = false;

    protected $fillable = [
        'producto_id',
        'tipo_movimiento',
        'cantidad',
        'stock_anterior',
        'stock_nuevo',
        'motivo',
        'usuario_id',
        'pedido_id',
        'referencia',
        'costo_unitario'
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'stock_anterior' => 'integer',
        'stock_nuevo' => 'integer',
        'costo_unitario' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relaciones
    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }

    // Scopes para filtros
    public function scopeEntradas($query)
    {
        return $query->where('tipo_movimiento', 'entrada');
    }

    public function scopeSalidas($query)
    {
        return $query->where('tipo_movimiento', 'salida');
    }

    public function scopeAjustes($query)
    {
        return $query->where('tipo_movimiento', 'ajuste');
    }

    public function scopeDevoluciones($query)
    {
        return $query->where('tipo_movimiento', 'devolucion');
    }

    public function scopeReservas($query)
    {
        return $query->where('tipo_movimiento', 'reserva');
    }

    public function scopeLiberaciones($query)
    {
        return $query->where('tipo_movimiento', 'liberacion');
    }

    public function scopePorProducto($query, $productoId)
    {
        return $query->where('producto_id', $productoId);
    }

    public function scopePorFecha($query, $fechaInicio, $fechaFin = null)
    {
        $query->whereBetween('created_at', [$fechaInicio, $fechaFin ?? now()]);
        return $query;
    }

    // Métodos de utilidad
    public function getTipoMovimientoLabelAttribute(): string
    {
        return match($this->tipo_movimiento) {
            'entrada' => 'Entrada',
            'salida' => 'Salida',
            'ajuste' => 'Ajuste',
            'devolucion' => 'Devolución',
            'reserva' => 'Reserva',
            'liberacion' => 'Liberación',
            default => 'Desconocido'
        };
    }

    public function getClaseColorAttribute(): string
    {
        return match($this->tipo_movimiento) {
            'entrada' => 'text-green-600 bg-green-50',
            'salida' => 'text-red-600 bg-red-50',
            'ajuste' => 'text-yellow-600 bg-yellow-50',
            'devolucion' => 'text-blue-600 bg-blue-50',
            'reserva' => 'text-purple-600 bg-purple-50',
            'liberacion' => 'text-indigo-600 bg-indigo-50',
            default => 'text-gray-600 bg-gray-50'
        };
    }
} 