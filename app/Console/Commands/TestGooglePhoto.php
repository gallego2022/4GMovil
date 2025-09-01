<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Usuario;
use App\Helpers\PhotoHelper;

class TestGooglePhoto extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:google-photo {email?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prueba la funcionalidad de fotos de Google OAuth';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        if ($email) {
            $usuario = Usuario::where('correo_electronico', $email)->first();
            if (!$usuario) {
                $this->error("Usuario con email {$email} no encontrado.");
                return 1;
            }
        } else {
            $usuario = Usuario::whereNotNull('google_id')->first();
            if (!$usuario) {
                $this->error("No se encontraron usuarios con Google OAuth.");
                return 1;
            }
        }

        $this->info("Usuario: {$usuario->nombre_usuario}");
        $this->info("Email: {$usuario->correo_electronico}");
        $this->info("Google ID: {$usuario->google_id}");
        $this->info("Foto de perfil: {$usuario->foto_perfil}");
        
        if ($usuario->foto_perfil) {
            $this->info("Tipo de foto: " . PhotoHelper::getPhotoType($usuario->foto_perfil));
            $this->info("Es URL externa: " . (PhotoHelper::isExternalUrl($usuario->foto_perfil) ? 'Sí' : 'No'));
            $this->info("Es foto de Google: " . (PhotoHelper::isGooglePhotoUrl($usuario->foto_perfil) ? 'Sí' : 'No'));
            $this->info("URL para mostrar: " . PhotoHelper::getPhotoUrl($usuario->foto_perfil));
            $this->info("Puede ser eliminada: " . (PhotoHelper::canDeletePhoto($usuario->foto_perfil) ? 'Sí' : 'No'));
        } else {
            $this->warn("El usuario no tiene foto de perfil.");
        }

        return 0;
    }
}
