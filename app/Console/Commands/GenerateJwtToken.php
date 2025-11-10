<?php

namespace App\Console\Commands;

use App\Models\Usuario;
use App\Services\JwtService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class GenerateJwtToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jwt:generate 
                            {--email= : Email del usuario}
                            {--id= : ID del usuario}
                            {--export : Exportar token para variables de entorno}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Genera un token JWT para un usuario específico';

    /**
     * Execute the console command.
     */
    public function handle(JwtService $jwtService): int
    {
        $email = $this->option('email');
        $id = $this->option('id');
        $export = $this->option('export');

        // Buscar usuario por email o ID
        if ($email) {
            $usuario = Usuario::where('correo_electronico', $email)->first();
        } elseif ($id) {
            $usuario = Usuario::find($id);
        } else {
            $this->error('Debes proporcionar --email o --id');
            $this->info('Ejemplo: php artisan jwt:generate --email=admin@example.com');
            return Command::FAILURE;
        }

        if (!$usuario) {
            $this->error('Usuario no encontrado');
            return Command::FAILURE;
        }

        if (!$usuario->estado) {
            $this->error('El usuario está inactivo');
            return Command::FAILURE;
        }

        // Generar token
        try {
            $token = $jwtService->generateToken($usuario);
            $payload = $jwtService->validateToken($token);

            $this->info('Token JWT generado exitosamente');
            $this->newLine();
            $this->line('Usuario: ' . $usuario->nombre_usuario);
            $this->line('Email: ' . $usuario->correo_electronico);
            $this->line('Rol: ' . $usuario->rol);
            $this->newLine();
            $this->line('Token:');
            $this->line($token);
            $this->newLine();

            if ($payload) {
                $this->line('Información del Token:');
                $this->line('  - User ID: ' . ($payload['sub'] ?? 'N/A'));
                $this->line('  - Rol: ' . ($payload['rol'] ?? 'N/A'));
                $this->line('  - Email: ' . ($payload['email'] ?? 'N/A'));
                $this->line('  - Expira: ' . (isset($payload['exp']) ? date('Y-m-d H:i:s', $payload['exp']) : 'N/A'));
                $this->newLine();
            }

            // Exportar para variables de entorno
            if ($export) {
                $this->info('Formato para variables de entorno:');
                $this->newLine();
                $this->line('JWT_TOKEN=' . $token);
                $this->newLine();
                $this->info('Para Postman:');
                $this->line('Authorization: Bearer ' . $token);
                $this->newLine();
            }

            // Copiar al portapapeles si está disponible
            if ($this->confirm('¿Copiar token al portapapeles?', true)) {
                if (PHP_OS_FAMILY === 'Windows') {
                    exec('echo ' . escapeshellarg($token) . ' | clip');
                    $this->info('Token copiado al portapapeles');
                } elseif (PHP_OS_FAMILY === 'Darwin') {
                    exec('echo ' . escapeshellarg($token) . ' | pbcopy');
                    $this->info('Token copiado al portapapeles');
                } elseif (PHP_OS_FAMILY === 'Linux') {
                    exec('echo ' . escapeshellarg($token) . ' | xclip -selection clipboard');
                    $this->info('Token copiado al portapapeles');
                } else {
                    $this->warn('No se pudo copiar al portapapeles automáticamente');
                }
            }

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error al generar token: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}

