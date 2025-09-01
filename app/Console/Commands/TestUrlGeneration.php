<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class TestUrlGeneration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:url-generation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Probar la generación de URLs para verificar que sean absolutas';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("🔗 Probando generación de URLs...\n");
        
        // Generar token de prueba
        $token = Str::random(60);
        $email = 'test@example.com';
        
        $this->info("🔑 Token generado: " . substr($token, 0, 20) . "...");
        $this->info("📧 Email: {$email}");
        
        // Probar URL relativa (false)
        $urlRelativa = route('password.reset', [
            'token' => $token,
            'email' => $email,
        ], false);
        
        // Probar URL absoluta (true)
        $urlAbsoluta = route('password.reset', [
            'token' => $token,
            'email' => $email,
        ], true);
        
        $this->info("\n📋 Resultados:");
        $this->info("   🔗 URL Relativa (false): {$urlRelativa}");
        $this->info("   🔗 URL Absoluta (true):  {$urlAbsoluta}");
        
        // Verificar si la URL absoluta contiene el dominio
        if (str_starts_with($urlAbsoluta, 'http')) {
            $this->info("\n✅ URL Absoluta generada correctamente");
            $this->info("   🌐 Dominio detectado: " . parse_url($urlAbsoluta, PHP_URL_HOST));
        } else {
            $this->warn("\n⚠️ URL Absoluta no contiene protocolo HTTP");
        }
        
        // Verificar configuración de APP_URL
        $appUrl = config('app.url');
        $this->info("\n⚙️ Configuración actual:");
        $this->info("   📍 APP_URL: {$appUrl}");
        
        if (empty($appUrl)) {
            $this->warn("   ⚠️ APP_URL está vacío en .env");
            $this->info("   💡 Agrega APP_URL=http://127.0.0.1:8000 en tu archivo .env");
        }
        
        return 0;
    }
}
