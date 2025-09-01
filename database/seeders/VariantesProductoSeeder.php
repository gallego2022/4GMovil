<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Producto;
use App\Models\VarianteProducto;

class VariantesProductoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener todos los productos
        $productos = Producto::all();
        
        foreach ($productos as $producto) {
            // Crear variantes de colores para cada producto
            $variantes = [
                [
                    'nombre' => 'Negro',
                    'codigo_color' => '#000000',
                    'stock' => rand(5, 20),
                    'disponible' => true,
                    'precio_adicional' => 0,
                    'descripcion' => 'Color elegante y versátil que combina con cualquier estilo.',
                    'orden' => 1
                ],
                [
                    'nombre' => 'Blanco',
                    'codigo_color' => '#FFFFFF',
                    'stock' => rand(3, 15),
                    'disponible' => true,
                    'precio_adicional' => 0,
                    'descripcion' => 'Color limpio y moderno, perfecto para un look minimalista.',
                    'orden' => 2
                ],
                [
                    'nombre' => 'Azul',
                    'codigo_color' => '#3B82F6',
                    'stock' => rand(2, 12),
                    'disponible' => true,
                    'precio_adicional' => rand(0, 50000),
                    'descripcion' => 'Color vibrante y llamativo, ideal para destacar.',
                    'orden' => 3
                ],
                [
                    'nombre' => 'Rojo',
                    'codigo_color' => '#EF4444',
                    'stock' => 0,
                    'disponible' => false,
                    'precio_adicional' => 0,
                    'descripcion' => 'Color audaz y energético, para personalidades únicas.',
                    'orden' => 4
                ],
                [
                    'nombre' => 'Verde',
                    'codigo_color' => '#10B981',
                    'stock' => rand(1, 8),
                    'disponible' => true,
                    'precio_adicional' => rand(0, 30000),
                    'descripcion' => 'Color fresco y natural, transmite tranquilidad.',
                    'orden' => 5
                ],
                [
                    'nombre' => 'Dorado',
                    'codigo_color' => '#F59E0B',
                    'stock' => rand(1, 5),
                    'disponible' => true,
                    'precio_adicional' => rand(50000, 100000),
                    'descripcion' => 'Color premium y sofisticado, para quienes buscan exclusividad.',
                    'orden' => 6
                ]
            ];
            
            foreach ($variantes as $variante) {
                VarianteProducto::create([
                    'producto_id' => $producto->producto_id,
                    'nombre' => $variante['nombre'],
                    'codigo_color' => $variante['codigo_color'],
                    'stock' => $variante['stock'],
                    'disponible' => $variante['disponible'],
                    'precio_adicional' => $variante['precio_adicional'],
                    'descripcion' => $variante['descripcion'],
                    'orden' => $variante['orden']
                ]);
            }
        }
    }
}
