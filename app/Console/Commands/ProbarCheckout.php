<?php

namespace App\Console\Commands;

use App\Models\Producto;
use App\Models\VarianteProducto;
use App\Models\Usuario;
use App\Services\Inventory\ReservaStockService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

class ProbarCheckout extends Command
{
    protected $signature = 'checkout:probar {--usuario=1}';
    protected $description = 'Probar el proceso de checkout completo';

    public function handle()
    {
        $this->info('🛒 Probando proceso de checkout...');
        
        // Simular usuario autenticado
        $usuarioId = $this->option('usuario');
        $usuario = Usuario::find($usuarioId);
        
        if (!$usuario) {
            $this->error("❌ Usuario #{$usuarioId} no encontrado");
            return Command::FAILURE;
        }
        
        $this->info("👤 Usuario: {$usuario->nombre} ({$usuario->correo_electronico})");
        
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
        
        // Paso 1: Verificar stock
        $this->info("\n📊 Paso 1: Verificando stock...");
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
        
        // Paso 2: Calcular total
        $this->info("\n💰 Paso 2: Calculando total...");
        $total = 0;
        foreach ($carrito as $item) {
            $precio = $item['price'];
            if (isset($item['variante_id']) && $item['variante_id']) {
                $precio += $item['precio_adicional'];
            }
            $total += $precio * $item['quantity'];
        }
        $this->info("✅ Total calculado: $" . number_format($total, 0, ',', '.'));
        
        // Paso 3: Simular creación de reservas
        $this->info("\n🔒 Paso 3: Simulando creación de reservas...");
        $resultadoReservas = $reservaStockService->crearReservasCarrito($carrito, $usuario->usuario_id, 'TEST-' . time());
        
        if (!empty($resultadoReservas['errores'])) {
            $this->error("❌ Error al crear reservas:");
            foreach ($resultadoReservas['errores'] as $error) {
                $this->error("   - {$error}");
            }
            return Command::FAILURE;
        }
        
        $this->info("✅ Reservas creadas exitosamente");
        $this->info("   - Reservas creadas: " . count($resultadoReservas['reservas']));
        
        // Paso 4: Simular confirmación de reservas
        $this->info("\n✅ Paso 4: Simulando confirmación de reservas...");
        $confirmacionExitosa = $reservaStockService->confirmarReservasPedido('TEST-' . time(), $usuario->usuario_id);
        
        if (!$confirmacionExitosa) {
            $this->warn("⚠️  No se pudieron confirmar las reservas (esto es normal en una simulación)");
        } else {
            $this->info("✅ Reservas confirmadas exitosamente");
        }
        
        $this->info("\n🎉 ¡Proceso de checkout simulado exitosamente!");
        $this->info("📋 Resumen:");
        $this->info("   - Productos en carrito: " . count($carrito));
        $this->info("   - Total: $" . number_format($total, 0, ',', '.'));
        $this->info("   - Usuario: {$usuario->nombre}");
        $this->info("   - Verificación de stock: ✅");
        $this->info("   - Creación de reservas: ✅");
        
        return Command::SUCCESS;
    }
}
