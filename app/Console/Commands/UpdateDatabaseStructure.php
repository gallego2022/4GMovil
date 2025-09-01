<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateDatabaseStructure extends Command
{
    protected $signature = 'db:update-structure {--force : Forzar actualización sin preguntar}';
    protected $description = 'Actualiza la estructura de la base de datos para los nuevos campos de métodos de pago y estados de pedido';

    public function handle()
    {
        $this->info('🔧 Actualizando estructura de la base de datos...');

        if (!$this->option('force')) {
            if (!$this->confirm('¿Estás seguro de que quieres actualizar la estructura de la base de datos? Esto puede afectar datos existentes.')) {
                $this->info('Operación cancelada.');
                return 0;
            }
        }

        // Actualizar tabla metodos_pago
        if (Schema::hasTable('metodos_pago')) {
            $this->info('📦 Actualizando tabla metodos_pago...');
            
            // Agregar nuevas columnas si no existen
            if (!Schema::hasColumn('metodos_pago', 'descripcion')) {
                Schema::table('metodos_pago', function ($table) {
                    $table->text('descripcion')->nullable()->after('nombre');
                });
                $this->line('  ✅ Columna "descripcion" agregada');
            }
            
            if (!Schema::hasColumn('metodos_pago', 'tipo')) {
                Schema::table('metodos_pago', function ($table) {
                    $table->string('tipo', 50)->default('general')->after('descripcion');
                });
                $this->line('  ✅ Columna "tipo" agregada');
            }
            
            if (!Schema::hasColumn('metodos_pago', 'configuracion')) {
                Schema::table('metodos_pago', function ($table) {
                    $table->json('configuracion')->nullable()->after('tipo');
                });
                $this->line('  ✅ Columna "configuracion" agregada');
            }
            
            if (!Schema::hasColumn('metodos_pago', 'estado')) {
                Schema::table('metodos_pago', function ($table) {
                    $table->boolean('estado')->default(true)->after('configuracion');
                });
                $this->line('  ✅ Columna "estado" agregada');
            }

            // Renombrar nombre_metodo a nombre si existe
            if (Schema::hasColumn('metodos_pago', 'nombre_metodo')) {
                try {
                    Schema::table('metodos_pago', function ($table) {
                        $table->renameColumn('nombre_metodo', 'nombre');
                    });
                    $this->line('  ✅ Columna "nombre_metodo" renombrada a "nombre"');
                } catch (\Exception $e) {
                    $this->warn('  ⚠️  No se pudo renombrar la columna: ' . $e->getMessage());
                }
            }
        }

        // Actualizar tabla estados_pedido
        if (Schema::hasTable('estados_pedido')) {
            $this->info('📊 Actualizando tabla estados_pedido...');
            
            // Agregar nuevas columnas si no existen
            if (!Schema::hasColumn('estados_pedido', 'descripcion')) {
                Schema::table('estados_pedido', function ($table) {
                    $table->text('descripcion')->nullable()->after('nombre');
                });
                $this->line('  ✅ Columna "descripcion" agregada');
            }
            
            if (!Schema::hasColumn('estados_pedido', 'color')) {
                Schema::table('estados_pedido', function ($table) {
                    $table->string('color', 7)->default('#3b82f6')->after('descripcion');
                });
                $this->line('  ✅ Columna "color" agregada');
            }
            
            if (!Schema::hasColumn('estados_pedido', 'orden')) {
                Schema::table('estados_pedido', function ($table) {
                    $table->integer('orden')->default(1)->after('color');
                });
                $this->line('  ✅ Columna "orden" agregada');
            }
            
            if (!Schema::hasColumn('estados_pedido', 'estado')) {
                Schema::table('estados_pedido', function ($table) {
                    $table->boolean('estado')->default(true)->after('orden');
                });
                $this->line('  ✅ Columna "estado" agregada');
            }

            // Renombrar nombre_estado a nombre si existe
            if (Schema::hasColumn('estados_pedido', 'nombre_estado')) {
                try {
                    Schema::table('estados_pedido', function ($table) {
                        $table->renameColumn('nombre_estado', 'nombre');
                    });
                    $this->line('  ✅ Columna "nombre_estado" renombrada a "nombre"');
                } catch (\Exception $e) {
                    $this->warn('  ⚠️  No se pudo renombrar la columna: ' . $e->getMessage());
                }
            }

            // Habilitar timestamps si no están habilitados
            if (!Schema::hasColumn('estados_pedido', 'created_at')) {
                Schema::table('estados_pedido', function ($table) {
                    $table->timestamps();
                });
                $this->line('  ✅ Timestamps habilitados');
            }
        }

        $this->info('');
        $this->info('✅ Estructura de la base de datos actualizada exitosamente!');
        $this->info('');
        $this->info('🚀 Ahora puedes ejecutar: php artisan db:populate');
        $this->info('💡 O si prefieres migración fresca: php artisan migrate:fresh --seed');
        
        return 0;
    }
}
