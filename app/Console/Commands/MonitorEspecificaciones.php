<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Categoria;
use App\Models\EspecificacionCategoria;

class MonitorEspecificaciones extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'especificaciones:monitor {--categoria= : ID de categoría específica} {--detallado : Mostrar información detallada}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monitorear especificaciones de categorías';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Monitoreando especificaciones de categorías...');

        $categoriaId = $this->option('categoria');
        $detallado = $this->option('detallado');

        if ($categoriaId) {
            // Monitorear categoría específica
            $categoria = Categoria::find($categoriaId);
            if (!$categoria) {
                $this->error("❌ Categoría con ID {$categoriaId} no encontrada.");
                return 1;
            }

            $this->monitorCategoria($categoria, $detallado);
        } else {
            // Monitorear todas las categorías
            $categorias = Categoria::withCount('especificaciones')->get();
            
            $this->info("\n📊 RESUMEN GENERAL:");
            $this->table(
                ['ID', 'Categoría', 'Especificaciones', 'Activas', 'Requeridas'],
                $categorias->map(function ($cat) {
                    $activas = $cat->especificaciones()->where('activo', true)->count();
                    $requeridas = $cat->especificaciones()->where('requerido', true)->count();
                    
                    return [
                        $cat->categoria_id,
                        $cat->nombre,
                        $cat->especificaciones_count,
                        $activas,
                        $requeridas
                    ];
                })
            );

            if ($detallado) {
                foreach ($categorias as $categoria) {
                    $this->monitorCategoria($categoria, true);
                }
            }
        }

        return 0;
    }

    private function monitorCategoria(Categoria $categoria, bool $detallado = false)
    {
        $this->info("\n🎯 CATEGORÍA: {$categoria->nombre} (ID: {$categoria->categoria_id})");
        
        $especificaciones = $categoria->especificaciones()->orderBy('orden')->get();
        
        if ($especificaciones->count() === 0) {
            $this->warn("  ⚠️  No tiene especificaciones configuradas");
            return;
        }

        $activas = $especificaciones->where('activo', true)->count();
        $requeridas = $especificaciones->where('requerido', true)->count();
        
        $this->line("  📋 Total especificaciones: {$especificaciones->count()}");
        $this->line("  ✅ Activas: {$activas}");
        $this->line("  🔴 Requeridas: {$requeridas}");

        if ($detallado) {
            $this->info("  📝 Detalle de especificaciones:");
            
            foreach ($especificaciones as $esp) {
                $status = $esp->activo ? '✅' : '❌';
                $required = $esp->requerido ? '🔴' : '⚪';
                $tipo = ucfirst($esp->tipo_campo);
                
                $this->line("    {$status} {$required} [{$esp->orden}] {$esp->etiqueta} ({$tipo})");
                
                if ($esp->descripcion) {
                    $this->line("       📝 {$esp->descripcion}");
                }
                
                if ($esp->opciones) {
                    $opciones = implode(', ', $esp->opciones_array);
                    $this->line("       🎯 Opciones: {$opciones}");
                }
            }
        }

        // Estadísticas por tipo de campo
        $tipos = $especificaciones->groupBy('tipo_campo')->map->count();
        $this->info("  📊 Distribución por tipo:");
        foreach ($tipos as $tipo => $count) {
            $this->line("    - " . ucfirst($tipo) . ": {$count}");
        }
    }
}
