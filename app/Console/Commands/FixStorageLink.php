<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class FixStorageLink extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:fix-link';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica y corrige el enlace simbólico de storage si es necesario';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔗 Verificando enlace simbólico de storage...');

        $storagePath = public_path('storage');
        $targetPath = storage_path('app/public');

        // Verificar si el directorio target existe
        if (!File::exists($targetPath)) {
            $this->error("❌ El directorio target no existe: {$targetPath}");
            return 1;
        }

        // Verificar si el enlace existe
        if (!File::exists($storagePath)) {
            $this->warn("⚠️ El enlace simbólico no existe, creándolo...");
            $this->createLink($storagePath, $targetPath);
            return 0;
        }

        // Verificar si es un enlace simbólico
        if (!is_link($storagePath)) {
            $this->warn("⚠️ El directorio storage no es un enlace simbólico, recreándolo...");
            File::deleteDirectory($storagePath);
            $this->createLink($storagePath, $targetPath);
            return 0;
        }

        // Verificar si apunta al lugar correcto
        $currentTarget = readlink($storagePath);
        if ($currentTarget !== $targetPath) {
            $this->warn("⚠️ El enlace simbólico apunta a un lugar incorrecto: {$currentTarget}");
            $this->warn("   Debería apuntar a: {$targetPath}");
            File::delete($storagePath);
            $this->createLink($storagePath, $targetPath);
            return 0;
        }

        $this->info("✅ El enlace simbólico está correctamente configurado");
        $this->info("   Enlace: {$storagePath}");
        $this->info("   Target: {$targetPath}");

        return 0;
    }

    private function createLink($linkPath, $targetPath)
    {
        try {
            if (symlink($targetPath, $linkPath)) {
                $this->info("✅ Enlace simbólico creado exitosamente");
                $this->info("   Enlace: {$linkPath}");
                $this->info("   Target: {$targetPath}");
            } else {
                $this->error("❌ No se pudo crear el enlace simbólico");
                return 1;
            }
        } catch (\Exception $e) {
            $this->error("❌ Error al crear el enlace simbólico: " . $e->getMessage());
            return 1;
        }
    }
}
