<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SyncStorageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:sync {--force : Forzar sincronización incluso si los archivos existen}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza el contenido de storage/app/public al directorio público';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Sincronizando storage...');

        $storagePath = storage_path('app/public');
        $publicPath = public_path('storage');

        // Crear directorio público de storage si no existe
        if (!File::exists($publicPath)) {
            File::makeDirectory($publicPath, 0755, true);
            $this->info("📁 Directorio público creado: {$publicPath}");
        }

        // Verificar que el directorio de storage existe
        if (!File::exists($storagePath)) {
            $this->error("❌ El directorio de storage no existe: {$storagePath}");
            return 1;
        }

        // Obtener todas las carpetas y archivos en storage/app/public
        $items = File::allFiles($storagePath);
        $directories = File::directories($storagePath);

        $syncedFiles = 0;
        $syncedDirs = 0;

        // Sincronizar archivos
        foreach ($items as $item) {
            $relativePath = str_replace($storagePath . DIRECTORY_SEPARATOR, '', $item->getPathname());
            $destination = $publicPath . DIRECTORY_SEPARATOR . $relativePath;
            $destinationDir = dirname($destination);

            // Crear directorio de destino si no existe
            if (!File::exists($destinationDir)) {
                File::makeDirectory($destinationDir, 0755, true);
            }

            // Copiar archivo si no existe o si se fuerza la sincronización
            if (!File::exists($destination) || $this->option('force')) {
                File::copy($item->getPathname(), $destination);
                $syncedFiles++;
            }
        }

        // Sincronizar directorios
        foreach ($directories as $dir) {
            $dirName = basename($dir);
            $destinationDir = $publicPath . DIRECTORY_SEPARATOR . $dirName;

            if (!File::exists($destinationDir)) {
                File::makeDirectory($destinationDir, 0755, true);
                $syncedDirs++;
            }

            // Sincronizar contenido del directorio
            $this->syncDirectory($dir, $destinationDir);
        }

        // Establecer permisos correctos
        $this->setPermissions($publicPath);

        $this->info("✅ Sincronización completada:");
        $this->info("   📄 Archivos sincronizados: {$syncedFiles}");
        $this->info("   📁 Directorios creados: {$syncedDirs}");

        return 0;
    }

    /**
     * Sincronizar contenido de un directorio
     */
    private function syncDirectory($source, $destination)
    {
        if (!File::exists($source)) {
            return;
        }

        $items = File::allFiles($source);
        
        foreach ($items as $item) {
            $relativePath = str_replace($source . DIRECTORY_SEPARATOR, '', $item->getPathname());
            $destFile = $destination . DIRECTORY_SEPARATOR . $relativePath;
            $destDir = dirname($destFile);

            if (!File::exists($destDir)) {
                File::makeDirectory($destDir, 0755, true);
            }

            if (!File::exists($destFile) || $this->option('force')) {
                File::copy($item->getPathname(), $destFile);
            }
        }
    }

    /**
     * Establecer permisos correctos
     */
    private function setPermissions($path)
    {
        try {
            // En sistemas Unix/Linux
            if (PHP_OS_FAMILY !== 'Windows') {
                exec("chown -R www-data:www-data {$path} 2>/dev/null || true");
                exec("chmod -R 775 {$path} 2>/dev/null || true");
            }
        } catch (\Exception $e) {
            $this->warn("⚠️ No se pudieron establecer permisos: " . $e->getMessage());
        }
    }
}
