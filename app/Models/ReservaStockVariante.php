<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReservaStockVariante extends Model
{
    protected $table = 'reservas_stock_variantes';
    protected $primaryKey = 'reserva_id';

    protected $fillable = [
        'variante_id',
        'usuario_id',
        'cantidad',
        'fecha_reserva',
        'fecha_expiracion',
        'estado',
        'referencia_pedido',
        'motivo'
    ];

    protected $casts = [
        'fecha_reserva' => 'datetime',
        'fecha_expiracion' => 'datetime',
        'cantidad' => 'integer'
    ];

    // Estados de la reserva
    const ESTADO_ACTIVA = 'activa';
    const ESTADO_CONFIRMADA = 'confirmada';
    const ESTADO_EXPIRADA = 'expirada';
    const ESTADO_CANCELADA = 'cancelada';

    // Relaciones
    public function variante(): BelongsTo
    {
        return $this->belongsTo(VarianteProducto::class, 'variante_id', 'variante_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id', 'usuario_id');
    }

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class, 'referencia_pedido', 'pedido_id');
    }

    // Métodos estáticos para crear reservas
    public static function crearReserva(
        int $varianteId,
        int $usuarioId,
        int $cantidad,
        ?string $referenciaPedido = null,
        string $motivo = 'Reserva de compra',
        int $minutosExpiracion = 30
    ): ?self {
        return DB::transaction(function () use ($varianteId, $usuarioId, $cantidad, $referenciaPedido, $motivo, $minutosExpiracion) {
            $variante = VarianteProducto::find($varianteId);
            
            if (!$variante) {
                Log::error('Variante no encontrada para reserva', ['variante_id' => $varianteId]);
                return null;
            }

            // Verificar stock disponible
            if (!$variante->tieneStockSuficiente($cantidad)) {
                Log::warning('Stock insuficiente para reserva', [
                    'variante_id' => $varianteId,
                    'cantidad_solicitada' => $cantidad,
                    'stock_disponible' => $variante->stock_disponible
                ]);
                return null;
            }

            // Crear la reserva
            $reserva = self::create([
                'variante_id' => $varianteId,
                'usuario_id' => $usuarioId,
                'cantidad' => $cantidad,
                'fecha_reserva' => now(),
                'fecha_expiracion' => now()->addMinutes($minutosExpiracion),
                'estado' => self::ESTADO_ACTIVA,
                'referencia_pedido' => $referenciaPedido,
                'motivo' => $motivo
            ]);

            // Reservar el stock en la variante
            $reservaExitosa = $variante->reservarStock(
                $cantidad,
                "Reserva #{$reserva->reserva_id} - {$motivo}",
                $usuarioId,
                $reserva->reserva_id
            );

            if (!$reservaExitosa) {
                throw new \Exception('No se pudo reservar el stock');
            }

            Log::info('Reserva de stock creada', [
                'reserva_id' => $reserva->reserva_id,
                'variante_id' => $varianteId,
                'cantidad' => $cantidad,
                'usuario_id' => $usuarioId
            ]);

            return $reserva;
        });
    }

    // Métodos de instancia
    public function confirmar(): bool
    {
        return DB::transaction(function () {
            if ($this->estado !== self::ESTADO_ACTIVA) {
                Log::warning('No se puede confirmar una reserva que no está activa', [
                    'reserva_id' => $this->reserva_id,
                    'estado_actual' => $this->estado
                ]);
                return false;
            }

            $this->update(['estado' => self::ESTADO_CONFIRMADA]);

            // Confirmar la reserva en la variante
            $this->variante->confirmarReserva(
                $this->cantidad,
                "Venta confirmada - Reserva #{$this->reserva_id}",
                $this->usuario_id,
                $this->reserva_id
            );

            Log::info('Reserva confirmada', ['reserva_id' => $this->reserva_id]);
            return true;
        });
    }

    public function cancelar(string $motivo = 'Cancelación manual'): bool
    {
        return DB::transaction(function () use ($motivo) {
            if ($this->estado !== self::ESTADO_ACTIVA) {
                Log::warning('No se puede cancelar una reserva que no está activa', [
                    'reserva_id' => $this->reserva_id,
                    'estado_actual' => $this->estado
                ]);
                return false;
            }

            $this->update(['estado' => self::ESTADO_CANCELADA]);

            // Liberar el stock reservado
            $this->variante->liberarReserva(
                $this->cantidad,
                "Cancelación - {$motivo} - Reserva #{$this->reserva_id}",
                $this->usuario_id,
                $this->reserva_id
            );

            Log::info('Reserva cancelada', [
                'reserva_id' => $this->reserva_id,
                'motivo' => $motivo
            ]);
            return true;
        });
    }

    public function expirar(): bool
    {
        return DB::transaction(function () {
            if ($this->estado !== self::ESTADO_ACTIVA) {
                return false;
            }

            $this->update(['estado' => self::ESTADO_EXPIRADA]);

            // Liberar el stock reservado
            $this->variante->liberarReserva(
                $this->cantidad,
                "Expiración automática - Reserva #{$this->reserva_id}",
                $this->usuario_id,
                $this->reserva_id
            );

            Log::info('Reserva expirada automáticamente', ['reserva_id' => $this->reserva_id]);
            return true;
        });
    }

    // Scopes para consultas comunes
    public function scopeActivas($query)
    {
        return $query->where('estado', self::ESTADO_ACTIVA);
    }

    public function scopeExpiradas($query)
    {
        return $query->where('estado', self::ESTADO_ACTIVA)
                    ->where('fecha_expiracion', '<', now());
    }

    public function scopePorUsuario($query, int $usuarioId)
    {
        return $query->where('usuario_id', $usuarioId);
    }

    public function scopePorVariante($query, int $varianteId)
    {
        return $query->where('variante_id', $varianteId);
    }

    // Métodos de utilidad
    public function estaExpirada(): bool
    {
        return $this->fecha_expiracion < now();
    }

    public function puedeConfirmar(): bool
    {
        return $this->estado === self::ESTADO_ACTIVA && !$this->estaExpirada();
    }

    public function puedeCancelar(): bool
    {
        return $this->estado === self::ESTADO_ACTIVA;
    }
}
