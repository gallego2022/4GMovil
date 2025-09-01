<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Mail\RestablecerContrasena;
use App\Mail\OtpVerification;
use App\Models\OtpCode;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
use Laravel\Cashier\Billable;

class Usuario extends Authenticatable implements MustVerifyEmail, CanResetPassword
{
    use Notifiable, HasFactory, Billable;

    protected $table = 'usuarios';
    protected $primaryKey = 'usuario_id';

    protected $fillable = [
        'nombre_usuario',
        'correo_electronico',
        'google_id',
        'contrasena',
        'telefono',
        'foto_perfil',
        'estado',
        'rol',
        'fecha_registro',
        'created_at',
        'updated_at',
        'email_verified_at',
        'stripe_id',
        'pm_type',
        'pm_last_four',
        'trial_ends_at'
    ];

    protected $casts = [
        'estado' => 'boolean',
        'fecha_registro' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'email_verified_at' => 'datetime',
        'trial_ends_at' => 'datetime'
    ];

    public $timestamps = true;

    protected $hidden = ['contrasena'];

    public function getEmailForVerification()
    {
        return $this->correo_electronico;
    }


    // Laravel espera que el password esté en un campo llamado 'password',
    public function getAuthPassword()
    {
        return $this->contrasena;
    }

    // Verificar si el usuario puede hacer login manual (tiene contraseña)
    public function canLoginManually()
    {
        return !is_null($this->contrasena);
    }

    // Verificar si es un usuario de Google OAuth
    public function isGoogleUser()
    {
        return !is_null($this->google_id);
    }

    // Si usas restablecimiento de contraseña, puedes especificar el email así:
    public function getEmailForPasswordReset()
    {
        return $this->correo_electronico;
    }

    // Relaciones
    public function direcciones()
    {
        return $this->hasMany(Direccion::class, 'usuario_id');
    }

    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'usuario_id');
    }

    public function resenas()
    {
        return $this->hasMany(Resena::class, 'usuario_id');
    }

    // Accessor para el nombre
    public function getNombreAttribute()
    {
        return $this->nombre_usuario;
    }

    public function sendEmailVerificationNotification()
    {
        Log::info("Paso 1: 🔔 Enviando notificación OTP de verificación a: {$this->correo_electronico}");
        
        // Crear código OTP
        $otp = OtpCode::crear($this->usuario_id, 'email_verification', 10);
        
        // Enviar correo con OTP
        Mail::to($this->correo_electronico)->send(new OtpVerification($this, $otp->codigo, 'email_verification', 10));
    }


    public function sendPasswordResetNotification($token)
    {
        Log::info("🔔 Enviando notificación OTP de restablecimiento a: {$this->correo_electronico}");
        
        // Crear código OTP para restablecimiento
        $otp = OtpCode::crear($this->usuario_id, 'password_reset', 10);
        
        // Enviar correo con OTP
        Mail::to($this->correo_electronico)->send(new OtpVerification($this, $otp->codigo, 'password_reset', 10));
    }
}
