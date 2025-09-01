<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Usuario;
use App\Mail\OtpVerification;
use App\Mail\RestablecerContrasena;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class TestBasicMailables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:basic-mailables {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Probar los Mailable básicos (OTP y restablecimiento)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info("🔐 Probando Mailable básicos...");
        $this->info("📧 Email: {$email}");
        
        // Verificar si el usuario existe
        $usuario = Usuario::where('correo_electronico', $email)->first();
        
        if (!$usuario) {
            $this->error("❌ Usuario no encontrado con el email: {$email}");
            return 1;
        }
        
        $this->info("✅ Usuario encontrado: {$usuario->nombre_usuario}");
        
        $this->info("\n📧 Enviando emails de prueba...");
        
        try {
            // 1. Verificación OTP
            $this->testVerificacionCorreo($usuario);
            
            // 2. Restablecimiento de contraseña
            $this->testRestablecerContrasena($usuario);
            
            $this->info("\n✅ Todos los Mailable básicos se enviaron exitosamente!");
            $this->info("📬 Revisa tu bandeja de entrada (y carpeta de spam)");
            
        } catch (\Exception $e) {
            $this->error("❌ Error al enviar emails: " . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
    
    private function testVerificacionCorreo($usuario)
    {
        $this->info("   📧 Enviando verificación OTP...");
        
        // Generar código OTP de prueba
        $codigo = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        Mail::to($usuario->correo_electronico)->send(new OtpVerification($usuario, $codigo, 'email_verification', 10));
        
        $this->info("      ✅ Verificación OTP enviado");
    }
    
    private function testRestablecerContrasena($usuario)
    {
        $this->info("   🔐 Enviando restablecimiento de contraseña...");
        
        $token = Str::random(60);
        $resetUrl = route('password.reset', [
            'token' => $token,
            'email' => $usuario->correo_electronico,
        ], true);
        
        Mail::to($usuario->correo_electronico)->send(new RestablecerContrasena($usuario, $resetUrl));
        
        $this->info("      ✅ Restablecimiento de contraseña enviado");
    }
}
