<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class InstallRedisGuide extends Command
{
    protected $signature = 'redis:install-guide';
    protected $description = 'Guía para instalar Redis en diferentes sistemas operativos';

    public function handle()
    {
        $this->info('🔧 Guía de Instalación de Redis para 4GMovil');
        $this->newLine();

        $this->info('📋 Pasos para instalar Redis:');
        $this->newLine();

        $this->info('🪟 Windows:');
        $this->line('1. Descargar Redis desde: https://github.com/microsoftarchive/redis/releases');
        $this->line('2. Extraer y ejecutar redis-server.exe');
        $this->line('3. Instalar extensión PHP Redis:');
        $this->line('   - Descargar desde: https://pecl.php.net/package/redis');
        $this->line('   - O usar: composer require predis/predis');
        $this->newLine();

        $this->info('🐧 Linux (Ubuntu/Debian):');
        $this->line('sudo apt update');
        $this->line('sudo apt install redis-server');
        $this->line('sudo apt install php-redis');
        $this->line('sudo systemctl start redis-server');
        $this->line('sudo systemctl enable redis-server');
        $this->newLine();

        $this->info('🍎 macOS:');
        $this->line('brew install redis');
        $this->line('brew services start redis');
        $this->line('pecl install redis');
        $this->newLine();

        $this->info('🐳 Docker (Alternativa):');
        $this->line('docker run -d --name redis -p 6379:6379 redis:alpine');
        $this->newLine();

        $this->info('⚙️ Configuración en .env:');
        $this->line('CACHE_DRIVER=redis');
        $this->line('REDIS_HOST=127.0.0.1');
        $this->line('REDIS_PASSWORD=null');
        $this->line('REDIS_PORT=6379');
        $this->newLine();

        $this->info('🧪 Verificar instalación:');
        $this->line('php artisan test:redis-cache-performance');
        $this->newLine();

        $this->info('💡 Alternativa sin Redis:');
        $this->line('El sistema funcionará con caché de archivos si Redis no está disponible');
        $this->line('Para habilitar: CACHE_DRIVER=file en .env');

        return Command::SUCCESS;
    }
}
