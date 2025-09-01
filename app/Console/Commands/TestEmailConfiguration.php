<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class TestEmailConfiguration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Probar la configuración de email enviando un correo de prueba';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info("🔧 Probando configuración de email...");
        $this->info("📧 Enviando correo de prueba a: {$email}");
        
        // Mostrar configuración actual
        $this->info("\n📋 Configuración actual:");
        $this->info("MAIL_MAILER: " . config('mail.default'));
        $this->info("MAIL_HOST: " . config('mail.mailers.smtp.host'));
        $this->info("MAIL_PORT: " . config('mail.mailers.smtp.port'));
        $this->info("MAIL_ENCRYPTION: " . config('mail.mailers.smtp.encryption'));
        $this->info("MAIL_USERNAME: " . config('mail.mailers.smtp.username'));
        $this->info("MAIL_PASSWORD: " . (config('mail.mailers.smtp.password') ? '***CONFIGURADO***' : 'NO CONFIGURADO'));
        
        try {
            // Enviar correo de prueba
            Mail::raw('Este es un correo de prueba desde 4GMovil. Si recibes esto, la configuración de email está funcionando correctamente.', function ($message) use ($email) {
                $message->to($email)
                        ->subject('Prueba de Email - 4GMovil')
                        ->from(config('mail.from.address'), config('mail.from.name'));
            });
            
            $this->info("\n✅ Correo enviado exitosamente!");
            $this->info("📬 Revisa tu bandeja de entrada (y carpeta de spam)");
            
        } catch (\Exception $e) {
            $this->error("\n❌ Error al enviar correo:");
            $this->error($e->getMessage());
            
            // Log del error para debugging
            Log::error('Error en prueba de email: ' . $e->getMessage());
            
            $this->info("\n🔍 Posibles soluciones:");
            $this->info("1. Verifica las credenciales SMTP en tu archivo .env");
            $this->info("2. Asegúrate de que el puerto y encriptación sean correctos");
            $this->info("3. Verifica que tu proveedor de email permita conexiones SMTP");
            $this->info("4. Revisa los logs en storage/logs/laravel.log");
        }
        
        return 0;
    }
}
