<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Marca;

class ProductoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * NOTA: Los precios se almacenan directamente en pesos colombianos (sin decimales)
     * Ejemplo: $1,299,999 se almacena como 1299999
     */
    public function run(): void
    {
        // Verificar si ya existen productos
        if (Producto::count() > 0) {
            $this->command->info('Los productos ya existen, saltando...');
            return;
        }

        // Obtener categorías y marcas
        $categorias = Categoria::all()->keyBy('nombre');
        $marcas = Marca::all()->keyBy('nombre');

        // Crear productos para Smartphones
        if (isset($categorias['Smartphones'])) {
            $this->crearProductosSmartphones($categorias['Smartphones'], $marcas);
        }

        // Crear productos para Laptops
        if (isset($categorias['Laptops'])) {
            $this->crearProductosLaptops($categorias['Laptops'], $marcas);
        }

        // Crear productos para Tablets
        if (isset($categorias['Tablets'])) {
            $this->crearProductosTablets($categorias['Tablets'], $marcas);
        }

        // Crear productos para Auriculares
        if (isset($categorias['Auriculares'])) {
            $this->crearProductosAuriculares($categorias['Auriculares'], $marcas);
        }

        // Crear productos para Bafles
        if (isset($categorias['Bafles'])) {
            $this->crearProductosBafles($categorias['Bafles'], $marcas);
        }

        // Crear productos para Smartwatches
        if (isset($categorias['Smartwatches'])) {
            $this->crearProductosSmartwatches($categorias['Smartwatches'], $marcas);
        }

        // Crear productos para Accesorios
        if (isset($categorias['Accesorios'])) {
            $this->crearProductosAccesorios($categorias['Accesorios'], $marcas);
        }

        $this->command->info('✅ Productos creados exitosamente!');
    }

    private function crearProductosSmartphones($categoria, $marcas)
    {
        $productos = [
            [
                'nombre_producto' => 'Samsung Galaxy S24 Ultra',
                'descripcion' => 'El smartphone más avanzado de Samsung con S Pen integrado, cámara de 200MP y procesador Snapdragon 8 Gen 3',
                'precio' => 1300000,
                'stock' => 25,
                'estado' => 'nuevo',
                'activo' => true,
                'stock_reservado' => 3,
                'stock_disponible' => 22,
                'marca_id' => $marcas['Samsung']->marca_id
            ],
            [
                'nombre_producto' => 'iPhone 15 Pro Max',
                'descripcion' => 'El iPhone más potente con chip A17 Pro, cámara de 48MP y construcción en titanio',
                'precio' => 1200000,
                'stock' => 30,
                'estado' => 'nuevo',
                'activo' => true,
                'stock_reservado' => 5,
                'stock_disponible' => 25,
                'marca_id' => $marcas['Apple']->marca_id
            ],
            [
                'nombre_producto' => 'Xiaomi 14 Ultra',
                'descripcion' => 'Flagship de Xiaomi con cámara Leica de 50MP, pantalla AMOLED 2K y carga de 90W',
                'precio' => 900000,
                'stock' => 20,
                'estado' => 'nuevo',
                'activo' => true,
                'stock_reservado' => 2,
                'stock_disponible' => 18,
                'marca_id' => $marcas['Xiaomi']->marca_id
            ],
            [
                'nombre_producto' => 'Huawei P60 Pro',
                'descripcion' => 'Smartphone premium con cámara XMAGE de 48MP, pantalla OLED y diseño elegante',
                'precio' => 1000000,
                'stock' => 15,
                'estado' => 'nuevo',
                'activo' => true,
                'stock_reservado' => 1,
                'stock_disponible' => 14,
                'marca_id' => $marcas['Huawei']->marca_id
            ]
        ];

        foreach ($productos as $producto) {
            $producto['categoria_id'] = $categoria->categoria_id;
            Producto::create($producto);
        }

        $this->command->info("   📱 Smartphones: " . count($productos) . " productos creados");
    }

    private function crearProductosLaptops($categoria, $marcas)
    {
        $productos = [
            [
                'nombre_producto' => 'MacBook Pro 16" M3 Max',
                'descripcion' => 'Laptop profesional con chip M3 Max, 32GB RAM unificada y pantalla Liquid Retina XDR',
                'precio' => 3500000,
                'stock' => 12,
                'estado' => 'nuevo',
                'activo' => true,
                'stock_reservado' => 2,
                'stock_disponible' => 10,
                'marca_id' => $marcas['Apple']->marca_id
            ],
            [
                'nombre_producto' => 'Samsung Galaxy Book3 Ultra',
                'descripcion' => 'Laptop premium con Intel Core i9, RTX 4070, 32GB RAM y pantalla AMOLED 3K',
                'precio' => 2500000,
                'stock' => 8,
                'estado' => 'nuevo',
                'activo' => true,
                'stock_reservado' => 1,
                'stock_disponible' => 7,
                'marca_id' => $marcas['Samsung']->marca_id
            ],
            [
                'nombre_producto' => 'Xiaomi Mi Notebook Pro 15',
                'descripcion' => 'Laptop elegante con Intel Core i7, 16GB RAM, SSD 512GB y pantalla 3.5K',
                'precio' => 1300000,
                'stock' => 15,
                'estado' => 'nuevo',
                'activo' => true,
                'stock_reservado' => 3,
                'stock_disponible' => 12,
                'marca_id' => $marcas['Xiaomi']->marca_id
            ],
            [
                'nombre_producto' => 'Huawei MateBook X Pro',
                'descripcion' => 'Laptop ultrabook con Intel Core i7, 16GB RAM, SSD 1TB y pantalla táctil 3K',
                'precio' => 1800000,
                'stock' => 10,
                'estado' => 'nuevo',
                'activo' => true,
                'stock_reservado' => 2,
                'stock_disponible' => 8,
                'marca_id' => $marcas['Huawei']->marca_id
            ]
        ];

        foreach ($productos as $producto) {
            $producto['categoria_id'] = $categoria->categoria_id;
            Producto::create($producto);
        }

        $this->command->info("   💻 Laptops: " . count($productos) . " productos creados");
    }

    private function crearProductosTablets($categoria, $marcas)
    {
        $productos = [
            [
                'nombre_producto' => 'iPad Pro 12.9" M2',
                'descripcion' => 'Tablet profesional con chip M2, pantalla Liquid Retina XDR y compatibilidad con Apple Pencil',
                'precio' => 1100000,
                'stock' => 18,
                'estado' => 'nuevo',
                'activo' => true,
                'stock_reservado' => 4,
                'stock_disponible' => 14,
                'marca_id' => $marcas['Apple']->marca_id
            ],
            [
                'nombre_producto' => 'Samsung Galaxy Tab S9 Ultra',
                'descripcion' => 'Tablet Android premium con pantalla AMOLED 14.6", S Pen incluido y procesador Snapdragon 8 Gen 2',
                'precio' => 1200000,
                'stock' => 12,
                'estado' => 'nuevo',
                'activo' => true,
                'stock_reservado' => 2,
                'stock_disponible' => 10,
                'marca_id' => $marcas['Samsung']->marca_id
            ],
            [
                'nombre_producto' => 'Xiaomi Pad 6 Pro',
                'descripcion' => 'Tablet de alto rendimiento con pantalla 11", Snapdragon 8+ Gen 1 y 8GB RAM',
                'precio' => 400000,
                'stock' => 25,
                'estado' => 'nuevo',
                'activo' => true,
                'stock_reservado' => 5,
                'stock_disponible' => 20,
                'marca_id' => $marcas['Xiaomi']->marca_id
            ],
            [
                'nombre_producto' => 'Huawei MatePad Pro 13.2"',
                'descripcion' => 'Tablet profesional con pantalla OLED 13.2", procesador Kirin y compatibilidad con M-Pencil',
                'precio' => 800000,
                'stock' => 15,
                'estado' => 'nuevo',
                'activo' => true,
                'stock_reservado' => 3,
                'stock_disponible' => 12,
                'marca_id' => $marcas['Huawei']->marca_id
            ]
        ];

        foreach ($productos as $producto) {
            $producto['categoria_id'] = $categoria->categoria_id;
            Producto::create($producto);
        }

        $this->command->info("   📱 Tablets: " . count($productos) . " productos creados");
    }

    private function crearProductosAuriculares($categoria, $marcas)
    {
        $productos = [
            [
                'nombre_producto' => 'Sony WH-1000XM5',
                'descripcion' => 'Auriculares inalámbricos con cancelación de ruido líder en la industria y 30h de batería',
                'precio' => 400000,
                'stock' => 20,
                'estado' => 'nuevo',
                'activo' => true,
                'stock_reservado' => 4,
                'stock_disponible' => 16,
                'marca_id' => $marcas['Sony']->marca_id
            ],
            [
                'nombre_producto' => 'Apple AirPods Pro 2',
                'descripcion' => 'Auriculares inalámbricos con cancelación de ruido activa y audio espacial',
                'precio' => 250000,
                'stock' => 35,
                'estado' => 'nuevo',
                'activo' => true,
                'stock_reservado' => 7,
                'stock_disponible' => 28,
                'marca_id' => $marcas['Apple']->marca_id
            ],
            [
                'nombre_producto' => 'Samsung Galaxy Buds2 Pro',
                'descripcion' => 'Auriculares true wireless con cancelación de ruido inteligente y audio Hi-Fi 24-bit',
                'precio' => 200000,
                'stock' => 28,
                'estado' => 'nuevo',
                'activo' => true,
                'stock_reservado' => 5,
                'stock_disponible' => 23,
                'marca_id' => $marcas['Samsung']->marca_id
            ],
            [
                'nombre_producto' => 'Huawei FreeBuds Pro 3',
                'descripcion' => 'Auriculares premium con cancelación de ruido inteligente y audio Hi-Fi',
                'precio' => 180000,
                'stock' => 15,
                'estado' => 'nuevo',
                'activo' => true,
                'stock_reservado' => 3,
                'stock_disponible' => 12,
                'marca_id' => $marcas['Huawei']->marca_id
            ]
        ];

        foreach ($productos as $producto) {
            $producto['categoria_id'] = $categoria->categoria_id;
            Producto::create($producto);
        }

        $this->command->info("   🎧 Auriculares: " . count($productos) . " productos creados");
    }

    private function crearProductosBafles($categoria, $marcas)
    {
        $productos = [
            [
                'nombre_producto' => 'Sony SRS-XB43',
                'descripcion' => 'Altavoz Bluetooth portátil con EXTRA BASS, luces LED y hasta 24h de reproducción',
                'precio' => 200000,
                'stock' => 15,
                'estado' => 'nuevo',
                'activo' => true,
                'stock_reservado' => 2,
                'stock_disponible' => 13,
                'marca_id' => $marcas['Sony']->marca_id
            ],
            [
                'nombre_producto' => 'JBL Charge 5',
                'descripcion' => 'Altavoz Bluetooth portátil con 20h de batería y resistencia al agua IPX7',
                'precio' => 180000,
                'stock' => 20,
                'estado' => 'nuevo',
                'activo' => true,
                'stock_reservado' => 4,
                'stock_disponible' => 16,
                'marca_id' => $marcas['Samsung']->marca_id
            ],
            [
                'nombre_producto' => 'Xiaomi Mi Smart Speaker',
                'descripcion' => 'Altavoz inteligente con asistente de voz y conectividad WiFi',
                'precio' => 90000,
                'stock' => 25,
                'estado' => 'nuevo',
                'activo' => true,
                'stock_reservado' => 5,
                'stock_disponible' => 20,
                'marca_id' => $marcas['Xiaomi']->marca_id
            ],
            [
                'nombre_producto' => 'Huawei Sound Joy',
                'descripcion' => 'Altavoz portátil con diseño cilíndrico y hasta 26h de reproducción',
                'precio' => 160000,
                'stock' => 18,
                'estado' => 'nuevo',
                'activo' => true,
                'stock_reservado' => 3,
                'stock_disponible' => 15,
                'marca_id' => $marcas['Huawei']->marca_id
            ]
        ];

        foreach ($productos as $producto) {
            $producto['categoria_id'] = $categoria->categoria_id;
            Producto::create($producto);
        }

        $this->command->info("   🔊 Bafles: " . count($productos) . " productos creados");
    }

    private function crearProductosSmartwatches($categoria, $marcas)
    {
        $productos = [
            [
                'nombre_producto' => 'Apple Watch Series 9',
                'descripcion' => 'Smartwatch con monitor cardíaco, GPS y pantalla Always-On Retina',
                'precio' => 400000,
                'stock' => 30,
                'estado' => 'nuevo',
                'activo' => true,
                'stock_reservado' => 8,
                'stock_disponible' => 22,
                'marca_id' => $marcas['Apple']->marca_id
            ],
            [
                'nombre_producto' => 'Samsung Galaxy Watch 6 Classic',
                'descripcion' => 'Smartwatch con bisel rotativo, monitor de salud avanzado y pantalla AMOLED',
                'precio' => 350000,
                'stock' => 25,
                'estado' => 'nuevo',
                'activo' => true,
                'stock_reservado' => 5,
                'stock_disponible' => 20,
                'marca_id' => $marcas['Samsung']->marca_id
            ],
            [
                'nombre_producto' => 'Xiaomi Mi Band 8 Pro',
                'descripcion' => 'Banda de fitness con pantalla AMOLED 1.74", monitor de sueño y 14 días de batería',
                'precio' => 80000,
                'stock' => 50,
                'estado' => 'nuevo',
                'activo' => true,
                'stock_reservado' => 10,
                'stock_disponible' => 40,
                'marca_id' => $marcas['Xiaomi']->marca_id
            ],
            [
                'nombre_producto' => 'Huawei Watch GT 4',
                'descripcion' => 'Smartwatch deportivo con GPS dual, monitor de entrenamiento y hasta 14 días de batería',
                'precio' => 300000,
                'stock' => 20,
                'estado' => 'nuevo',
                'activo' => true,
                'stock_reservado' => 3,
                'stock_disponible' => 17,
                'marca_id' => $marcas['Huawei']->marca_id
            ]
        ];

        foreach ($productos as $producto) {
            $producto['categoria_id'] = $categoria->categoria_id;
            Producto::create($producto);
        }

        $this->command->info("   ⌚ Smartwatches: " . count($productos) . " productos creados");
    }

    private function crearProductosAccesorios($categoria, $marcas)
    {
        $productos = [
            [
                'nombre_producto' => 'Apple Pencil 2',
                'descripcion' => 'Lápiz digital de precisión para iPad con carga inalámbrica y doble toque',
                'precio' => 130000,
                'stock' => 40,
                'estado' => 'nuevo',
                'activo' => true,
                'stock_reservado' => 8,
                'stock_disponible' => 32,
                'marca_id' => $marcas['Apple']->marca_id
            ],
            [
                'nombre_producto' => 'Samsung S Pen',
                'descripcion' => 'Lápiz digital para Galaxy Tab con 4096 niveles de presión y 0.7ms de latencia',
                'precio' => 90000,
                'stock' => 35,
                'estado' => 'nuevo',
                'activo' => true,
                'stock_reservado' => 7,
                'stock_disponible' => 28,
                'marca_id' => $marcas['Samsung']->marca_id
            ],
            [
                'nombre_producto' => 'Xiaomi Mi Wireless Charger',
                'descripcion' => 'Cargador inalámbrico de 20W con diseño elegante y LED indicador',
                'precio' => 60000,
                'stock' => 45,
                'estado' => 'nuevo',
                'activo' => true,
                'stock_reservado' => 9,
                'stock_disponible' => 36,
                'marca_id' => $marcas['Xiaomi']->marca_id
            ],
            [
                'nombre_producto' => 'Huawei SuperCharge',
                'descripcion' => 'Cargador rápido de 40W con tecnología SuperCharge y múltiples puertos',
                'precio' => 80000,
                'stock' => 30,
                'estado' => 'nuevo',
                'activo' => true,
                'stock_reservado' => 6,
                'stock_disponible' => 24,
                'marca_id' => $marcas['Huawei']->marca_id
            ]
        ];

        foreach ($productos as $producto) {
            $producto['categoria_id'] = $categoria->categoria_id;
            Producto::create($producto);
        }

        $this->command->info("   🔌 Accesorios: " . count($productos) . " productos creados");
    }

    private function crearProductosAudio($categoria, $marcas)
    {
        $productos = [
            [
                'nombre_producto' => 'Sony WH-1000XM5',
                'descripcion' => 'Auriculares inalámbricos con cancelación de ruido líder en la industria y 30h de batería',
                'precio' => 399999,
                'stock' => 20,
                'estado' => 'nuevo',
                'activo' => true,
                'stock_reservado' => 4,
                'stock_disponible' => 16,
                'marca_id' => $marcas['Sony']->marca_id
            ],
            [
                'nombre_producto' => 'Apple AirPods Pro 2',
                'descripcion' => 'Auriculares inalámbricos con cancelación de ruido activa y audio espacial',
                'precio' => 249999,
                'stock' => 35,
                'estado' => 'nuevo',
                'activo' => true,
                'stock_reservado' => 7,
                'stock_disponible' => 28,
                'marca_id' => $marcas['Apple']->marca_id
            ],
            [
                'nombre_producto' => 'Samsung Galaxy Buds2 Pro',
                'descripcion' => 'Auriculares true wireless con cancelación de ruido inteligente y audio Hi-Fi 24-bit',
                'precio' => 199999,
                'stock' => 28,
                'estado' => 'nuevo',
                'activo' => true,
                'stock_reservado' => 5,
                'stock_disponible' => 23,
                'marca_id' => $marcas['Samsung']->marca_id
            ],
            [
                'nombre_producto' => 'Sony SRS-XB43',
                'descripcion' => 'Altavoz Bluetooth portátil con EXTRA BASS, luces LED y hasta 24h de reproducción',
                'precio' => 199999,
                'stock' => 15,
                'estado' => 'nuevo',
                'activo' => true,
                'stock_reservado' => 2,
                'stock_disponible' => 13,
                'marca_id' => $marcas['Sony']->marca_id
            ]
        ];

        foreach ($productos as $producto) {
            $producto['categoria_id'] = $categoria->categoria_id;
            Producto::create($producto);
        }

        $this->command->info("   🎵 Audio: " . count($productos) . " productos creados");
    }
}
