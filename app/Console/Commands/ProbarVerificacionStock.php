<?php

namespace App\Console\Commands;

use App\Models\Producto;
use App\Models\VarianteProducto;
use App\Services\ReservaStockService;
use Illuminate\Console\Command;

class ProbarVerificacionStock extends Command
{
    protected $signature = 'stock:probar-verificacion';
    protected $description = 'Probar la verificación de stock para productos y variantes';

    public function handle()
    {
        $this->info('🧪 Probando verificación de stock...');
        
        // Obtener productos y variantes de prueba
        $productos = Producto::take(3)->get();
        $variantes = VarianteProducto::with('producto')->take(3)->get();
        
        $this->info("📦 Productos encontrados: {$productos->count()}");
        $this->info("🎨 Variantes encontradas: {$variantes->count()}");
        
        // Crear carrito de prueba
        $carrito = [];
        
        // Agregar productos sin variantes
        foreach ($productos as $producto) {
            $carrito[] = [
                'id' => $producto->producto_id,
                'name' => $producto->nombre_producto,
                'price' => $producto->precio,
                'quantity' => 1,
                'variante_id' => null
            ];
        }
        
        // Agregar productos con variantes
        foreach ($variantes as $variante) {
            $carrito[] = [
                'id' => $variante->producto->producto_id,
                'name' => $variante->producto->nombre_producto,
                'price' => $variante->producto->precio,
                'quantity' => 1,
                'variante_id' => $variante->variante_id,
                'variante_nombre' => $variante->nombre,
                'precio_adicional' => $variante->precio_adicional
            ];
        }
        
        $this->info("🛒 Carrito de prueba creado con " . count($carrito) . " items");
        
        // Probar verificación de stock
        $reservaStockService = new ReservaStockService();
        $resultado = $reservaStockService->verificarStockCarrito($carrito);
        
        $this->info("📊 Resultado de verificación:");
        $this->info("   - Disponible: " . ($resultado['disponible'] ? '✅ Sí' : '❌ No'));
        $this->info("   - Errores: " . count($resultado['errores']));
        $this->info("   - Productos verificados: " . count($resultado['productos_info']));
        
        if (!empty($resultado['errores'])) {
            $this->warn("⚠️  Errores encontrados:");
            foreach ($resultado['errores'] as $error) {
                $this->warn("   - {$error}");
            }
        }
        
        $this->info("📋 Información de productos:");
        foreach ($resultado['productos_info'] as $info) {
            $status = $info['disponible'] ? '✅' : '❌';
            $this->info("   {$status} {$info['nombre']} - Stock: {$info['stock_disponible']}, Solicitado: {$info['cantidad_solicitada']}");
        }
        
        // Probar métodos individuales
        $this->info("\n🔍 Probando métodos individuales:");
        
        foreach ($productos as $producto) {
            $tieneStock = $producto->tieneStockSuficiente(1);
            $status = $tieneStock ? '✅' : '❌';
            $this->info("   {$status} Producto '{$producto->nombre_producto}' - Stock: {$producto->stock_disponible}, tieneStockSuficiente(1): " . ($tieneStock ? 'Sí' : 'No'));
        }
        
        foreach ($variantes as $variante) {
            $tieneStock = $variante->tieneStockSuficiente(1);
            $status = $tieneStock ? '✅' : '❌';
            $this->info("   {$status} Variante '{$variante->nombre}' - Stock: {$variante->stock_disponible}, tieneStockSuficiente(1): " . ($tieneStock ? 'Sí' : 'No'));
        }
        
        $this->info("\n🎉 Prueba completada!");
        
        return Command::SUCCESS;
    }
}
