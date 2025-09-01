<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Producto;
use App\Models\Usuario;
use App\Notifications\StockBajoNotification;
use Illuminate\Support\Facades\Log;

class VerificarStockCommand extends Command
{
    protected $signature = 'inventario:verificar-stock {--notificar : Enviar notificaciones por email}';
    protected $description = 'Verificar productos con stock bajo y enviar notificaciones';

    public function handle()
    {
        $this->info('🔍 Verificando stock de productos...');
        
        $enviarNotificaciones = $this->option('notificar');
        
        // Obtener productos con stock crítico
        $productosCriticos = Producto::stockCritico()->activos()->get();
        $this->info("📊 Productos con stock crítico: {$productosCriticos->count()}");
        
        // Obtener productos con stock bajo
        $productosBajos = Producto::stockBajo()->activos()->get();
        $this->info("📊 Productos con stock bajo: {$productosBajos->count()}");
        
        // Obtener productos sin stock
        $productosSinStock = Producto::sinStock()->activos()->get();
        $this->info("📊 Productos sin stock: {$productosSinStock->count()}");
        
        if ($enviarNotificaciones) {
            $this->enviarNotificaciones($productosCriticos, $productosBajos, $productosSinStock);
        }
        
        // Mostrar resumen en consola
        $this->mostrarResumen($productosCriticos, $productosBajos, $productosSinStock);
        
        $this->info('✅ Verificación de stock completada.');
        
        return 0;
    }
    
    private function enviarNotificaciones($productosCriticos, $productosBajos, $productosSinStock)
    {
        $this->info('📧 Enviando notificaciones...');
        
        // Obtener administradores
        $administradores = Usuario::where('rol', 'admin')->where('estado', true)->get();
        
        if ($administradores->isEmpty()) {
            $this->warn('⚠️ No se encontraron administradores para enviar notificaciones.');
            return;
        }
        
        $notificacionesEnviadas = 0;
        
        // Notificar productos críticos
        foreach ($productosCriticos as $producto) {
            foreach ($administradores as $admin) {
                try {
                    $admin->notify(new StockBajoNotification($producto, 'critico'));
                    $notificacionesEnviadas++;
                    
                    Log::info('Notificación de stock crítico enviada', [
                        'admin_id' => $admin->usuario_id,
                        'producto_id' => $producto->producto_id,
                        'producto_nombre' => $producto->nombre_producto
                    ]);
                } catch (\Exception $e) {
                    Log::error('Error al enviar notificación de stock crítico', [
                        'admin_id' => $admin->usuario_id,
                        'producto_id' => $producto->producto_id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }
        
        // Notificar productos con stock bajo (solo si no están en crítico)
        foreach ($productosBajos as $producto) {
            if (!$productosCriticos->contains('producto_id', $producto->producto_id)) {
                foreach ($administradores as $admin) {
                    try {
                        $admin->notify(new StockBajoNotification($producto, 'bajo'));
                        $notificacionesEnviadas++;
                        
                        Log::info('Notificación de stock bajo enviada', [
                            'admin_id' => $admin->usuario_id,
                            'producto_id' => $producto->producto_id,
                            'producto_nombre' => $producto->nombre_producto
                        ]);
                    } catch (\Exception $e) {
                        Log::error('Error al enviar notificación de stock bajo', [
                            'admin_id' => $admin->usuario_id,
                            'producto_id' => $producto->producto_id,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }
        }
        
        $this->info("📧 Notificaciones enviadas: {$notificacionesEnviadas}");
    }
    
    private function mostrarResumen($productosCriticos, $productosBajos, $productosSinStock)
    {
        $this->newLine();
        $this->info('📋 RESUMEN DE ALERTAS DE STOCK');
        $this->info('================================');
        
        if ($productosCriticos->isNotEmpty()) {
            $this->error('🚨 PRODUCTOS CON STOCK CRÍTICO:');
            foreach ($productosCriticos as $producto) {
                $this->line("   • {$producto->nombre_producto} (ID: {$producto->producto_id})");
                $this->line("     Stock actual: {$producto->stock} | Mínimo: {$producto->stock_minimo}");
            }
            $this->newLine();
        }
        
        if ($productosBajos->isNotEmpty()) {
            $this->warn('⚠️ PRODUCTOS CON STOCK BAJO:');
            foreach ($productosBajos as $producto) {
                if (!$productosCriticos->contains('producto_id', $producto->producto_id)) {
                    $this->line("   • {$producto->nombre_producto} (ID: {$producto->producto_id})");
                    $this->line("     Stock actual: {$producto->stock} | Mínimo: {$producto->stock_minimo}");
                }
            }
            $this->newLine();
        }
        
        if ($productosSinStock->isNotEmpty()) {
            $this->error('❌ PRODUCTOS SIN STOCK:');
            foreach ($productosSinStock as $producto) {
                $this->line("   • {$producto->nombre_producto} (ID: {$producto->producto_id})");
            }
            $this->newLine();
        }
        
        if ($productosCriticos->isEmpty() && $productosBajos->isEmpty() && $productosSinStock->isEmpty()) {
            $this->info('✅ No hay alertas de stock pendientes.');
        }
    }
} 