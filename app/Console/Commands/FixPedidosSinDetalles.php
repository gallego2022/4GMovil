<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pedido;
use App\Models\DetallePedido;
use App\Models\Producto;

class FixPedidosSinDetalles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pedidos:fix-sin-detalles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Corrige pedidos que no tienen detalles agregando productos de prueba';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Buscando pedidos sin detalles...');
        
        // Obtener pedidos que no tienen detalles
        $pedidosSinDetalles = Pedido::whereDoesntHave('detalles')->get();
        
        if ($pedidosSinDetalles->isEmpty()) {
            $this->info('✅ Todos los pedidos tienen detalles. No hay nada que corregir.');
            return 0;
        }
        
        $this->warn("Se encontraron {$pedidosSinDetalles->count()} pedidos sin detalles.");
        
        // Obtener productos disponibles
        $productos = Producto::where('activo', true)->take(5)->get();
        
        if ($productos->isEmpty()) {
            $this->error('❌ No hay productos disponibles para agregar a los pedidos.');
            return 1;
        }
        
        $this->info("Usando {$productos->count()} productos para agregar detalles.");
        
        $contador = 0;
        
        foreach ($pedidosSinDetalles as $pedido) {
            $this->info("Procesando pedido #{$pedido->pedido_id}...");
            
            // Agregar 2-3 productos al pedido
            $cantidadProductos = rand(2, 3);
            $productosSeleccionados = $productos->random($cantidadProductos);
            
            foreach ($productosSeleccionados as $producto) {
                $cantidad = rand(1, 2);
                $precio = $producto->precio;
                
                DetallePedido::create([
                    'pedido_id' => $pedido->pedido_id,
                    'producto_id' => $producto->producto_id,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precio
                ]);
                
                $this->line("  - Agregado: {$producto->nombre_producto} (x{$cantidad}) - $" . number_format($precio, 0, ',', '.'));
            }
            
            $contador++;
        }
        
        $this->info("✅ Se corrigieron {$contador} pedidos exitosamente.");
        return 0;
    }
}
