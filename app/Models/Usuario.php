<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Notifications\VerificarCorreoElectronico;
use App\Notifications\RecuperarContrasena;
use Illuminate\Support\Facades\Log;
use App\Models\Mail;

class Usuario extends Authenticatable implements MustVerifyEmail
{
    use Notifiable, HasFactory;

    protected $table = 'usuarios';
    protected $primaryKey = 'usuario_id';

    protected $fillable = [
        'nombre_usuario',
        'correo_electronico',
        'contrasena',
        'telefono',
        'foto_perfil',
        'estado',
        'rol',
        'fecha_registro',
        'created_at',
        'updated_at',
        'email_verified_at'
    ];

    protected $casts = [
        'estado' => 'boolean',
        'fecha_registro' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'email_verified_at' => 'datetime'
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

    public function sendEmailVerificationNotification()
    {
        Log::info("Paso 1: 🔔 Enviando notificación personalizada de verificación a: {$this->correo_electronico}");
        $this->notify(new VerificarCorreoElectronico);
    }


    public function sendPasswordResetNotification($token)
    {
        $this->notify(new RecuperarContrasena($token));
    }
}
