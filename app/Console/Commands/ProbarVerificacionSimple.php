<?php

namespace App\Console\Commands;

use App\Models\Producto;
use App\Models\VarianteProducto;
use App\Services\ReservaStockService;
use Illuminate\Console\Command;

class ProbarVerificacionSimple extends Command
{
    protected $signature = 'stock:probar-simple';
    protected $description = 'Probar solo la verificación de stock sin crear reservas';

    public function handle()
    {
        $this->info('🧪 Probando verificación de stock simple...');
        
        // Crear carrito de prueba con productos que tienen stock
        $carrito = [];
        
        // Buscar productos con stock disponible
        $productosConStock = Producto::where('stock_disponible', '>', 0)
            ->where('activo', true)
            ->take(2)
            ->get();
            
        $variantesConStock = VarianteProducto::where('stock_disponible', '>', 0)
            ->where('disponible', true)
            ->with('producto')
            ->take(2)
            ->get();
        
        $this->info("📦 Productos con stock: {$productosConStock->count()}");
        $this->info("🎨 Variantes con stock: {$variantesConStock->count()}");
        
        // Agregar productos sin variantes
        foreach ($productosConStock as $producto) {
            $carrito[] = [
                'id' => $producto->producto_id,
                'name' => $producto->nombre_producto,
                'price' => $producto->precio,
                'quantity' => 1,
                'variante_id' => null
            ];
            $this->info("   ✅ Agregado: {$producto->nombre_producto} - Stock: {$producto->stock_disponible}");
        }
        
        // Agregar productos con variantes
        foreach ($variantesConStock as $variante) {
            $carrito[] = [
                'id' => $variante->producto->producto_id,
                'name' => $variante->producto->nombre_producto,
                'price' => $variante->producto->precio,
                'quantity' => 1,
                'variante_id' => $variante->variante_id,
                'variante_nombre' => $variante->nombre,
                'precio_adicional' => $variante->precio_adicional
            ];
            $this->info("   ✅ Agregado: {$variante->producto->nombre_producto} ({$variante->nombre}) - Stock: {$variante->stock_disponible}");
        }
        
        if (empty($carrito)) {
            $this->error("❌ No hay productos con stock disponible para probar");
            return Command::FAILURE;
        }
        
        $this->info("🛒 Carrito creado con " . count($carrito) . " items");
        
        // Verificar stock
        $this->info("\n📊 Verificando stock...");
        $reservaStockService = new ReservaStockService();
        $verificacionStock = $reservaStockService->verificarStockCarrito($carrito);
        
        if (!$verificacionStock['disponible']) {
            $this->error("❌ Verificación de stock falló:");
            foreach ($verificacionStock['errores'] as $error) {
                $this->error("   - {$error}");
            }
            return Command::FAILURE;
        }
        
        $this->info("✅ Verificación de stock exitosa");
        
        // Calcular total
        $this->info("\n💰 Calculando total...");
        $total = 0;
        foreach ($carrito as $item) {
            $precio = $item['price'];
            if (isset($item['variante_id']) && $item['variante_id']) {
                $precio += $item['precio_adicional'];
            }
            $total += $precio * $item['quantity'];
        }
        $this->info("✅ Total calculado: $" . number_format($total, 0, ',', '.'));
        
        $this->info("\n🎉 ¡Verificación simple completada exitosamente!");
        $this->info("📋 Resumen:");
        $this->info("   - Productos en carrito: " . count($carrito));
        $this->info("   - Total: $" . number_format($total, 0, ',', '.'));
        $this->info("   - Verificación de stock: ✅");
        
        return Command::SUCCESS;
    }
}
