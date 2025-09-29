<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class TestGooglePasswordModal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:google-password-modal {--create-user : Crear un usuario de Google de prueba}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Probar el modal de establecer contraseña para usuarios de Google';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Probando modal de establecer contraseña de Google...');

        if ($this->option('create-user')) {
            $this->createTestUser();
        }

        $this->testGoogleUsers();
        $this->testPasswordValidation();
    }

    private function createTestUser()
    {
        $this->info('👤 Creando usuario de Google de prueba...');

        $usuario = Usuario::create([
            'nombre_usuario' => 'Usuario Google Test',
            'correo_electronico' => 'test-google@example.com',
            'contrasena' => null, // Sin contraseña = usuario de Google
            'telefono' => '3001234567',
            'rol' => 'cliente',
            'estado' => true,
            'foto_perfil' => 'https://lh3.googleusercontent.com/a/test-avatar.jpg'
        ]);

        $this->info("✅ Usuario creado: {$usuario->correo_electronico}");
        $this->info("   ID: {$usuario->usuario_id}");
        $this->info("   Contraseña: " . ($usuario->contrasena ? 'Tiene' : 'No tiene (Google)'));
    }

    private function testGoogleUsers()
    {
        $this->info('🔍 Verificando usuarios de Google...');

        $googleUsers = Usuario::whereNull('contrasena')->get();

        if ($googleUsers->isEmpty()) {
            $this->warn('⚠️ No se encontraron usuarios de Google');
            return;
        }

        $this->info("📊 Se encontraron {$googleUsers->count()} usuarios de Google:");

        foreach ($googleUsers as $usuario) {
            $this->line("   • {$usuario->nombre_usuario} ({$usuario->correo_electronico}) - Rol: {$usuario->rol}");
        }
    }

    private function testPasswordValidation()
    {
        $this->info('🔐 Probando validación de contraseñas...');

        $testPasswords = [
            '123' => 'Muy corta',
            'password' => 'Sin mayúscula ni número',
            'PASSWORD' => 'Sin minúscula ni número',
            'Password' => 'Sin número ni símbolo',
            'Password123' => 'Sin símbolo',
            'Password123!' => 'Válida',
            'MyPass123!' => 'Válida',
            'Test@123' => 'Válida'
        ];

        foreach ($testPasswords as $password => $description) {
            $isValid = $this->validatePassword($password);
            $status = $isValid ? '✅' : '❌';
            $this->line("   {$status} {$password} - {$description}");
        }
    }

    private function validatePassword($password)
    {
        if (strlen($password) < 8) return false;
        if (!preg_match('/[A-Z]/', $password)) return false;
        if (!preg_match('/[a-z]/', $password)) return false;
        if (!preg_match('/[0-9]/', $password)) return false;
        if (!preg_match('/[!@#$%^&*]/', $password)) return false;
        
        return true;
    }
}
