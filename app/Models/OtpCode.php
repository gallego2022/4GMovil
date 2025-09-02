<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class OtpCode extends Model
{
    use HasFactory;

    protected $table = 'otp_codes';
    protected $primaryKey = 'otp_id';

    protected $fillable = [
        'usuario_id',
        'codigo',
        'tipo',
        'fecha_expiracion',
        'usado'
    ];

    protected $casts = [
        'fecha_expiracion' => 'datetime',
        'usado' => 'boolean'
    ];

    // Relación con Usuario
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    // Generar código OTP de 6 dígitos
    public static function generarCodigo(): string
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    // Crear un nuevo código OTP
    public static function crear($usuarioId, $tipo, $minutosExpiracion = 10): self
    {
        // Invalidar códigos anteriores del mismo tipo para el usuario
        self::where('usuario_id', $usuarioId)
            ->where('tipo', $tipo)
            ->where('usado', false)
            ->update(['usado' => true]);

        return self::create([
            'usuario_id' => $usuarioId,
            'codigo' => self::generarCodigo(),
            'tipo' => $tipo,
            'fecha_expiracion' => now()->addMinutes($minutosExpiracion),
            'usado' => false
        ]);
    }

    // Verificar código OTP
    public static function verificar($usuarioId, $codigo, $tipo): bool
    {
        $otp = self::where('usuario_id', $usuarioId)
            ->where('codigo', $codigo)
            ->where('tipo', $tipo)
            ->where('usado', false)
            ->where('fecha_expiracion', '>', now())
            ->first();

        if ($otp) {
            $otp->update(['usado' => true]);
            return true;
        }

        return false;
    }

    // Verificar si el usuario tiene un código OTP válido
    public static function tieneCodigoValido($usuarioId, $tipo): bool
    {
        return self::where('usuario_id', $usuarioId)
            ->where('tipo', $tipo)
            ->where('usado', false)
            ->where('fecha_expiracion', '>', now())
            ->exists();
    }

    // Obtener el código OTP válido actual
    public static function obtenerCodigoValido($usuarioId, $tipo): ?self
    {
        return self::where('usuario_id', $usuarioId)
            ->where('tipo', $tipo)
            ->where('usado', false)
            ->where('fecha_expiracion', '>', now())
            ->first();
    }

    // Limpiar códigos expirados
    public static function limpiarExpirados(): int
    {
        return self::where('fecha_expiracion', '<', now())->delete();
    }

    // Verificar si el código está expirado
    public function estaExpirado(): bool
    {
        return $this->fecha_expiracion->isPast();
    }

    // Verificar si el código está usado
    public function estaUsado(): bool
    {
        return $this->usado;
    }

    // Obtener tiempo restante en minutos
    public function tiempoRestante(): int
    {
        return max(0, now()->diffInMinutes($this->fecha_expiracion, false));
    }
}
