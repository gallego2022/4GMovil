<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\OtpCode;
use App\Models\Usuario;

class TestOtpVerification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'otp:test-verification {email} {codigo}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Probar la verificación de un código OTP';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $codigo = $this->argument('codigo');

        $this->info("🧪 Probando verificación OTP...");
        $this->info("📧 Email: {$email}");
        $this->info("🔑 Código: {$codigo}");

        // Buscar usuario
        $usuario = Usuario::where('correo_electronico', $email)->first();
        
        if (!$usuario) {
            $this->error("❌ Usuario no encontrado con email: {$email}");
            return 1;
        }

        $this->info("✅ Usuario encontrado: {$usuario->nombre_usuario} (ID: {$usuario->usuario_id})");

        // Verificar código OTP
        if (OtpCode::verificar($usuario->usuario_id, $codigo, 'email_verification')) {
            $this->info("✅ Código OTP verificado correctamente!");
            
            // Marcar email como verificado
            $usuario->update(['email_verified_at' => now()]);
            $this->info("✅ Email marcado como verificado: {$usuario->email_verified_at}");
            
            return 0;
        } else {
            $this->error("❌ Código OTP inválido o expirado");
            
            // Mostrar información del código OTP
            $otp = OtpCode::where('usuario_id', $usuario->usuario_id)
                ->where('codigo', $codigo)
                ->where('tipo', 'email_verification')
                ->first();
            
            if ($otp) {
                $this->info("📋 Información del código OTP:");
                $this->line("  - ID: {$otp->otp_id}");
                $this->line("  - Usado: " . ($otp->usado ? 'Sí' : 'No'));
                $this->line("  - Expiración: {$otp->fecha_expiracion}");
                $this->line("  - Expiró: " . ($otp->estaExpirado() ? 'Sí' : 'No'));
                $this->line("  - Tiempo restante: {$otp->tiempoRestante()} minutos");
            } else {
                $this->info("📋 No se encontró el código OTP en la base de datos");
            }
            
            return 1;
        }
    }
}
