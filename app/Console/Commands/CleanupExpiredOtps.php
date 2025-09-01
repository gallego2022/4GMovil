<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\OtpCode;
use Illuminate\Support\Facades\Log;

class CleanupExpiredOtps extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'otp:cleanup {--dry-run : Mostrar qué se eliminaría sin ejecutar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Limpiar códigos OTP expirados de la base de datos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧹 Iniciando limpieza de códigos OTP expirados...');

        // Contar códigos expirados
        $expiredCount = OtpCode::where('expires_at', '<', now())->count();
        
        if ($expiredCount === 0) {
            $this->info('✅ No hay códigos OTP expirados para limpiar.');
            return 0;
        }

        $this->info("📊 Se encontraron {$expiredCount} códigos OTP expirados.");

        if ($this->option('dry-run')) {
            $this->warn('🔍 Modo dry-run: No se eliminarán registros.');
            $this->info('Los siguientes códigos serían eliminados:');
            
            $expiredOtps = OtpCode::where('expires_at', '<', now())
                ->with('usuario')
                ->get();
            
            foreach ($expiredOtps as $otp) {
                $this->line("  - ID: {$otp->id} | Usuario: {$otp->usuario->correo_electronico} | Tipo: {$otp->tipo} | Expiró: {$otp->expires_at}");
            }
            
            return 0;
        }

        // Confirmar eliminación
        if (!$this->confirm("¿Estás seguro de que quieres eliminar {$expiredCount} códigos OTP expirados?")) {
            $this->info('❌ Operación cancelada.');
            return 0;
        }

        try {
            // Eliminar códigos expirados
            $deletedCount = OtpCode::limpiarExpirados();
            
            $this->info("✅ Se eliminaron {$deletedCount} códigos OTP expirados exitosamente.");
            
            Log::info("Comando otp:cleanup ejecutado - {$deletedCount} códigos OTP expirados eliminados");
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error("❌ Error al limpiar códigos OTP: " . $e->getMessage());
            Log::error("Error en comando otp:cleanup: " . $e->getMessage());
            
            return 1;
        }
    }
}
