<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Usuario;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Log;

class TestPasswordResetEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:password-reset-email {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Probar específicamente el email de restablecimiento de contraseña';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info("🔐 Probando email de restablecimiento de contraseña para: {$email}");
        
        // Verificar si el usuario existe
        $usuario = Usuario::where('correo_electronico', $email)->first();
        
        if (!$usuario) {
            $this->error("❌ Usuario no encontrado con el email: {$email}");
            return 1;
        }
        
        $this->info("✅ Usuario encontrado: {$usuario->nombre_usuario}");
        
        // Verificar que el usuario implementa CanResetPassword
        if (!($usuario instanceof \Illuminate\Contracts\Auth\CanResetPassword)) {
            $this->error("❌ El usuario NO implementa CanResetPassword");
            return 1;
        }
        
        $this->info("✅ Usuario implementa CanResetPassword");
        
        // Verificar método sendPasswordResetNotification
        if (!method_exists($usuario, 'sendPasswordResetNotification')) {
            $this->error("❌ El usuario NO tiene método sendPasswordResetNotification");
            return 1;
        }
        
        $this->info("✅ Usuario tiene método sendPasswordResetNotification");
        
        // Simular el envío del enlace de restablecimiento
        try {
            $this->info("\n📧 Enviando email de restablecimiento...");
            
            $status = Password::sendResetLink(['correo_electronico' => $email]);
            
            if ($status === Password::RESET_LINK_SENT) {
                $this->info("✅ Email de restablecimiento enviado exitosamente!");
                $this->info("📬 Revisa tu bandeja de entrada (y carpeta de spam)");
                $this->info("📋 Mensaje: " . __($status));
                
                // Verificar que se creó el token en la base de datos
                $this->checkTokenInDatabase($email);
                
            } else {
                $this->error("❌ Error al enviar email de restablecimiento");
                $this->error("📋 Estado: " . __($status));
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Excepción: " . $e->getMessage());
            Log::error('Error en prueba de restablecimiento de contraseña: ' . $e->getMessage());
        }
        
        return 0;
    }
    
    private function checkTokenInDatabase($email)
    {
        try {
            $this->info("\n🗄️ Verificando token en base de datos...");
            
            $token = \Illuminate\Support\Facades\DB::table('password_reset_tokens')
                ->where('email', $email)
                ->first();
            
            if ($token) {
                $this->info("✅ Token encontrado en base de datos");
                $this->info("🔑 Token: " . substr($token->token, 0, 20) . "...");
                $this->info("⏰ Creado: " . $token->created_at);
                
                // Verificar que el token no haya expirado
                $created = \Carbon\Carbon::parse($token->created_at);
                $expires = $created->addMinutes(60);
                $now = \Carbon\Carbon::now();
                
                if ($now->lt($expires)) {
                    $this->info("✅ Token válido (expira en " . $now->diffForHumans($expires) . ")");
                } else {
                    $this->warn("⚠️ Token expirado");
                }
                
            } else {
                $this->warn("⚠️ No se encontró token en la base de datos");
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Error al verificar token: " . $e->getMessage());
        }
    }
}
