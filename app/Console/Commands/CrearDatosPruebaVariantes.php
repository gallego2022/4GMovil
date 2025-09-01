<?php

namespace App\Console\Commands;

use App\Models\Producto;
use App\Models\VarianteProducto;
use Illuminate\Console\Command;

class CrearDatosPruebaVariantes extends Command
{
    protected $signature = 'variantes:crear-prueba';
    protected $description = 'Crear datos de prueba para variantes con diferentes niveles de stock';

    public function handle()
    {
        $this->info('🎯 Creando datos de prueba para variantes...');

        // Buscar un producto existente
        $producto = Producto::first();
        
        if (!$producto) {
            $this->error('❌ No hay productos disponibles. Crea un producto primero.');
            return Command::FAILURE;
        }

        $this->info("📦 Usando producto: {$producto->nombre_producto}");

        // Crear variantes con diferentes niveles de stock
        $variantes = [
            [
                'nombre' => 'Rojo',
                'codigo_color' => '#FF0000',
                'precio_adicional' => 50000,
                'stock_disponible' => 0, // Agotado
                'stock_minimo' => 10,
                'descripcion' => 'Variante agotada para pruebas'
            ],
            [
                'nombre' => 'Azul',
                'codigo_color' => '#0000FF',
                'precio_adicional' => 30000,
                'stock_disponible' => 2, // Crítico (20% del mínimo)
                'stock_minimo' => 10,
                'descripcion' => 'Variante con stock crítico'
            ],
            [
                'nombre' => 'Verde',
                'codigo_color' => '#00FF00',
                'precio_adicional' => 40000,
                'stock_disponible' => 5, // Bajo (50% del mínimo)
                'stock_minimo' => 10,
                'descripcion' => 'Variante con stock bajo'
            ],
            [
                'nombre' => 'Negro',
                'codigo_color' => '#000000',
                'precio_adicional' => 20000,
                'stock_disponible' => 15, // Normal
                'stock_minimo' => 10,
                'descripcion' => 'Variante con stock normal'
            ]
        ];

        $creadas = 0;
        foreach ($variantes as $datos) {
            try {
                $variante = VarianteProducto::create([
                    'producto_id' => $producto->producto_id,
                    'nombre' => $datos['nombre'],
                    'codigo_color' => $datos['codigo_color'],
                    'precio_adicional' => $datos['precio_adicional'],
                    'stock_disponible' => $datos['stock_disponible'],
                    'stock_minimo' => $datos['stock_minimo'],
                    'disponible' => true,
                    'descripcion' => $datos['descripcion']
                ]);

                $porcentaje = $datos['stock_minimo'] > 0 
                    ? round(($datos['stock_disponible'] / $datos['stock_minimo']) * 100, 1) 
                    : 0;

                $this->info("✅ Variante creada: {$variante->nombre} - Stock: {$datos['stock_disponible']} ({$porcentaje}% del mínimo)");
                $creadas++;

            } catch (\Exception $e) {
                $this->error("❌ Error creando variante {$datos['nombre']}: " . $e->getMessage());
            }
        }

        $this->info("🎉 Se crearon {$creadas} variantes de prueba.");
        $this->info("💡 Ahora puedes probar los comandos:");
        $this->info("   - php artisan variantes:verificar-alertas --tipo=agotado");
        $this->info("   - php artisan variantes:verificar-alertas --tipo=critico");
        $this->info("   - php artisan variantes:verificar-alertas --tipo=bajo");
        $this->info("   - php artisan variantes:verificar-alertas");

        return Command::SUCCESS;
    }
}
