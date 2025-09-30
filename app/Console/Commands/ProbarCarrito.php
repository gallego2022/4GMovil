<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Usuario;
use App\Models\Producto;
use App\Models\VarianteProducto;
use App\Models\Carrito;
use App\Models\CarritoItem;
use App\Services\Business\CarritoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProbarCarrito extends Command
{
    protected $signature = 'carrito:probar {--usuario-id=1} {--producto-id=1}';
    protected $description = 'Prueba el funcionamiento del carrito de compras';

    public function handle(): int
    {
        $this->info('🛒 Iniciando prueba del carrito de compras...');
        
        try {
            // Obtener parámetros
            $usuarioId = $this->option('usuario-id');
            $productoId = $this->option('producto-id');
            
            // Verificar que el usuario existe
            $usuario = Usuario::find($usuarioId);
            if (!$usuario) {
                $this->error("❌ Usuario con ID {$usuarioId} no encontrado");
                return 1;
            }
            
            // Verificar que el producto existe
            $producto = Producto::find($productoId);
            if (!$producto) {
                $this->error("❌ Producto con ID {$productoId} no encontrado");
                return 1;
            }
            
            $this->info("✅ Usuario: {$usuario->nombre} (ID: {$usuario->id})");
            $this->info("✅ Producto: {$producto->nombre_producto} (ID: {$producto->id})");
            
            // Autenticar como el usuario
            Auth::login($usuario);
            $this->info("✅ Usuario autenticado correctamente");
            
            // Crear instancia del servicio
            $carritoService = app(CarritoService::class);
            
            // 1. Probar obtener carrito vacío
            $this->info("\n📋 1. Probando obtener carrito vacío...");
            $carritoVacio = $carritoService->getCart();
            $this->info("Resultado: " . json_encode($carritoVacio, JSON_PRETTY_PRINT));
            
            // 2. Probar agregar producto al carrito
            $this->info("\n➕ 2. Probando agregar producto al carrito...");
            $request = new Request([
                'producto_id' => $productoId,
                'variante_id' => null,
                'cantidad' => 2
            ]);
            
            $resultadoAgregar = $carritoService->addToCart($request);
            $this->info("Resultado agregar: " . json_encode($resultadoAgregar, JSON_PRETTY_PRINT));
            
            // 3. Verificar que se guardó en la base de datos
            $this->info("\n🔍 3. Verificando datos en la base de datos...");
            $carrito = Carrito::where('usuario_id', $usuarioId)->first();
            if ($carrito) {
                $this->info("✅ Carrito encontrado en BD (ID: {$carrito->id})");
                $items = $carrito->items;
                $this->info("Items en carrito: " . $items->count());
                foreach ($items as $item) {
                    $this->info("  - Producto ID: {$item->producto_id}, Cantidad: {$item->cantidad}");
                }
            } else {
                $this->error("❌ No se encontró carrito en la base de datos");
            }
            
            // 4. Probar obtener carrito con items
            $this->info("\n📋 4. Probando obtener carrito con items...");
            $carritoConItems = $carritoService->getCart();
            $this->info("Resultado: " . json_encode($carritoConItems, JSON_PRETTY_PRINT));
            
            // 5. Probar actualizar cantidad
            $this->info("\n🔄 5. Probando actualizar cantidad...");
            if ($carrito && $carrito->items->count() > 0) {
                $item = $carrito->items->first();
                $requestUpdate = new Request(['cantidad' => 5]);
                $resultadoUpdate = $carritoService->updateCartItem($item->id, $requestUpdate);
                $this->info("Resultado actualizar: " . json_encode($resultadoUpdate, JSON_PRETTY_PRINT));
            }
            
            // 6. Probar eliminar item
            $this->info("\n🗑️ 6. Probando eliminar item...");
            if ($carrito && $carrito->items->count() > 0) {
                $item = $carrito->items->first();
                $resultadoEliminar = $carritoService->removeFromCart($item->id);
                $this->info("Resultado eliminar: " . json_encode($resultadoEliminar, JSON_PRETTY_PRINT));
            }
            
            // 7. Verificar estado final
            $this->info("\n🔍 7. Verificando estado final...");
            $carritoFinal = Carrito::where('usuario_id', $usuarioId)->first();
            if ($carritoFinal) {
                $this->info("Items restantes: " . $carritoFinal->items->count());
            }
            
            $this->info("\n✅ Prueba del carrito completada exitosamente");
            return 0;
            
        } catch (\Exception $e) {
            $this->error("❌ Error durante la prueba: " . $e->getMessage());
            $this->error("Stack trace: " . $e->getTraceAsString());
            return 1;
        }
    }
}