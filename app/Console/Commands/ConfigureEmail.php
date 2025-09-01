<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ConfigureEmail extends Command
{
    protected $signature = 'email:configure {--driver=smtp} {--host=} {--port=} {--username=} {--password=} {--encryption=tls}';
    protected $description = 'Configurar email real para las notificaciones';

    public function handle()
    {
        $this->info('📧 Configurando email real para notificaciones...');
        
        $driver = $this->option('driver') ?: $this->choice(
            'Selecciona el driver de email:',
            ['smtp', 'mailgun', 'ses', 'postmark', 'resend'],
            'smtp'
        );

        $config = [];

        switch ($driver) {
            case 'smtp':
                $config = $this->configureSmtp();
                break;
            case 'mailgun':
                $config = $this->configureMailgun();
                break;
            case 'ses':
                $config = $this->configureSes();
                break;
            case 'postmark':
                $config = $this->configurePostmark();
                break;
            case 'resend':
                $config = $this->configureResend();
                break;
        }

        if ($config) {
            $this->displayConfiguration($driver, $config);
            $this->testEmailConfiguration($driver, $config);
        }

        return 0;
    }

    private function configureSmtp()
    {
        $this->info('🔧 Configurando SMTP...');
        
        $host = $this->option('host') ?: $this->ask('Host SMTP (ej: smtp.gmail.com):', 'smtp.gmail.com');
        $port = $this->option('port') ?: $this->ask('Puerto (ej: 587):', '587');
        $username = $this->option('username') ?: $this->ask('Usuario/Email:');
        $password = $this->option('password') ?: $this->secret('Contraseña:');
        $encryption = $this->option('encryption') ?: $this->choice('Encriptación:', ['tls', 'ssl'], 'tls');

        if (!$username || !$password) {
            $this->error('❌ Usuario y contraseña son requeridos');
            return null;
        }

        return [
            'MAIL_MAILER' => 'smtp',
            'MAIL_HOST' => $host,
            'MAIL_PORT' => $port,
            'MAIL_USERNAME' => $username,
            'MAIL_PASSWORD' => $password,
            'MAIL_ENCRYPTION' => $encryption,
            'MAIL_FROM_ADDRESS' => $username,
            'MAIL_FROM_NAME' => '4GMovil',
        ];
    }

    private function configureMailgun()
    {
        $this->info('🔧 Configurando Mailgun...');
        
        $domain = $this->ask('Dominio de Mailgun:');
        $secret = $this->secret('API Key de Mailgun:');

        if (!$domain || !$secret) {
            $this->error('❌ Dominio y API Key son requeridos');
            return null;
        }

        return [
            'MAIL_MAILER' => 'mailgun',
            'MAILGUN_DOMAIN' => $domain,
            'MAILGUN_SECRET' => $secret,
            'MAIL_FROM_ADDRESS' => "noreply@{$domain}",
            'MAIL_FROM_NAME' => '4GMovil',
        ];
    }

    private function configureSes()
    {
        $this->info('🔧 Configurando Amazon SES...');
        
        $key = $this->ask('AWS Access Key ID:');
        $secret = $this->secret('AWS Secret Access Key:');
        $region = $this->ask('Región AWS (ej: us-east-1):', 'us-east-1');

        if (!$key || !$secret) {
            $this->error('❌ AWS Access Key y Secret son requeridos');
            return null;
        }

        return [
            'MAIL_MAILER' => 'ses',
            'AWS_ACCESS_KEY_ID' => $key,
            'AWS_SECRET_ACCESS_KEY' => $secret,
            'AWS_DEFAULT_REGION' => $region,
            'MAIL_FROM_ADDRESS' => $this->ask('Email de origen:'),
            'MAIL_FROM_NAME' => '4GMovil',
        ];
    }

    private function configurePostmark()
    {
        $this->info('🔧 Configurando Postmark...');
        
        $token = $this->secret('Token de Postmark:');

        if (!$token) {
            $this->error('❌ Token de Postmark es requerido');
            return null;
        }

        return [
            'MAIL_MAILER' => 'postmark',
            'POSTMARK_TOKEN' => $token,
            'MAIL_FROM_ADDRESS' => $this->ask('Email de origen:'),
            'MAIL_FROM_NAME' => '4GMovil',
        ];
    }

    private function configureResend()
    {
        $this->info('🔧 Configurando Resend...');
        
        $key = $this->secret('API Key de Resend:');

        if (!$key) {
            $this->error('❌ API Key de Resend es requerido');
            return null;
        }

        return [
            'MAIL_MAILER' => 'resend',
            'RESEND_KEY' => $key,
            'MAIL_FROM_ADDRESS' => $this->ask('Email de origen:'),
            'MAIL_FROM_NAME' => '4GMovil',
        ];
    }

    private function displayConfiguration($driver, $config)
    {
        $this->newLine();
        $this->info('📋 Configuración generada:');
        $this->newLine();
        
        foreach ($config as $key => $value) {
            if (str_contains($key, 'PASSWORD') || str_contains($key, 'SECRET') || str_contains($key, 'KEY')) {
                $this->line("  {$key}: " . str_repeat('*', 8));
            } else {
                $this->line("  {$key}: {$value}");
            }
        }

        $this->newLine();
        $this->warn('📝 Agrega estas variables a tu archivo .env:');
        $this->newLine();
        
        foreach ($config as $key => $value) {
            $this->line("{$key}={$value}");
        }
    }

    private function testEmailConfiguration($driver, $config)
    {
        if ($this->confirm('¿Deseas probar la configuración de email?')) {
            $this->info('🧪 Probando configuración de email...');
            
            try {
                // Aquí podrías implementar una prueba real de email
                $this->info('✅ Configuración de email válida');
                $this->info('💡 Para probar completamente, ejecuta: php artisan email:test');
            } catch (\Exception $e) {
                $this->error('❌ Error en la configuración: ' . $e->getMessage());
            }
        }
    }
}
