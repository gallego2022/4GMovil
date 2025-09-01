<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class CheckEmailSetup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:email-setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verificar toda la configuración de email para restablecimiento de contraseña';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("🔍 Verificando configuración de email para 4GMovil...\n");
        
        // 1. Verificar configuración de mail
        $this->checkMailConfiguration();
        
        // 2. Verificar configuración de auth
        $this->checkAuthConfiguration();
        
        // 3. Verificar modelo Usuario
        $this->checkUsuarioModel();
        
        // 4. Verificar notificaciones
        $this->checkNotifications();
        
        // 5. Verificar base de datos
        $this->checkDatabase();
        
        $this->info("\n✅ Verificación completada!");
        $this->info("📧 Si hay problemas, revisa el archivo EMAIL_CONFIGURATION_EXAMPLE.md");
    }
    
    private function checkMailConfiguration()
    {
        $this->info("📧 1. Configuración de Mail:");
        
        $mailConfig = [
            'MAIL_MAILER' => config('mail.default'),
            'MAIL_HOST' => config('mail.mailers.smtp.host'),
            'MAIL_PORT' => config('mail.mailers.smtp.port'),
            'MAIL_ENCRYPTION' => config('mail.mailers.smtp.encryption'),
            'MAIL_USERNAME' => config('mail.mailers.smtp.username'),
            'MAIL_PASSWORD' => config('mail.mailers.smtp.password') ? '***CONFIGURADO***' : 'NO CONFIGURADO',
            'MAIL_FROM_ADDRESS' => config('mail.from.address'),
            'MAIL_FROM_NAME' => config('mail.from.name'),
        ];
        
        foreach ($mailConfig as $key => $value) {
            $status = $value ? '✅' : '❌';
            $this->line("   {$status} {$key}: {$value}");
        }
        
        // Verificar si la configuración está completa
        $missing = array_filter($mailConfig, function($value) {
            return !$value || $value === 'NO CONFIGURADO';
        });
        
        if (count($missing) > 0) {
            $this->warn("   ⚠️  Faltan configuraciones de email en .env");
        } else {
            $this->info("   ✅ Configuración de mail completa");
        }
    }
    
    private function checkAuthConfiguration()
    {
        $this->info("\n🔐 2. Configuración de Auth:");
        
        $authConfig = [
            'AUTH_GUARD' => config('auth.defaults.guard'),
            'AUTH_PROVIDER' => config('auth.defaults.passwords'),
            'PASSWORD_RESET_TABLE' => config('auth.passwords.users.table'),
            'PASSWORD_RESET_EXPIRE' => config('auth.passwords.users.expire') . ' minutos',
            'PASSWORD_RESET_THROTTLE' => config('auth.passwords.users.throttle') . ' segundos',
        ];
        
        foreach ($authConfig as $key => $value) {
            $this->line("   ✅ {$key}: {$value}");
        }
    }
    
    private function checkUsuarioModel()
    {
        $this->info("\n👤 3. Modelo Usuario:");
        
        try {
            $usuario = new \App\Models\Usuario();
            $reflection = new \ReflectionClass($usuario);
            
            $interfaces = $reflection->getInterfaceNames();
            $traits = $reflection->getTraitNames();
            
            $this->line("   ✅ Clase: " . get_class($usuario));
            $this->line("   ✅ Tabla: " . $usuario->getTable());
            $this->line("   ✅ Primary Key: " . $usuario->getKeyName());
            
            // Verificar interfaces
            if (in_array('Illuminate\Contracts\Auth\CanResetPassword', $interfaces)) {
                $this->line("   ✅ Implementa CanResetPassword");
            } else {
                $this->warn("   ❌ NO implementa CanResetPassword");
            }
            
            if (in_array('Illuminate\Notifications\Notifiable', $traits)) {
                $this->line("   ✅ Usa trait Notifiable");
            } else {
                $this->warn("   ❌ NO usa trait Notifiable");
            }
            
            // Verificar métodos
            if (method_exists($usuario, 'getEmailForPasswordReset')) {
                $this->line("   ✅ Método getEmailForPasswordReset existe");
            } else {
                $this->warn("   ❌ Método getEmailForPasswordReset NO existe");
            }
            
            if (method_exists($usuario, 'sendPasswordResetNotification')) {
                $this->line("   ✅ Método sendPasswordResetNotification existe");
            } else {
                $this->warn("   ❌ Método sendPasswordResetNotification NO existe");
            }
            
        } catch (\Exception $e) {
            $this->error("   ❌ Error al verificar modelo: " . $e->getMessage());
        }
    }
    
    private function checkNotifications()
    {
        $this->info("\n🔔 4. Notificaciones:");
        
        $notifications = [
            'RecuperarContrasena' => \App\Notifications\RecuperarContrasena::class,
        ];
        
        foreach ($notifications as $name => $class) {
            if (class_exists($class)) {
                $this->line("   ✅ {$name}: Existe");
            } else {
                $this->warn("   ❌ {$name}: NO existe");
            }
        }
    }
    
    private function checkDatabase()
    {
        $this->info("\n🗄️  5. Base de Datos:");
        
        try {
            // Verificar conexión
            DB::connection()->getPdo();
            $this->line("   ✅ Conexión a BD: OK");
            
            // Verificar tabla password_reset_tokens
            if (DB::getSchemaBuilder()->hasTable('password_reset_tokens')) {
                $this->line("   ✅ Tabla password_reset_tokens: Existe");
                
                // Verificar estructura
                $columns = DB::getSchemaBuilder()->getColumnListing('password_reset_tokens');
                $requiredColumns = ['email', 'token', 'created_at'];
                
                foreach ($requiredColumns as $column) {
                    if (in_array($column, $columns)) {
                        $this->line("      ✅ Columna {$column}: Existe");
                    } else {
                        $this->warn("      ❌ Columna {$column}: NO existe");
                    }
                }
            } else {
                $this->warn("   ❌ Tabla password_reset_tokens: NO existe");
            }
            
            // Verificar tabla usuarios
            if (DB::getSchemaBuilder()->hasTable('usuarios')) {
                $this->line("   ✅ Tabla usuarios: Existe");
                
                // Verificar columnas importantes
                $columns = DB::getSchemaBuilder()->getColumnListing('usuarios');
                $requiredColumns = ['correo_electronico', 'contrasena'];
                
                foreach ($requiredColumns as $column) {
                    if (in_array($column, $columns)) {
                        $this->line("      ✅ Columna {$column}: Existe");
                    } else {
                        $this->warn("      ❌ Columna {$column}: NO existe");
                    }
                }
            } else {
                $this->warn("   ❌ Tabla usuarios: NO existe");
            }
            
        } catch (\Exception $e) {
            $this->error("   ❌ Error de BD: " . $e->getMessage());
        }
    }
}
