<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Producto;
use App\Models\VarianteProducto;

class VarianteProductoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar si ya existen variantes
        if (VarianteProducto::count() > 0) {
            $this->command->info('Las variantes ya existen, saltando...');
            return;
        }

        $this->command->info('🌱 Creando variantes de productos...');

        // Obtener todos los productos
        $productos = Producto::all();

        foreach ($productos as $producto) {
            $this->crearVariantesParaProducto($producto);
        }

        $this->command->info('✅ Variantes creadas exitosamente!');
    }

    private function crearVariantesParaProducto(Producto $producto): void
    {
        $variantes = $this->obtenerVariantesPorCategoria($producto);
        
        foreach ($variantes as $variante) {
            VarianteProducto::create([
                'producto_id' => $producto->producto_id,
                'nombre' => $variante['nombre'],
                'codigo_variante' => $this->generarCodigoVariante($producto, $variante),
                'stock' => $variante['stock'],
                'stock_disponible' => $variante['stock'],
                'stock_reservado' => 0,
                'precio_adicional' => $variante['precio_adicional'] ?? 0,
                'disponible' => true,
                'activo' => true,
                'peso' => $variante['peso'] ?? null,
                'dimensiones' => $variante['dimensiones'] ?? null,
                'notas' => $variante['notas'] ?? null,
            ]);
        }

        // Actualizar el stock total del producto
        $this->actualizarStockProducto($producto);
    }

    private function obtenerVariantesPorCategoria(Producto $producto): array
    {
        $categoriaNombre = $producto->categoria->nombre ?? '';
        
        switch ($categoriaNombre) {
            case 'Smartphones':
                return $this->getVariantesSmartphones();
            case 'Laptops':
                return $this->getVariantesLaptops();
            case 'Tablets':
                return $this->getVariantesTablets();
            case 'Auriculares':
                return $this->getVariantesAuriculares();
            case 'Bafles':
                return $this->getVariantesBafles();
            case 'Smartwatches':
                return $this->getVariantesSmartwatches();
            case 'Accesorios':
                return $this->getVariantesAccesorios();
            default:
                return $this->getVariantesGenericas();
        }
    }

    private function getVariantesSmartphones(): array
    {
        return [
            [
                'nombre' => '128GB - Negro',
                'stock' => 8,
                'precio_adicional' => 0,
                'peso' => 0.2,
                'dimensiones' => '15.5 x 7.5 x 0.8 cm'
            ],
            [
                'nombre' => '256GB - Negro',
                'stock' => 10,
                'precio_adicional' => 50000,
                'peso' => 0.2,
                'dimensiones' => '15.5 x 7.5 x 0.8 cm'
            ],
            [
                'nombre' => '512GB - Negro',
                'stock' => 7,
                'precio_adicional' => 100000,
                'peso' => 0.2,
                'dimensiones' => '15.5 x 7.5 x 0.8 cm'
            ],
            [
                'nombre' => '128GB - Blanco',
                'stock' => 6,
                'precio_adicional' => 0,
                'peso' => 0.2,
                'dimensiones' => '15.5 x 7.5 x 0.8 cm'
            ],
            [
                'nombre' => '256GB - Blanco',
                'stock' => 8,
                'precio_adicional' => 50000,
                'peso' => 0.2,
                'dimensiones' => '15.5 x 7.5 x 0.8 cm'
            ]
        ];
    }

    private function getVariantesLaptops(): array
    {
        return [
            [
                'nombre' => '8GB RAM - 256GB SSD',
                'stock' => 5,
                'precio_adicional' => 0,
                'peso' => 1.5,
                'dimensiones' => '32.5 x 22.5 x 1.8 cm'
            ],
            [
                'nombre' => '16GB RAM - 512GB SSD',
                'stock' => 8,
                'precio_adicional' => 150000,
                'peso' => 1.5,
                'dimensiones' => '32.5 x 22.5 x 1.8 cm'
            ],
            [
                'nombre' => '32GB RAM - 1TB SSD',
                'stock' => 3,
                'precio_adicional' => 300000,
                'peso' => 1.5,
                'dimensiones' => '32.5 x 22.5 x 1.8 cm'
            ]
        ];
    }

    private function getVariantesTablets(): array
    {
        return [
            [
                'nombre' => '64GB - WiFi',
                'stock' => 6,
                'precio_adicional' => 0,
                'peso' => 0.5,
                'dimensiones' => '24.5 x 16.9 x 0.7 cm'
            ],
            [
                'nombre' => '128GB - WiFi',
                'stock' => 8,
                'precio_adicional' => 80000,
                'peso' => 0.5,
                'dimensiones' => '24.5 x 16.9 x 0.7 cm'
            ],
            [
                'nombre' => '256GB - WiFi + Cellular',
                'stock' => 4,
                'precio_adicional' => 150000,
                'peso' => 0.5,
                'dimensiones' => '24.5 x 16.9 x 0.7 cm'
            ]
        ];
    }

    private function getVariantesAuriculares(): array
    {
        return [
            [
                'nombre' => 'Negro',
                'stock' => 12,
                'precio_adicional' => 0,
                'peso' => 0.25,
                'dimensiones' => '18 x 8 x 3 cm'
            ],
            [
                'nombre' => 'Blanco',
                'stock' => 10,
                'precio_adicional' => 0,
                'peso' => 0.25,
                'dimensiones' => '18 x 8 x 3 cm'
            ],
            [
                'nombre' => 'Azul',
                'stock' => 6,
                'precio_adicional' => 0,
                'peso' => 0.25,
                'dimensiones' => '18 x 8 x 3 cm'
            ]
        ];
    }

    private function getVariantesBafles(): array
    {
        return [
            [
                'nombre' => 'Negro',
                'stock' => 8,
                'precio_adicional' => 0,
                'peso' => 1.2,
                'dimensiones' => '25 x 15 x 12 cm'
            ],
            [
                'nombre' => 'Blanco',
                'stock' => 6,
                'precio_adicional' => 0,
                'peso' => 1.2,
                'dimensiones' => '25 x 15 x 12 cm'
            ],
            [
                'nombre' => 'Rojo',
                'stock' => 4,
                'precio_adicional' => 10000,
                'peso' => 1.2,
                'dimensiones' => '25 x 15 x 12 cm'
            ]
        ];
    }

    private function getVariantesSmartwatches(): array
    {
        return [
            [
                'nombre' => '40mm - Negro',
                'stock' => 8,
                'precio_adicional' => 0,
                'peso' => 0.3,
                'dimensiones' => '4 x 3.4 x 1.1 cm'
            ],
            [
                'nombre' => '44mm - Negro',
                'stock' => 10,
                'precio_adicional' => 50000,
                'peso' => 0.35,
                'dimensiones' => '4.4 x 3.6 x 1.2 cm'
            ],
            [
                'nombre' => '40mm - Plata',
                'stock' => 6,
                'precio_adicional' => 0,
                'peso' => 0.3,
                'dimensiones' => '4 x 3.4 x 1.1 cm'
            ],
            [
                'nombre' => '44mm - Plata',
                'stock' => 8,
                'precio_adicional' => 50000,
                'peso' => 0.35,
                'dimensiones' => '4.4 x 3.6 x 1.2 cm'
            ]
        ];
    }

    private function getVariantesAccesorios(): array
    {
        return [
            [
                'nombre' => 'Negro',
                'stock' => 15,
                'precio_adicional' => 0,
                'peso' => 0.1,
                'dimensiones' => '10 x 5 x 2 cm'
            ],
            [
                'nombre' => 'Blanco',
                'stock' => 12,
                'precio_adicional' => 0,
                'peso' => 0.1,
                'dimensiones' => '10 x 5 x 2 cm'
            ],
            [
                'nombre' => 'Transparente',
                'stock' => 8,
                'precio_adicional' => 0,
                'peso' => 0.1,
                'dimensiones' => '10 x 5 x 2 cm'
            ]
        ];
    }

    private function getVariantesGenericas(): array
    {
        return [
            [
                'nombre' => 'Estándar',
                'stock' => 10,
                'precio_adicional' => 0,
                'peso' => 0.5,
                'dimensiones' => '15 x 10 x 5 cm'
            ]
        ];
    }

    private function generarCodigoVariante(Producto $producto, array $variante): string
    {
        $codigoBase = strtoupper(substr($producto->nombre_producto, 0, 3));
        $codigoVariante = strtoupper(substr($variante['nombre'], 0, 3));
        $numero = rand(100, 999);
        
        return "{$codigoBase}-{$codigoVariante}-{$numero}";
    }

    private function actualizarStockProducto(Producto $producto): void
    {
        $stockTotal = $producto->variantes()->sum('stock');
        $stockDisponible = $producto->variantes()->sum('stock_disponible');
        
        $producto->update([
            'stock' => $stockTotal,
            'stock_disponible' => $stockDisponible
        ]);
    }
}
