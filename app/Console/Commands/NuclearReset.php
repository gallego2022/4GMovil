<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;

class NuclearReset extends Command
{
    protected $signature = 'nuclear:reset {--force : Forzar reset sin preguntar}';
    protected $description = 'RESET NUCLEAR: Elimina todo y recrea desde cero (SOLO PARA DESARROLLO)';

    public function handle()
    {
        $this->error('⚠️  ⚠️  ⚠️  RESET NUCLEAR ⚠️  ⚠️  ⚠️');
        $this->error('ESTE COMANDO ELIMINARÁ TODOS LOS DATOS');
        $this->error('SOLO USAR EN DESARROLLO');
        $this->error('⚠️  ⚠️  ⚠️  ⚠️  ⚠️  ⚠️  ⚠️  ⚠️  ⚠️  ⚠️');
        $this->newLine();

        if (!$this->option('force')) {
            if (!$this->confirm('¿Estás 100% seguro de que quieres hacer un RESET NUCLEAR? Esto eliminará TODOS los datos.')) {
                $this->info('Reset nuclear cancelado.');
                return 0;
            }
            
            if (!$this->confirm('¿Estás en un entorno de DESARROLLO? No ejecutes esto en producción.')) {
                $this->info('Reset nuclear cancelado por seguridad.');
                return 0;
            }
        }

        $this->info('🚀 INICIANDO RESET NUCLEAR...');
        $this->newLine();

        try {
            // 1. Eliminar todas las tablas
            $this->info('🗑️  Eliminando todas las tablas...');
            Schema::dropAllTables();
            $this->info('  ✅ Todas las tablas eliminadas');

            // 2. Limpiar caché
            $this->info('🧹 Limpiando caché...');
            $this->call('config:clear');
            $this->call('route:clear');
            $this->call('view:clear');
            $this->call('cache:clear');
            $this->info('  ✅ Caché limpiado');

            // 3. Ejecutar migraciones frescas
            $this->info('📦 Ejecutando migraciones frescas...');
            $this->call('migrate');
            $this->info('  ✅ Migraciones ejecutadas');

            // 4. Ejecutar seeders
            $this->info('🌱 Ejecutando seeders...');
            $this->call('db:seed');
            $this->info('  ✅ Seeders ejecutados');

            // 5. Limpiar logs
            $this->info('📝 Limpiando logs...');
            $logFile = storage_path('logs/laravel.log');
            if (file_exists($logFile)) {
                file_put_contents($logFile, '');
                $this->info('  ✅ Logs limpiados');
            }

            $this->newLine();
            $this->info('🎉 RESET NUCLEAR COMPLETADO EXITOSAMENTE!');
            $this->newLine();
            $this->info('📋 ESTADO ACTUAL:');
            $this->info('   ✅ Base de datos completamente limpia');
            $this->info('   ✅ Estructura recreada desde cero');
            $this->info('   ✅ Datos por defecto creados');
            $this->info('   ✅ Caché limpiado');
            $this->info('   ✅ Logs limpiados');
            $this->newLine();
            $this->info('🔑 CREDENCIALES POR DEFECTO:');
            $this->info('   👤 Admin: admin@4gmovil.com / Admin123!');
            $this->newLine();
            $this->info('🚀 PRÓXIMOS PASOS:');
            $this->info('   1. Cerrar sesión de Google en el navegador');
            $this->info('   2. Borrar cookies y caché del navegador');
            $this->info('   3. Intentar login con Google nuevamente');
            $this->info('   4. Verificar que funciona correctamente');

        } catch (\Exception $e) {
            $this->error("❌ ERROR DURANTE RESET NUCLEAR: " . $e->getMessage());
            $this->error("La base de datos puede estar en un estado inconsistente.");
            $this->error("Considera restaurar desde un backup o ejecutar manualmente:");
            $this->error("php artisan migrate:fresh --seed");
            return 1;
        }

        return 0;
    }
}
