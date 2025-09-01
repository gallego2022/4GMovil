<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Categoria;
use App\Models\EspecificacionCategoria;
use Illuminate\Support\Facades\DB;

class AgregarEspecificacionesCategoria extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'especificaciones:agregar 
                            {categoria_id : ID de la categoría}
                            {--forzar : Forzar la creación aunque existan especificaciones}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Agregar especificaciones predefinidas a una categoría';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $categoriaId = $this->argument('categoria_id');
        $forzar = $this->option('forzar');

        // Verificar que la categoría existe
        $categoria = Categoria::find($categoriaId);
        if (!$categoria) {
            $this->error("❌ Categoría con ID {$categoriaId} no encontrada.");
            return 1;
        }

        $this->info("📱 **Agregando especificaciones a: {$categoria->nombre_categoria}**");

        // Verificar si ya existen especificaciones
        $existentes = EspecificacionCategoria::where('categoria_id', $categoriaId)->count();
        if ($existentes > 0 && !$forzar) {
            if (!$this->confirm("Ya existen {$existentes} especificaciones. ¿Deseas continuar?")) {
                $this->info("❌ Operación cancelada.");
                return 0;
            }
        }

        // Obtener especificaciones para esta categoría
        $especificaciones = $this->obtenerEspecificacionesPorCategoria($categoriaId);
        if (empty($especificaciones)) {
            $this->error("❌ No hay especificaciones predefinidas para la categoría '{$categoria->nombre_categoria}'");
            $this->info("💡 Puedes crear especificaciones personalizadas usando el script 'agregar_especificaciones.php'");
            return 1;
        }

        $bar = $this->output->createProgressBar(count($especificaciones));
        $bar->start();

        $agregadas = 0;
        $existentes = 0;

        foreach ($especificaciones as $espec) {
            try {
                // Verificar si ya existe esta especificación
                $existe = EspecificacionCategoria::where('categoria_id', $categoriaId)
                    ->where('nombre_campo', $espec['nombre_campo'])
                    ->exists();
                
                if (!$existe) {
                    EspecificacionCategoria::create([
                        'categoria_id' => $categoriaId,
                        'nombre_campo' => $espec['nombre_campo'],
                        'etiqueta' => $espec['etiqueta'],
                        'tipo_campo' => $espec['tipo_campo'],
                        'opciones' => isset($espec['opciones']) ? $espec['opciones'] : null,
                        'unidad' => $espec['unidad'] ?? null,
                        'descripcion' => $espec['descripcion'] ?? null,
                        'requerido' => $espec['requerido'],
                        'orden' => $espec['orden'],
                        'activo' => true,
                    ]);
                    $agregadas++;
                } else {
                    $existentes++;
                }
                
                $bar->advance();
            } catch (\Exception $e) {
                $this->error("\n❌ Error al agregar {$espec['etiqueta']}: " . $e->getMessage());
            }
        }

        $bar->finish();
        $this->newLine();

        $this->info("✅ **Proceso completado!**");
        $this->info("📊 Especificaciones agregadas: {$agregadas}");
        if ($existentes > 0) {
            $this->info("📊 Especificaciones existentes: {$existentes}");
        }

        return 0;
    }

    /**
     * Obtener especificaciones predefinidas por categoría
     */
    private function obtenerEspecificacionesPorCategoria($categoriaId)
    {
        $especificacionesPorCategoria = [
            // Smartphones (ID: 1)
            1 => [
                ['nombre_campo' => 'pantalla', 'etiqueta' => 'Tamaño de Pantalla', 'tipo_campo' => 'text', 'unidad' => 'pulgadas', 'requerido' => true, 'orden' => 1],
                ['nombre_campo' => 'resolucion', 'etiqueta' => 'Resolución', 'tipo_campo' => 'text', 'requerido' => true, 'orden' => 2],
                ['nombre_campo' => 'ram', 'etiqueta' => 'Memoria RAM', 'tipo_campo' => 'select', 'unidad' => 'GB', 'opciones' => ['2', '3', '4', '6', '8', '12', '16', '32'], 'requerido' => true, 'orden' => 3],
                ['nombre_campo' => 'almacenamiento', 'etiqueta' => 'Almacenamiento', 'tipo_campo' => 'select', 'unidad' => 'GB', 'opciones' => ['32', '64', '128', '256', '512', '1TB'], 'requerido' => true, 'orden' => 4],
                ['nombre_campo' => 'procesador', 'etiqueta' => 'Procesador', 'tipo_campo' => 'text', 'requerido' => true, 'orden' => 5],
                ['nombre_campo' => 'bateria', 'etiqueta' => 'Capacidad de Batería', 'tipo_campo' => 'number', 'unidad' => 'mAh', 'requerido' => true, 'orden' => 6],
                ['nombre_campo' => 'camara_principal', 'etiqueta' => 'Cámara Principal', 'tipo_campo' => 'text', 'unidad' => 'MP', 'requerido' => true, 'orden' => 7],
                ['nombre_campo' => 'camara_frontal', 'etiqueta' => 'Cámara Frontal', 'tipo_campo' => 'text', 'unidad' => 'MP', 'requerido' => false, 'orden' => 8],
                ['nombre_campo' => 'sistema_operativo', 'etiqueta' => 'Sistema Operativo', 'tipo_campo' => 'select', 'opciones' => ['iOS', 'Android', 'HarmonyOS'], 'requerido' => true, 'orden' => 9],
                ['nombre_campo' => 'version_so', 'etiqueta' => 'Versión del SO', 'tipo_campo' => 'text', 'requerido' => false, 'orden' => 10],
                ['nombre_campo' => 'carga_rapida', 'etiqueta' => 'Carga Rápida', 'tipo_campo' => 'checkbox', 'requerido' => false, 'orden' => 11],
                ['nombre_campo' => 'resistente_agua', 'etiqueta' => 'Resistente al Agua', 'tipo_campo' => 'checkbox', 'requerido' => false, 'orden' => 12],
            ],
            
            // Laptops (ID: 2)
            2 => [
                ['nombre_campo' => 'pantalla', 'etiqueta' => 'Tamaño de Pantalla', 'tipo_campo' => 'text', 'unidad' => 'pulgadas', 'requerido' => true, 'orden' => 1],
                ['nombre_campo' => 'resolucion', 'etiqueta' => 'Resolución', 'tipo_campo' => 'select', 'opciones' => ['1366x768', '1920x1080', '2560x1440', '3840x2160'], 'requerido' => true, 'orden' => 2],
                ['nombre_campo' => 'ram', 'etiqueta' => 'Memoria RAM', 'tipo_campo' => 'select', 'unidad' => 'GB', 'opciones' => ['4', '8', '16', '32', '64'], 'requerido' => true, 'orden' => 3],
                ['nombre_campo' => 'almacenamiento', 'etiqueta' => 'Almacenamiento', 'tipo_campo' => 'select', 'unidad' => 'GB', 'opciones' => ['128', '256', '512', '1TB', '2TB'], 'requerido' => true, 'orden' => 4],
                ['nombre_campo' => 'tipo_almacenamiento', 'etiqueta' => 'Tipo de Almacenamiento', 'tipo_campo' => 'select', 'opciones' => ['SSD', 'HDD', 'SSD + HDD'], 'requerido' => true, 'orden' => 5],
                ['nombre_campo' => 'procesador', 'etiqueta' => 'Procesador', 'tipo_campo' => 'text', 'requerido' => true, 'orden' => 6],
                ['nombre_campo' => 'tarjeta_grafica', 'etiqueta' => 'Tarjeta Gráfica', 'tipo_campo' => 'text', 'requerido' => false, 'orden' => 7],
                ['nombre_campo' => 'sistema_operativo', 'etiqueta' => 'Sistema Operativo', 'tipo_campo' => 'select', 'opciones' => ['Windows', 'macOS', 'Linux', 'Sin SO'], 'requerido' => true, 'orden' => 8],
                ['nombre_campo' => 'bateria', 'etiqueta' => 'Duración de Batería', 'tipo_campo' => 'text', 'unidad' => 'horas', 'requerido' => false, 'orden' => 9],
                ['nombre_campo' => 'peso', 'etiqueta' => 'Peso', 'tipo_campo' => 'number', 'unidad' => 'kg', 'requerido' => false, 'orden' => 10],
                ['nombre_campo' => 'puertos', 'etiqueta' => 'Puertos Disponibles', 'tipo_campo' => 'textarea', 'requerido' => false, 'orden' => 11],
            ],
            
            // Tablets (ID: 3)
            3 => [
                ['nombre_campo' => 'pantalla', 'etiqueta' => 'Tamaño de Pantalla', 'tipo_campo' => 'text', 'unidad' => 'pulgadas', 'requerido' => true, 'orden' => 1],
                ['nombre_campo' => 'resolucion', 'etiqueta' => 'Resolución', 'tipo_campo' => 'text', 'requerido' => true, 'orden' => 2],
                ['nombre_campo' => 'ram', 'etiqueta' => 'Memoria RAM', 'tipo_campo' => 'select', 'unidad' => 'GB', 'opciones' => ['2', '3', '4', '6', '8'], 'requerido' => true, 'orden' => 3],
                ['nombre_campo' => 'almacenamiento', 'etiqueta' => 'Almacenamiento', 'tipo_campo' => 'select', 'unidad' => 'GB', 'opciones' => ['32', '64', '128', '256'], 'requerido' => true, 'orden' => 4],
                ['nombre_campo' => 'procesador', 'etiqueta' => 'Procesador', 'tipo_campo' => 'text', 'requerido' => true, 'orden' => 5],
                ['nombre_campo' => 'bateria', 'etiqueta' => 'Capacidad de Batería', 'tipo_campo' => 'number', 'unidad' => 'mAh', 'requerido' => true, 'orden' => 6],
                ['nombre_campo' => 'camara_principal', 'etiqueta' => 'Cámara Principal', 'tipo_campo' => 'text', 'unidad' => 'MP', 'requerido' => true, 'orden' => 7],
                ['nombre_campo' => 'camara_frontal', 'etiqueta' => 'Cámara Frontal', 'tipo_campo' => 'text', 'unidad' => 'MP', 'requerido' => false, 'orden' => 8],
                ['nombre_campo' => 'sistema_operativo', 'etiqueta' => 'Sistema Operativo', 'tipo_campo' => 'select', 'opciones' => ['iOS', 'Android', 'Windows'], 'requerido' => true, 'orden' => 9],
                ['nombre_campo' => 'conectividad', 'etiqueta' => 'Conectividad', 'tipo_campo' => 'select', 'opciones' => ['WiFi', 'WiFi + Cellular', 'WiFi + 5G'], 'requerido' => true, 'orden' => 10],
            ],
            
            // Auriculares (ID: 4)
            4 => [
                ['nombre_campo' => 'tipo', 'etiqueta' => 'Tipo de Auricular', 'tipo_campo' => 'select', 'opciones' => ['In-ear', 'On-ear', 'Over-ear', 'True Wireless'], 'requerido' => true, 'orden' => 1],
                ['nombre_campo' => 'conectividad', 'etiqueta' => 'Conectividad', 'tipo_campo' => 'select', 'opciones' => ['Cableado', 'Bluetooth', 'USB-C', 'Bluetooth + Cableado'], 'requerido' => true, 'orden' => 2],
                ['nombre_campo' => 'impedancia', 'etiqueta' => 'Impedancia', 'tipo_campo' => 'number', 'unidad' => 'Ω', 'requerido' => false, 'orden' => 3],
                ['nombre_campo' => 'frecuencia', 'etiqueta' => 'Rango de Frecuencia', 'tipo_campo' => 'text', 'unidad' => 'Hz', 'requerido' => false, 'orden' => 4],
                ['nombre_campo' => 'bateria', 'etiqueta' => 'Duración de Batería', 'tipo_campo' => 'text', 'unidad' => 'horas', 'requerido' => false, 'orden' => 5],
                ['nombre_campo' => 'cancelacion_ruido', 'etiqueta' => 'Cancelación de Ruido', 'tipo_campo' => 'checkbox', 'requerido' => false, 'orden' => 6],
                ['nombre_campo' => 'resistente_agua', 'etiqueta' => 'Resistente al Agua', 'tipo_campo' => 'checkbox', 'requerido' => false, 'orden' => 7],
                ['nombre_campo' => 'microfono', 'etiqueta' => 'Micrófono Integrado', 'tipo_campo' => 'checkbox', 'requerido' => false, 'orden' => 8],
            ],
            
            // Bafles (ID: 5)
            5 => [
                ['nombre_campo' => 'tipo', 'etiqueta' => 'Tipo de Bafle', 'tipo_campo' => 'select', 'opciones' => ['Portátil', 'Bluetooth', 'WiFi', 'Smart', 'Party'], 'requerido' => true, 'orden' => 1],
                ['nombre_campo' => 'potencia', 'etiqueta' => 'Potencia', 'tipo_campo' => 'number', 'unidad' => 'W', 'requerido' => true, 'orden' => 2],
                ['nombre_campo' => 'conectividad', 'etiqueta' => 'Conectividad', 'tipo_campo' => 'select', 'opciones' => ['Bluetooth', 'WiFi', 'Cableado', 'Bluetooth + WiFi'], 'requerido' => true, 'orden' => 3],
                ['nombre_campo' => 'impedancia', 'etiqueta' => 'Impedancia', 'tipo_campo' => 'number', 'unidad' => 'Ω', 'requerido' => false, 'orden' => 4],
                ['nombre_campo' => 'frecuencia', 'etiqueta' => 'Rango de Frecuencia', 'tipo_campo' => 'text', 'unidad' => 'Hz', 'requerido' => false, 'orden' => 5],
                ['nombre_campo' => 'bateria', 'etiqueta' => 'Duración de Batería', 'tipo_campo' => 'text', 'unidad' => 'horas', 'requerido' => false, 'orden' => 6],
                ['nombre_campo' => 'resistente_agua', 'etiqueta' => 'Resistente al Agua', 'tipo_campo' => 'checkbox', 'requerido' => false, 'orden' => 7],
                ['nombre_campo' => 'luces_rgb', 'etiqueta' => 'Luces RGB', 'tipo_campo' => 'checkbox', 'requerido' => false, 'orden' => 8],
                ['nombre_campo' => 'entradas', 'etiqueta' => 'Entradas Disponibles', 'tipo_campo' => 'textarea', 'requerido' => false, 'orden' => 9],
            ],
            
            // Smartwatches (ID: 6)
            6 => [
                ['nombre_campo' => 'pantalla', 'etiqueta' => 'Tamaño de Pantalla', 'tipo_campo' => 'text', 'unidad' => 'pulgadas', 'requerido' => true, 'orden' => 1],
                ['nombre_campo' => 'resolucion', 'etiqueta' => 'Resolución', 'tipo_campo' => 'text', 'requerido' => true, 'orden' => 2],
                ['nombre_campo' => 'bateria', 'etiqueta' => 'Duración de Batería', 'tipo_campo' => 'text', 'unidad' => 'días', 'requerido' => true, 'orden' => 3],
                ['nombre_campo' => 'sistema_operativo', 'etiqueta' => 'Sistema Operativo', 'tipo_campo' => 'select', 'opciones' => ['watchOS', 'Wear OS', 'Tizen', 'Proprietary'], 'requerido' => true, 'orden' => 4],
                ['nombre_campo' => 'resistente_agua', 'etiqueta' => 'Resistente al Agua', 'tipo_campo' => 'checkbox', 'requerido' => false, 'orden' => 5],
                ['nombre_campo' => 'gps', 'etiqueta' => 'GPS Integrado', 'tipo_campo' => 'checkbox', 'requerido' => false, 'orden' => 6],
                ['nombre_campo' => 'ritmo_cardiaco', 'etiqueta' => 'Monitor de Ritmo Cardíaco', 'tipo_campo' => 'checkbox', 'requerido' => false, 'orden' => 7],
                ['nombre_campo' => 'oxigeno_sangre', 'etiqueta' => 'Monitor de Oxígeno en Sangre', 'tipo_campo' => 'checkbox', 'requerido' => false, 'orden' => 8],
                ['nombre_campo' => 'conectividad', 'etiqueta' => 'Conectividad', 'tipo_campo' => 'select', 'opciones' => ['Bluetooth', 'WiFi', 'Bluetooth + WiFi', 'Bluetooth + WiFi + LTE'], 'requerido' => true, 'orden' => 9],
            ],
            
            // Accesorios (ID: 7)
            7 => [
                ['nombre_campo' => 'tipo', 'etiqueta' => 'Tipo de Accesorio', 'tipo_campo' => 'select', 'opciones' => ['Cable', 'Cargador', 'Carcasa', 'Protector', 'Soporte', 'Otro'], 'requerido' => true, 'orden' => 1],
                ['nombre_campo' => 'compatibilidad', 'etiqueta' => 'Compatibilidad', 'tipo_campo' => 'text', 'requerido' => false, 'orden' => 2],
                ['nombre_campo' => 'material', 'etiqueta' => 'Material', 'tipo_campo' => 'text', 'requerido' => false, 'orden' => 3],
                ['nombre_campo' => 'color', 'etiqueta' => 'Color', 'tipo_campo' => 'text', 'requerido' => false, 'orden' => 4],
                ['nombre_campo' => 'dimensiones', 'etiqueta' => 'Dimensiones', 'tipo_campo' => 'text', 'unidad' => 'cm', 'requerido' => false, 'orden' => 5],
            ],
        ];

        return $especificacionesPorCategoria[$categoriaId] ?? [];
    }
}
