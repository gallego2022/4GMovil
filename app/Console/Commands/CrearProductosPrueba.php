<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\EspecificacionCategoria;
use App\Models\EspecificacionProducto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CrearProductosPrueba extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'productos:crear-prueba 
                            {--categoria= : ID de la categoría específica}
                            {--cantidad=10 : Cantidad de productos por categoría}
                            {--forzar : Forzar la creación incluso si ya existen productos}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crear productos de prueba con especificaciones dinámicas por categoría';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 **Creando Productos de Prueba**');
        $this->newLine();

        // Verificar si hay categorías
        $categorias = Categoria::all();
        if ($categorias->isEmpty()) {
            $this->error('❌ No hay categorías disponibles. Ejecuta primero los seeders.');
            return 1;
        }

        // Verificar si hay marcas
        $marcas = Marca::all();
        if ($marcas->isEmpty()) {
            $this->error('❌ No hay marcas disponibles. Ejecuta primero los seeders.');
            return 1;
        }

        $categoriaId = $this->option('categoria');
        $cantidad = (int) $this->option('cantidad');
        $forzar = $this->option('forzar');

        // Si se especifica una categoría, solo procesar esa
        if ($categoriaId) {
            $categoria = Categoria::find($categoriaId);
            if (!$categoria) {
                $this->error("❌ Categoría con ID {$categoriaId} no encontrada.");
                return 1;
            }
            $categorias = collect([$categoria]);
        }

        $totalProductos = 0;

        foreach ($categorias as $categoria) {
            $this->info("📱 **Procesando categoría: {$categoria->nombre_categoria}**");
            
            // Verificar si ya existen productos en esta categoría
            $productosExistentes = Producto::where('categoria_id', $categoria->categoria_id)->count();
            if ($productosExistentes > 0 && !$forzar) {
                $this->warn("   ⚠️  Ya existen {$productosExistentes} productos en esta categoría. Usa --forzar para continuar.");
                continue;
            }

            // Obtener especificaciones de la categoría
            $especificaciones = EspecificacionCategoria::where('categoria_id', $categoria->categoria_id)
                ->where('activo', true)
                ->orderBy('orden', 'asc')
                ->get();

            if ($especificaciones->isEmpty()) {
                $this->warn("   ⚠️  No hay especificaciones definidas para esta categoría.");
                continue;
            }

            $this->info("   📊 Especificaciones disponibles: {$especificaciones->count()}");
            
            // Crear productos para esta categoría
            $productosCreados = $this->crearProductosParaCategoria($categoria, $marcas, $especificaciones, $cantidad);
            $totalProductos += $productosCreados;
            
            $this->info("   ✅ Creados {$productosCreados} productos para {$categoria->nombre_categoria}");
            $this->newLine();
        }

        $this->info("🎉 **Proceso completado exitosamente!**");
        $this->info("📊 Total de productos creados: {$totalProductos}");
        $this->info("💡 Usa 'php artisan productos:crear-prueba --help' para ver más opciones.");

        return 0;
    }

    /**
     * Crear productos para una categoría específica
     */
    private function crearProductosParaCategoria($categoria, $marcas, $especificaciones, $cantidad)
    {
        $productosCreados = 0;
        $bar = $this->output->createProgressBar($cantidad);
        $bar->start();

        for ($i = 1; $i <= $cantidad; $i++) {
            try {
                DB::beginTransaction();

                // Seleccionar marca aleatoria
                $marca = $marcas->random();
                
                // Generar datos del producto
                $datosProducto = $this->generarDatosProducto($categoria, $marca, $i);
                
                // Crear el producto
                $producto = Producto::create($datosProducto);
                
                // Crear especificaciones para el producto
                $this->crearEspecificacionesProducto($producto, $especificaciones);
                
                DB::commit();
                $productosCreados++;
                $bar->advance();

            } catch (\Exception $e) {
                DB::rollBack();
                $this->error("   ❌ Error creando producto {$i}: " . $e->getMessage());
            }
        }

        $bar->finish();
        $this->newLine();

        return $productosCreados;
    }

    /**
     * Generar datos del producto
     */
    private function generarDatosProducto($categoria, $marca, $numero)
    {
        $nombres = $this->obtenerNombresProducto($categoria->nombre_categoria);
        $nombre = $nombres[array_rand($nombres)] . ' ' . $numero;
        
        $precio = rand(500000, 5000000);
        $costoUnitario = $precio * 0.7; // 70% del precio como costo unitario
        
        return [
            'nombre_producto' => $nombre,
            'descripcion' => "Producto de prueba {$numero} de la categoría {$categoria->nombre_categoria}",
            'precio' => $precio,
            'precio_anterior' => rand(600000, 6000000),
            'costo_unitario' => $costoUnitario,
            'stock' => rand(5, 50),
            'stock_minimo' => 5,
            'stock_maximo' => 100,
            'categoria_id' => $categoria->categoria_id,
            'marca_id' => $marca->marca_id,
            'estado' => 'nuevo',
            'activo' => true,
            'sku' => strtoupper(Str::random(8)),
            'codigo_barras' => rand(1000000000000, 9999999999999),
            'peso' => rand(100, 2000) / 1000, // kg
            'dimensiones' => rand(10, 50) . 'x' . rand(10, 50) . 'x' . rand(5, 20) . ' cm',
        ];
    }

    /**
     * Obtener nombres de productos según la categoría
     */
    private function obtenerNombresProducto($categoria)
    {
        $nombres = [
            'Smartphones' => [
                'iPhone', 'Samsung Galaxy', 'Xiaomi Redmi', 'Huawei P', 'OnePlus', 
                'Google Pixel', 'Motorola Edge', 'Sony Xperia', 'LG G', 'Nokia'
            ],
            'Laptops' => [
                'MacBook Pro', 'Dell XPS', 'HP Pavilion', 'Lenovo ThinkPad', 'ASUS ROG',
                'Acer Swift', 'MSI Gaming', 'Razer Blade', 'Alienware', 'Gigabyte Aero'
            ],
            'Tablets' => [
                'iPad Pro', 'Samsung Galaxy Tab', 'Amazon Fire', 'Huawei MatePad',
                'Lenovo Tab', 'Xiaomi Pad', 'ASUS ZenPad', 'Sony Xperia Tablet'
            ],
            'Auriculares' => [
                'Sony WH', 'Bose QuietComfort', 'Apple AirPods', 'Samsung Galaxy Buds',
                'Jabra Elite', 'Sennheiser Momentum', 'Audio-Technica', 'Beats'
            ],
            'Bafles' => [
                'JBL Flip', 'Bose SoundLink', 'Sony SRS', 'Ultimate Ears Boom',
                'Anker Soundcore', 'Marshall', 'Harman Kardon', 'Bang & Olufsen'
            ],
            'Smartwatches' => [
                'Apple Watch', 'Samsung Galaxy Watch', 'Garmin Fenix', 'Fitbit',
                'Huawei Watch', 'Xiaomi Mi Band', 'Amazfit', 'Polar'
            ]
        ];

        return $nombres[$categoria] ?? ['Producto', 'Dispositivo', 'Equipo', 'Gadget'];
    }

    /**
     * Crear especificaciones para el producto
     */
    private function crearEspecificacionesProducto($producto, $especificaciones)
    {
        foreach ($especificaciones as $espec) {
            $valor = $this->generarValorEspecificacion($espec);
            
            if ($valor !== null) {
                EspecificacionProducto::create([
                    'producto_id' => $producto->producto_id,
                    'especificacion_id' => $espec->especificacion_id,
                    'valor' => $valor
                ]);
            }
        }
    }

    /**
     * Generar valor para una especificación
     */
    private function generarValorEspecificacion($especificacion)
    {
        $nombreCampo = $especificacion->nombre_campo;
        $tipoCampo = $especificacion->tipo_campo;

        switch ($nombreCampo) {
            case 'ram':
                $valores = ['2', '3', '4', '6', '8', '12', '16', '32'];
                return $valores[array_rand($valores)];
                
            case 'almacenamiento':
                $valores = ['32', '64', '128', '256', '512', '1TB'];
                return $valores[array_rand($valores)];
                
            case 'pantalla':
                $valores = ['5.5', '6.1', '6.7', '7.0', '7.9', '10.1', '11', '12.9', '13.3', '14', '15.6', '17'];
                return $valores[array_rand($valores)];
                
            case 'resolucion':
                $resoluciones = [
                    '720p', '1080p', '1440p', '4K', '1920x1080', '2560x1440', '3840x2160',
                    '1334x750', '1792x828', '2436x1125', '2688x1242', '2778x1284'
                ];
                return $resoluciones[array_rand($resoluciones)];
                
            case 'procesador':
                $procesadores = [
                    'Intel Core i3', 'Intel Core i5', 'Intel Core i7', 'Intel Core i9',
                    'AMD Ryzen 3', 'AMD Ryzen 5', 'AMD Ryzen 7', 'AMD Ryzen 9',
                    'Apple M1', 'Apple M2', 'Apple M3', 'Qualcomm Snapdragon 8',
                    'MediaTek Dimensity', 'Samsung Exynos'
                ];
                return $procesadores[array_rand($procesadores)];
                
            case 'camara_principal':
                $valores = ['12MP', '16MP', '20MP', '24MP', '48MP', '64MP', '108MP'];
                return $valores[array_rand($valores)];
                
            case 'camara_frontal':
                $valores = ['8MP', '12MP', '16MP', '20MP', '24MP'];
                return $valores[array_rand($valores)];
                
            case 'bateria':
                $valores = ['3000mAh', '4000mAh', '4500mAh', '5000mAh', '6000mAh', '8000mAh'];
                return $valores[array_rand($valores)];
                
            case 'sistema_operativo':
                $sistemas = ['iOS', 'Android', 'Windows', 'macOS', 'Linux', 'Chrome OS'];
                return $sistemas[array_rand($sistemas)];
                
            case 'version_os':
                $versiones = ['14.0', '15.0', '16.0', '17.0', '13', '14', '15', '11', '12', '13'];
                return $versiones[array_rand($versiones)];
                
            case 'conectividad':
                $conectividades = ['WiFi 6', 'WiFi 5', 'Bluetooth 5.0', 'Bluetooth 5.2', '5G', '4G LTE'];
                return $conectividades[array_rand($conectividades)];
                
            case 'wifi':
                return rand(0, 1) ? '1' : '0';
                
            case 'bluetooth':
                return rand(0, 1) ? '1' : '0';
                
            case 'gpu':
                $gpus = [
                    'Intel UHD Graphics', 'NVIDIA GeForce RTX 3060', 'NVIDIA GeForce RTX 4070',
                    'AMD Radeon RX 6600', 'AMD Radeon RX 7600', 'Apple M1 GPU',
                    'Adreno 650', 'Mali-G78', 'PowerVR'
                ];
                return $gpus[array_rand($gpus)];
                
            case 'peso':
                return (rand(100, 3000) / 1000) . ' kg';
                
            case 'dimensiones':
                $ancho = rand(10, 50);
                $alto = rand(10, 50);
                $profundidad = rand(5, 20);
                return "{$ancho}x{$alto}x{$profundidad} cm";
                
            default:
                // Para campos de texto genéricos
                if ($tipoCampo === 'checkbox') {
                    return rand(0, 1) ? '1' : '0';
                } elseif ($tipoCampo === 'select' && $especificacion->opciones) {
                    $opciones = $especificacion->opciones;
                    if (is_array($opciones) && !empty($opciones)) {
                        return $opciones[array_rand($opciones)];
                    }
                }
                return 'Valor de prueba';
        }
    }
}
