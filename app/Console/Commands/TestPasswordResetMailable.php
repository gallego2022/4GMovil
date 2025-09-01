<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Usuario;
use App\Mail\RestablecerContrasena;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\DB;

class TestPasswordResetMailable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:password-reset-mailable {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Probar el nuevo método de envío usando Mailable para restablecimiento de contraseña';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info("🔐 Probando nuevo método Mailable para restablecimiento de contraseña...");
        $this->info("📧 Email: {$email}");
        
        // Verificar si el usuario existe
        $usuario = Usuario::where('correo_electronico', $email)->first();
        
        if (!$usuario) {
            $this->error("❌ Usuario no encontrado con el email: {$email}");
            return 1;
        }
        
        $this->info("✅ Usuario encontrado: {$usuario->nombre_usuario}");
        
        try {
            $this->info("\n📧 Enviando email usando sistema real de Laravel...");
            
            // Usar el sistema real de Laravel para generar el token
            $status = Password::sendResetLink(['correo_electronico' => $email]);
            
            if ($status === Password::RESET_LINK_SENT) {
                $this->info("✅ Token generado y email enviado exitosamente!");
                
                // Obtener el token real de la base de datos
                $tokenData = DB::table('password_reset_tokens')
                    ->where('email', $email)
                    ->first();
                
                if ($tokenData) {
                    $this->info("🔑 Token real generado: " . substr($tokenData->token, 0, 20) . "...");
                    
                    // Generar URL con el token real
                    $resetUrl = route('password.reset', [
                        'token' => $tokenData->token,
                        'email' => $email,
                    ], true);
                    
                    $this->info("🔗 URL real generada: {$resetUrl}");
                    
                    // Ahora enviar el email personalizado con el token real
                    $this->info("\n📧 Enviando email personalizado con Mailable...");
                    Mail::to($usuario->correo_electronico)->send(new RestablecerContrasena($usuario, $resetUrl));
                    
                    $this->info("✅ Email personalizado enviado exitosamente!");
                    $this->info("📬 Revisa tu bandeja de entrada (y carpeta de spam)");
                    
                    // Verificar que se envió correctamente
                    $this->info("\n🔍 Verificando envío...");
                    $this->info("✅ Mailable RestablecerContrasena enviado");
                    $this->info("✅ Vista: correo.restablecer-contrasena");
                    $this->info("✅ Usuario: {$usuario->nombre_usuario}");
                    $this->info("✅ Email: {$usuario->correo_electronico}");
                    $this->info("✅ Token válido en base de datos");
                    
                } else {
                    $this->error("❌ No se pudo obtener el token de la base de datos");
                }
                
            } else {
                $this->error("❌ Error al generar token: " . __($status));
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Error al enviar email:");
            $this->error($e->getMessage());
            Log::error('Error en prueba de Mailable: ' . $e->getMessage());
            
            $this->info("\n🔍 Posibles soluciones:");
            $this->info("1. Verifica que el Mailable RestablecerContrasena existe");
            $this->info("2. Verifica que la vista correo.restablecer-contrasena existe");
            $this->info("3. Revisa los logs en storage/logs/laravel.log");
        }
        
        return 0;
    }
}
