<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Usuario;
use App\Helpers\PhotoHelper;

class TestGooglePassword extends Command
{
    protected $signature = 'test:google-password {email?}';
    protected $description = 'Prueba el sistema de contraseñas para usuarios de Google OAuth';

    public function handle()
    {
        $email = $this->argument('email');
        
        if (!$email) {
            $email = $this->ask('Ingresa el email del usuario a probar:');
        }

        $usuario = Usuario::where('correo_electronico', $email)->first();

        if (!$usuario) {
            $this->error("Usuario con email '{$email}' no encontrado.");
            return 1;
        }

        $this->info("=== INFORMACIÓN DEL USUARIO ===");
        $this->line("ID: {$usuario->usuario_id}");
        $this->line("Nombre: {$usuario->nombre_usuario}");
        $this->line("Email: {$usuario->correo_electronico}");
        $this->line("Google ID: " . ($usuario->google_id ?: 'No tiene'));
        $this->line("Tiene contraseña: " . ($usuario->canLoginManually() ? 'SÍ' : 'NO'));
        $this->line("Es usuario de Google: " . ($usuario->isGoogleUser() ? 'SÍ' : 'NO'));
        $this->line("Estado: " . ($usuario->estado ? 'Activo' : 'Inactivo'));
        $this->line("Rol: {$usuario->rol}");
        
        if ($usuario->foto_perfil) {
            $this->line("Foto de perfil: {$usuario->foto_perfil}");
            $this->line("Tipo de foto: " . PhotoHelper::getPhotoType($usuario->foto_perfil));
        }

        $this->newLine();
        
        if (!$usuario->canLoginManually()) {
            $this->warn("⚠️  Este usuario NO puede hacer login manual (no tiene contraseña)");
            $this->line("Para solucionarlo, debe establecer una contraseña desde su perfil.");
        } else {
            $this->info("✅ Este usuario SÍ puede hacer login manual");
        }

        return 0;
    }
}
