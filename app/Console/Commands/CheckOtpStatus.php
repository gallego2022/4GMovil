<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\OtpCode;
use App\Models\Usuario;

class CheckOtpStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'otp:status {--user-id= : ID específico del usuario}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verificar el estado de los códigos OTP en la base de datos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Verificando estado de códigos OTP...');

        $userId = $this->option('user-id');

        if ($userId) {
            // Verificar usuario específico
            $usuario = Usuario::find($userId);
            if (!$usuario) {
                $this->error("❌ Usuario con ID {$userId} no encontrado.");
                return 1;
            }

            $this->info("👤 Verificando códigos OTP para: {$usuario->correo_electronico}");
            $this->checkUserOtps($usuario);
        } else {
            // Verificar todos los usuarios
            $usuarios = Usuario::all();
            
            foreach ($usuarios as $usuario) {
                $this->info("\n👤 Usuario: {$usuario->correo_electronico} (ID: {$usuario->usuario_id})");
                $this->checkUserOtps($usuario);
            }
        }

        return 0;
    }

    private function checkUserOtps(Usuario $usuario)
    {
        // Verificar estado de verificación
        $verificationStatus = $usuario->email_verified_at ? '✅ Verificado' : '❌ No verificado';
        $this->line("  📧 Estado: {$verificationStatus}");
        
        if ($usuario->email_verified_at) {
            $this->line("  📅 Verificado el: {$usuario->email_verified_at->format('Y-m-d H:i:s')}");
        }

        // Buscar códigos OTP del usuario
        $otpCodes = OtpCode::where('usuario_id', $usuario->usuario_id)->get();
        
        if ($otpCodes->count() === 0) {
            $this->line("  🔑 Códigos OTP: Ninguno encontrado");
            return;
        }

        $this->line("  🔑 Códigos OTP encontrados: {$otpCodes->count()}");

        foreach ($otpCodes as $otp) {
            $status = $otp->usado ? '❌ Usado' : '✅ Válido';
            $expired = $otp->estaExpirado() ? '⏰ Expirado' : '⏰ Válido';
            $timeLeft = $otp->tiempoRestante();
            
            $this->line("    - ID: {$otp->otp_id} | Código: {$otp->codigo} | Tipo: {$otp->tipo}");
            $this->line("      Estado: {$status} | Expiración: {$expired} | Tiempo restante: {$timeLeft} min");
            $this->line("      Creado: {$otp->created_at->format('Y-m-d H:i:s')}");
        }

        // Verificar si tiene códigos válidos para verificación
        $validVerificationOtps = OtpCode::where('usuario_id', $usuario->usuario_id)
            ->where('tipo', 'email_verification')
            ->where('usado', false)
            ->where('fecha_expiracion', '>', now())
            ->count();

        if ($validVerificationOtps > 0) {
            $this->line("  🎯 Tiene {$validVerificationOtps} código(s) OTP válido(s) para verificación");
        } else {
            $this->line("  ⚠️  No tiene códigos OTP válidos para verificación");
        }
    }
}
