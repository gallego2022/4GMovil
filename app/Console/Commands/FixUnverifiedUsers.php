<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Usuario;
use App\Models\OtpCode;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpVerification;

class FixUnverifiedUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:fix-unverified {--send-otp : Enviar códigos OTP a usuarios no verificados} {--verify-all : Marcar todos los usuarios como verificados (solo para desarrollo)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Solucionar usuarios que se registraron antes de corregir el error de OTP';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 Iniciando reparación de usuarios no verificados...');

        // Contar usuarios no verificados
        $unverifiedUsers = Usuario::whereNull('email_verified_at')->get();
        $count = $unverifiedUsers->count();

        if ($count === 0) {
            $this->info('✅ No hay usuarios no verificados que reparar.');
            return 0;
        }

        $this->info("📊 Se encontraron {$count} usuarios no verificados.");

        // Mostrar usuarios no verificados
        $this->table(
            ['ID', 'Nombre', 'Email', 'Fecha Registro', 'Estado'],
            $unverifiedUsers->map(function ($user) {
                return [
                    $user->usuario_id,
                    $user->nombre_usuario,
                    $user->correo_electronico,
                    $user->fecha_registro->format('Y-m-d H:i:s'),
                    $user->estado ? 'Activo' : 'Inactivo'
                ];
            })
        );

        // Opción para marcar todos como verificados (solo desarrollo)
        if ($this->option('verify-all')) {
            if (!$this->confirm('⚠️  ¿Estás seguro de que quieres marcar TODOS los usuarios como verificados? (Solo para desarrollo)')) {
                $this->info('❌ Operación cancelada.');
                return 0;
            }

            $this->info('🔓 Marcando todos los usuarios como verificados...');
            
            foreach ($unverifiedUsers as $user) {
                $user->update(['email_verified_at' => now()]);
                $this->line("  ✅ Usuario {$user->correo_electronico} marcado como verificado");
            }

            $this->info("✅ Se marcaron {$count} usuarios como verificados.");
            return 0;
        }

        // Opción para enviar códigos OTP
        if ($this->option('send-otp')) {
            $this->info('📧 Enviando códigos OTP a usuarios no verificados...');
            
            $successCount = 0;
            $errorCount = 0;

            foreach ($unverifiedUsers as $user) {
                try {
                    // Verificar si ya tiene un código OTP válido
                    if (!OtpCode::tieneCodigoValido($user->usuario_id, 'email_verification')) {
                        // Crear nuevo código OTP
                        $otp = OtpCode::crear($user->usuario_id, 'email_verification', 10);
                        
                        // Enviar correo con OTP
                        Mail::to($user->correo_electronico)->send(new OtpVerification($user, $otp->codigo, 'email_verification', 10));
                        
                        $this->line("  ✅ Código OTP enviado a: {$user->correo_electronico}");
                        $successCount++;
                    } else {
                        $this->line("  ⚠️  Usuario {$user->correo_electronico} ya tiene un código OTP válido");
                    }
                } catch (\Exception $e) {
                    $this->error("  ❌ Error enviando OTP a {$user->correo_electronico}: {$e->getMessage()}");
                    $errorCount++;
                    Log::error("Error enviando OTP a usuario {$user->usuario_id}: {$e->getMessage()}");
                }
            }

            $this->info("\n📊 Resumen del envío:");
            $this->info("  ✅ Exitosos: {$successCount}");
            $this->info("  ❌ Errores: {$errorCount}");
            
            if ($successCount > 0) {
                $this->info("\n💡 Los usuarios ahora pueden:");
                $this->info("  1. Ir a /otp/verify/register");
                $this->info("  2. Ingresar su email");
                $this->info("  3. Solicitar nuevo código OTP");
                $this->info("  4. Verificar su cuenta");
            }
        } else {
            $this->info("\n💡 Opciones disponibles:");
            $this->info("  --send-otp     : Enviar códigos OTP a usuarios no verificados");
            $this->info("  --verify-all   : Marcar todos como verificados (solo desarrollo)");
            $this->info("\n📝 Ejemplos:");
            $this->info("  php artisan users:fix-unverified --send-otp");
            $this->info("  php artisan users:fix-unverified --verify-all");
        }

        return 0;
    }
}
