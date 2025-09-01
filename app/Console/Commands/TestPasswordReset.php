<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Usuario;
use Illuminate\Support\Facades\Password;

class TestPasswordReset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:password-reset {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Probar el sistema de restablecimiento de contraseña';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info("Probando restablecimiento de contraseña para: {$email}");
        
        // Verificar si el usuario existe
        $usuario = Usuario::where('correo_electronico', $email)->first();
        
        if (!$usuario) {
            $this->error("Usuario no encontrado con el email: {$email}");
            return 1;
        }
        
        $this->info("Usuario encontrado: {$usuario->nombre_usuario}");
        
        // Simular el envío del enlace de restablecimiento
        try {
            $status = Password::sendResetLink(['correo_electronico' => $email]);
            
            if ($status === Password::RESET_LINK_SENT) {
                $this->info("✅ Enlace de restablecimiento enviado exitosamente");
                $this->info("Mensaje: " . __($status));
            } else {
                $this->error("❌ Error al enviar enlace de restablecimiento");
                $this->error("Estado: " . __($status));
            }
        } catch (\Exception $e) {
            $this->error("❌ Excepción: " . $e->getMessage());
        }
        
        return 0;
    }
}
