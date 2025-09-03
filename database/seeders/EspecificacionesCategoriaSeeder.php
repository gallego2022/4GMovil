<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EspecificacionesCategoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar si ya existen especificaciones
        if (DB::table('especificaciones_categoria')->count() > 0) {
            $this->command->info('La tabla especificaciones_categoria ya tiene datos. Saltando seeder...');
            return;
        }

        // Primero necesitamos crear algunas categorías básicas si no existen
        $categorias = [
            ['nombre' => 'Smartphones'],
            ['nombre' => 'Laptops'],
            ['nombre' => 'Tablets'],
            ['nombre' => 'Auriculares'],
            ['nombre' => 'Bafles'],
            ['nombre' => 'Smartwatches'],
            ['nombre' => 'Accesorios'],
        ];

        foreach ($categorias as $categoria) {
            DB::table('categorias')->insertOrIgnore([
                'nombre' => $categoria['nombre'],
                'descripcion' => 'Categoría de ' . $categoria['nombre'],
                'estado' => true,
                'orden' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Obtener las categorías creadas
        $smartphones = DB::table('categorias')->where('nombre', 'Smartphones')->first();
        $laptops = DB::table('categorias')->where('nombre', 'Laptops')->first();
        $tablets = DB::table('categorias')->where('nombre', 'Tablets')->first();
        $auriculares = DB::table('categorias')->where('nombre', 'Auriculares')->first();
        $bafles = DB::table('categorias')->where('nombre', 'Bafles')->first();
        $smartwatches = DB::table('categorias')->where('nombre', 'Smartwatches')->first();

        // Especificaciones para Smartphones
        if ($smartphones) {
            $this->crearEspecificacionesSmartphones($smartphones->categoria_id);
        }

        // Especificaciones para Laptops
        if ($laptops) {
            $this->crearEspecificacionesLaptops($laptops->categoria_id);
        }

        // Especificaciones para Tablets
        if ($tablets) {
            $this->crearEspecificacionesTablets($tablets->categoria_id);
        }

        // Especificaciones para Auriculares
        if ($auriculares) {
            $this->crearEspecificacionesAuriculares($auriculares->categoria_id);
        }

        // Especificaciones para Bafles
        if ($bafles) {
            $this->crearEspecificacionesBafles($bafles->categoria_id);
        }

        // Especificaciones para Smartwatches
        if ($smartwatches) {
            $this->crearEspecificacionesSmartwatches($smartwatches->categoria_id);
        }

        $this->command->info('✅ Especificaciones de categorías creadas exitosamente!');
    }

    private function crearEspecificacionesSmartphones($categoriaId)
    {
        $especificaciones = [
            ['nombre_campo' => 'pantalla', 'etiqueta' => 'Tamaño de Pantalla', 'tipo_campo' => 'texto', 'unidad' => 'pulgadas', 'requerido' => true, 'orden' => 1],
            ['nombre_campo' => 'resolucion', 'etiqueta' => 'Resolución', 'tipo_campo' => 'texto', 'requerido' => true, 'orden' => 2],
            ['nombre_campo' => 'ram', 'etiqueta' => 'Memoria RAM', 'tipo_campo' => 'select', 'unidad' => 'GB', 'opciones' => ['2', '3', '4', '6', '8', '12', '16'], 'requerido' => true, 'orden' => 3],
            ['nombre_campo' => 'almacenamiento', 'etiqueta' => 'Almacenamiento', 'tipo_campo' => 'select', 'unidad' => 'GB', 'opciones' => ['32', '64', '128', '256', '512', '1TB'], 'requerido' => true, 'orden' => 4],
            ['nombre_campo' => 'procesador', 'etiqueta' => 'Procesador', 'tipo_campo' => 'texto', 'requerido' => true, 'orden' => 5],
            ['nombre_campo' => 'bateria', 'etiqueta' => 'Capacidad de Batería', 'tipo_campo' => 'numero', 'unidad' => 'mAh', 'requerido' => true, 'orden' => 6],
            ['nombre_campo' => 'camara_principal', 'etiqueta' => 'Cámara Principal', 'tipo_campo' => 'texto', 'unidad' => 'MP', 'requerido' => true, 'orden' => 7],
            ['nombre_campo' => 'camara_frontal', 'etiqueta' => 'Cámara Frontal', 'tipo_campo' => 'texto', 'unidad' => 'MP', 'requerido' => false, 'orden' => 8],
            ['nombre_campo' => 'sistema_operativo', 'etiqueta' => 'Sistema Operativo', 'tipo_campo' => 'select', 'opciones' => ['Android', 'iOS'], 'requerido' => true, 'orden' => 9],
            ['nombre_campo' => 'version_so', 'etiqueta' => 'Versión del SO', 'tipo_campo' => 'texto', 'requerido' => false, 'orden' => 10],
            ['nombre_campo' => 'carga_rapida', 'etiqueta' => 'Carga Rápida', 'tipo_campo' => 'checkbox', 'requerido' => false, 'orden' => 11],
            ['nombre_campo' => 'resistente_agua', 'etiqueta' => 'Resistente al Agua', 'tipo_campo' => 'checkbox', 'requerido' => false, 'orden' => 12],
        ];

        $this->insertarEspecificaciones($categoriaId, $especificaciones);
    }

    private function crearEspecificacionesLaptops($categoriaId)
    {
        $especificaciones = [
            ['nombre_campo' => 'pantalla', 'etiqueta' => 'Tamaño de Pantalla', 'tipo_campo' => 'texto', 'unidad' => 'pulgadas', 'requerido' => true, 'orden' => 1],
            ['nombre_campo' => 'resolucion', 'etiqueta' => 'Resolución', 'tipo_campo' => 'select', 'opciones' => ['1366x768', '1920x1080', '2560x1440', '3840x2160'], 'requerido' => true, 'orden' => 2],
            ['nombre_campo' => 'ram', 'etiqueta' => 'Memoria RAM', 'tipo_campo' => 'select', 'unidad' => 'GB', 'opciones' => ['4', '8', '16', '32', '64'], 'requerido' => true, 'orden' => 3],
            ['nombre_campo' => 'almacenamiento', 'etiqueta' => 'Almacenamiento', 'tipo_campo' => 'select', 'unidad' => 'GB', 'opciones' => ['128', '256', '512', '1TB', '2TB'], 'requerido' => true, 'orden' => 4],
            ['nombre_campo' => 'tipo_almacenamiento', 'etiqueta' => 'Tipo de Almacenamiento', 'tipo_campo' => 'select', 'opciones' => ['SSD', 'HDD', 'SSD + HDD'], 'requerido' => true, 'orden' => 5],
            ['nombre_campo' => 'procesador', 'etiqueta' => 'Procesador', 'tipo_campo' => 'texto', 'requerido' => true, 'orden' => 6],
            ['nombre_campo' => 'tarjeta_grafica', 'etiqueta' => 'Tarjeta Gráfica', 'tipo_campo' => 'texto', 'requerido' => false, 'orden' => 7],
            ['nombre_campo' => 'sistema_operativo', 'etiqueta' => 'Sistema Operativo', 'tipo_campo' => 'select', 'opciones' => ['Windows', 'macOS', 'Linux', 'Sin SO'], 'requerido' => true, 'orden' => 8],
            ['nombre_campo' => 'bateria', 'etiqueta' => 'Duración de Batería', 'tipo_campo' => 'texto', 'unidad' => 'horas', 'requerido' => false, 'orden' => 9],
            ['nombre_campo' => 'peso', 'etiqueta' => 'Peso', 'tipo_campo' => 'numero', 'unidad' => 'kg', 'requerido' => false, 'orden' => 10],
            ['nombre_campo' => 'puertos', 'etiqueta' => 'Puertos Disponibles', 'tipo_campo' => 'texto', 'requerido' => false, 'orden' => 11],
        ];

        $this->insertarEspecificaciones($categoriaId, $especificaciones);
    }

    private function crearEspecificacionesTablets($categoriaId)
    {
        $especificaciones = [
            ['nombre_campo' => 'pantalla', 'etiqueta' => 'Tamaño de Pantalla', 'tipo_campo' => 'texto', 'unidad' => 'pulgadas', 'requerido' => true, 'orden' => 1],
            ['nombre_campo' => 'resolucion', 'etiqueta' => 'Resolución', 'tipo_campo' => 'texto', 'requerido' => true, 'orden' => 2],
            ['nombre_campo' => 'ram', 'etiqueta' => 'Memoria RAM', 'tipo_campo' => 'select', 'unidad' => 'GB', 'opciones' => ['2', '3', '4', '6', '8'], 'requerido' => true, 'orden' => 3],
            ['nombre_campo' => 'almacenamiento', 'etiqueta' => 'Almacenamiento', 'tipo_campo' => 'select', 'unidad' => 'GB', 'opciones' => ['32', '64', '128', '256', '512'], 'requerido' => true, 'orden' => 4],
            ['nombre_campo' => 'procesador', 'etiqueta' => 'Procesador', 'tipo_campo' => 'texto', 'requerido' => true, 'orden' => 5],
            ['nombre_campo' => 'bateria', 'etiqueta' => 'Capacidad de Batería', 'tipo_campo' => 'numero', 'unidad' => 'mAh', 'requerido' => true, 'orden' => 6],
            ['nombre_campo' => 'camara_principal', 'etiqueta' => 'Cámara Principal', 'tipo_campo' => 'texto', 'unidad' => 'MP', 'requerido' => false, 'orden' => 7],
            ['nombre_campo' => 'camara_frontal', 'etiqueta' => 'Cámara Frontal', 'tipo_campo' => 'texto', 'unidad' => 'MP', 'requerido' => false, 'orden' => 8],
            ['nombre_campo' => 'sistema_operativo', 'etiqueta' => 'Sistema Operativo', 'tipo_campo' => 'select', 'opciones' => ['Android', 'iOS', 'Windows'], 'requerido' => true, 'orden' => 9],
            ['nombre_campo' => 'conectividad', 'etiqueta' => 'Conectividad', 'tipo_campo' => 'select', 'opciones' => ['WiFi', 'WiFi + 4G', 'WiFi + 5G'], 'requerido' => true, 'orden' => 10],
        ];

        $this->insertarEspecificaciones($categoriaId, $especificaciones);
    }

    private function crearEspecificacionesAuriculares($categoriaId)
    {
        $especificaciones = [
            ['nombre_campo' => 'tipo', 'etiqueta' => 'Tipo de Auricular', 'tipo_campo' => 'select', 'opciones' => ['In-ear', 'On-ear', 'Over-ear', 'True Wireless'], 'requerido' => true, 'orden' => 1],
            ['nombre_campo' => 'conectividad', 'etiqueta' => 'Conectividad', 'tipo_campo' => 'select', 'opciones' => ['Cableado', 'Bluetooth', 'Cableado + Bluetooth'], 'requerido' => true, 'orden' => 2],
            ['nombre_campo' => 'impedancia', 'etiqueta' => 'Impedancia', 'tipo_campo' => 'numero', 'unidad' => 'Ω', 'requerido' => false, 'orden' => 3],
            ['nombre_campo' => 'frecuencia', 'etiqueta' => 'Rango de Frecuencia', 'tipo_campo' => 'texto', 'unidad' => 'Hz', 'requerido' => false, 'orden' => 4],
            ['nombre_campo' => 'bateria', 'etiqueta' => 'Duración de Batería', 'tipo_campo' => 'texto', 'unidad' => 'horas', 'requerido' => false, 'orden' => 5],
            ['nombre_campo' => 'cancelacion_ruido', 'etiqueta' => 'Cancelación de Ruido', 'tipo_campo' => 'checkbox', 'requerido' => false, 'orden' => 6],
            ['nombre_campo' => 'resistente_agua', 'etiqueta' => 'Resistente al Agua', 'tipo_campo' => 'checkbox', 'requerido' => false, 'orden' => 7],
            ['nombre_campo' => 'microfono', 'etiqueta' => 'Micrófono Integrado', 'tipo_campo' => 'checkbox', 'requerido' => false, 'orden' => 8],
        ];

        $this->insertarEspecificaciones($categoriaId, $especificaciones);
    }

    private function crearEspecificacionesBafles($categoriaId)
    {
        $especificaciones = [
            ['nombre_campo' => 'tipo', 'etiqueta' => 'Tipo de Bafle', 'tipo_campo' => 'select', 'opciones' => ['Portátil', 'De Mesa', 'De Pared', 'Subwoofer'], 'requerido' => true, 'orden' => 1],
            ['nombre_campo' => 'potencia', 'etiqueta' => 'Potencia', 'tipo_campo' => 'numero', 'unidad' => 'W', 'requerido' => true, 'orden' => 2],
            ['nombre_campo' => 'conectividad', 'etiqueta' => 'Conectividad', 'tipo_campo' => 'select', 'opciones' => ['Cableado', 'Bluetooth', 'WiFi', 'Cableado + Bluetooth'], 'requerido' => true, 'orden' => 3],
            ['nombre_campo' => 'impedancia', 'etiqueta' => 'Impedancia', 'tipo_campo' => 'numero', 'unidad' => 'Ω', 'requerido' => false, 'orden' => 4],
            ['nombre_campo' => 'frecuencia', 'etiqueta' => 'Rango de Frecuencia', 'tipo_campo' => 'texto', 'unidad' => 'Hz', 'requerido' => false, 'orden' => 5],
            ['nombre_campo' => 'bateria', 'etiqueta' => 'Duración de Batería', 'tipo_campo' => 'texto', 'unidad' => 'horas', 'requerido' => false, 'orden' => 6],
            ['nombre_campo' => 'resistente_agua', 'etiqueta' => 'Resistente al Agua', 'tipo_campo' => 'checkbox', 'requerido' => false, 'orden' => 7],
            ['nombre_campo' => 'luces_rgb', 'etiqueta' => 'Luces RGB', 'tipo_campo' => 'checkbox', 'requerido' => false, 'orden' => 8],
            ['nombre_campo' => 'entradas', 'etiqueta' => 'Entradas Disponibles', 'tipo_campo' => 'texto', 'requerido' => false, 'orden' => 9],
        ];

        $this->insertarEspecificaciones($categoriaId, $especificaciones);
    }

    private function crearEspecificacionesSmartwatches($categoriaId)
    {
        $especificaciones = [
            ['nombre_campo' => 'pantalla', 'etiqueta' => 'Tamaño de Pantalla', 'tipo_campo' => 'texto', 'unidad' => 'pulgadas', 'requerido' => true, 'orden' => 1],
            ['nombre_campo' => 'resolucion', 'etiqueta' => 'Resolución', 'tipo_campo' => 'texto', 'requerido' => true, 'orden' => 2],
            ['nombre_campo' => 'bateria', 'etiqueta' => 'Duración de Batería', 'tipo_campo' => 'texto', 'unidad' => 'días', 'requerido' => true, 'orden' => 3],
            ['nombre_campo' => 'sistema_operativo', 'etiqueta' => 'Sistema Operativo', 'tipo_campo' => 'select', 'opciones' => ['watchOS', 'Wear OS', 'Tizen', 'Proprietary'], 'requerido' => true, 'orden' => 4],
            ['nombre_campo' => 'resistente_agua', 'etiqueta' => 'Resistente al Agua', 'tipo_campo' => 'checkbox', 'requerido' => false, 'orden' => 5],
            ['nombre_campo' => 'gps', 'etiqueta' => 'GPS Integrado', 'tipo_campo' => 'checkbox', 'requerido' => false, 'orden' => 6],
            ['nombre_campo' => 'ritmo_cardiaco', 'etiqueta' => 'Monitor de Ritmo Cardíaco', 'tipo_campo' => 'checkbox', 'requerido' => false, 'orden' => 7],
            ['nombre_campo' => 'oxigeno_sangre', 'etiqueta' => 'Monitor de Oxígeno en Sangre', 'tipo_campo' => 'checkbox', 'requerido' => false, 'orden' => 8],
            ['nombre_campo' => 'conectividad', 'etiqueta' => 'Conectividad', 'tipo_campo' => 'select', 'opciones' => ['Bluetooth', 'WiFi', 'Bluetooth + WiFi', 'Bluetooth + WiFi + LTE'], 'requerido' => true, 'orden' => 9],
        ];

        $this->insertarEspecificaciones($categoriaId, $especificaciones);
    }

    private function insertarEspecificaciones($categoriaId, $especificaciones)
    {
        foreach ($especificaciones as $espec) {
            DB::table('especificaciones_categoria')->insert([
                'categoria_id' => $categoriaId,
                'nombre_campo' => $espec['nombre_campo'],
                'etiqueta' => $espec['etiqueta'],
                'tipo_campo' => $espec['tipo_campo'],
                'opciones' => isset($espec['opciones']) ? json_encode($espec['opciones']) : null,
                'unidad' => $espec['unidad'] ?? null,
                'requerido' => $espec['requerido'],
                'orden' => $espec['orden'],
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
