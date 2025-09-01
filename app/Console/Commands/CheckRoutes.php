<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;

class CheckRoutes extends Command
{
    protected $signature = 'routes:check';
    protected $description = 'Verifica que todas las rutas estén funcionando correctamente';

    public function handle()
    {
        $this->info('🔍 VERIFICANDO RUTAS...');
        $this->info('========================');

        // Verificar rutas de Google OAuth
        $this->info('📱 RUTAS DE GOOGLE OAUTH:');
        $googleRoutes = [
            'google.redirect' => '/auth/redirect/google',
            'google.callback' => '/auth/callback/google',
        ];

        foreach ($googleRoutes as $name => $uri) {
            $route = Route::getRoutes()->getByName($name);
            if ($route) {
                $this->line("  ✅ {$name}: {$uri}");
            } else {
                $this->error("  ❌ {$name}: NO ENCONTRADA");
            }
        }

        $this->newLine();

        // Verificar rutas de perfil
        $this->info('👤 RUTAS DE PERFIL:');
        $profileRoutes = [
            'perfil' => '/perfil',
            'perfil.actualizar' => '/perfil (PUT)',
            'perfil.eliminarFoto' => '/perfil/foto (DELETE)',
        ];

        foreach ($profileRoutes as $name => $uri) {
            $route = Route::getRoutes()->getByName($name);
            if ($route) {
                $this->line("  ✅ {$name}: {$uri}");
            } else {
                $this->error("  ❌ {$name}: NO ENCONTRADA");
            }
        }

        $this->newLine();

        // Verificar rutas de autenticación
        $this->info('🔐 RUTAS DE AUTENTICACIÓN:');
        $authRoutes = [
            'login' => '/login',
            'logout' => '/logout',
        ];

        foreach ($authRoutes as $name => $uri) {
            $route = Route::getRoutes()->getByName($name);
            if ($route) {
                $this->line("  ✅ {$name}: {$uri}");
            } else {
                $this->error("  ❌ {$name}: NO ENCONTRADA");
            }
        }

        $this->newLine();

        // Verificar middleware de rutas
        $this->info('🛡️  MIDDLEWARE DE RUTAS:');
        $this->line("  Perfil: " . (Route::getRoutes()->getByName('perfil') ? '✅ Existe' : '❌ No existe'));
        
        if (Route::getRoutes()->getByName('perfil')) {
            $route = Route::getRoutes()->getByName('perfil');
            $middlewares = $route->middleware();
            $this->line("  Middlewares: " . implode(', ', $middlewares));
        }

        $this->newLine();
        $this->info('🎯 RECOMENDACIONES:');
        $this->line("  Si hay rutas faltantes, ejecuta: php artisan route:clear");
        $this->line("  Para ver todas las rutas: php artisan route:list");
        
        return 0;
    }
}
