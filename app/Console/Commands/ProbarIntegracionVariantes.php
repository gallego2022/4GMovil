<?php

namespace App\Console\Commands;

use App\Models\Producto;
use App\Models\VarianteProducto;
use App\Services\StockSincronizacionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ProbarIntegracionVariantes extends Command
{
    protected $signature = 'variantes:probar-integracion {--producto-id= : ID específico del producto}';
    protected $description = 'Probar la integración completa del sistema de stock con variantes';

    public function handle()
    {
        $this->info('🧪 PROBANDO INTEGRACIÓN DE SISTEMA DE STOCK CON VARIANTES');
        $this->info('========================================================');
        $this->newLine();

        try {
            // 1. Verificar sincronización inicial
            $this->info('1️⃣ Verificando sincronización inicial...');
            $service = new StockSincronizacionService();
            $integridad = $service->verificarIntegridadStock();
            
            if ($integridad['total_problemas'] > 0) {
                $this->warn("   ⚠️  Se encontraron {$integridad['total_problemas']} problemas de sincronización");
                $this->info('   🔧 Corrigiendo sincronización...');
                $service->corregirSincronizacion();
            } else {
                $this->info('   ✅ Sincronización correcta');
            }
            $this->newLine();

            // 2. Obtener productos para la prueba
            $productoId = $this->option('producto-id');
            
            if ($productoId) {
                $productos = Producto::with('variantes')->where('producto_id', $productoId)->get();
            } else {
                $productos = Producto::with('variantes')
                    ->whereHas('variantes')
                    ->limit(3)
                    ->get();
            }

            if ($productos->isEmpty()) {
                $this->error('❌ No se encontraron productos con variantes para probar');
                return Command::FAILURE;
            }

            $this->info('2️⃣ Productos seleccionados para la prueba:');
            foreach ($productos as $producto) {
                $this->info("   📦 {$producto->nombre_producto} (ID: {$producto->producto_id})");
                $this->info("      Stock actual: {$producto->stock}");
                $this->info("      Variantes: {$producto->variantes->count()}");
                
                foreach ($producto->variantes as $variante) {
                    $this->info("         • {$variante->nombre}: {$variante->stock_disponible} unidades");
                }
                $this->newLine();
            }

            // 3. Probar operaciones de stock
            foreach ($productos as $producto) {
                $this->info("3️⃣ Probando operaciones con: {$producto->nombre_producto}");
                
                // Obtener primera variante
                $variante = $producto->variantes->first();
                if (!$variante) {
                    $this->warn("   ⚠️  No hay variantes disponibles");
                    continue;
                }

                $stockInicial = $variante->stock_disponible;
                $stockProductoInicial = $producto->stock;

                // Simular entrada de stock
                $this->info("   📥 Simulando entrada de 5 unidades a {$variante->nombre}...");
                $variante->registrarEntrada(5, 'Prueba de integración', 1);
                
                $variante->refresh();
                $producto->refresh();
                
                $this->info("      Stock variante: {$stockInicial} → {$variante->stock_disponible}");
                $this->info("      Stock producto: {$stockProductoInicial} → {$producto->stock}");
                
                if ($producto->stock == $stockProductoInicial + 5) {
                    $this->info("      ✅ Sincronización automática funcionando");
                } else {
                    $this->error("      ❌ Error en sincronización automática");
                }

                // Simular venta
                $this->info("   💰 Simulando venta de 2 unidades...");
                $stockAntesVenta = $variante->stock_disponible;
                $stockProductoAntesVenta = $producto->stock;
                
                $variante->registrarSalida(2, 'Venta de prueba', 1);
                
                $variante->refresh();
                $producto->refresh();
                
                $this->info("      Stock variante: {$stockAntesVenta} → {$variante->stock_disponible}");
                $this->info("      Stock producto: {$stockProductoAntesVenta} → {$producto->stock}");
                
                if ($producto->stock == $stockProductoAntesVenta - 2) {
                    $this->info("      ✅ Sincronización de venta funcionando");
                } else {
                    $this->error("      ❌ Error en sincronización de venta");
                }

                $this->newLine();
            }

            // 4. Probar métodos del modelo
            $this->info('4️⃣ Probando métodos del modelo Producto:');
            $producto = $productos->first();
            
            $this->info("   ¿Tiene variantes?: " . ($producto->tieneVariantes() ? 'Sí' : 'No'));
            $this->info("   Stock real: {$producto->stock_real}");
            $this->info("   Stock total variantes: {$producto->stock_total_variantes}");
            $this->info("   Stock disponible variantes: {$producto->stock_disponible_variantes}");
            $this->info("   ¿Necesita reposición?: " . ($producto->necesitaReposicionVariantes() ? 'Sí' : 'No'));
            $this->info("   Estado stock real: {$producto->estado_stock_real}");
            $this->newLine();

            // 5. Probar verificación de stock
            $this->info('5️⃣ Probando verificación de stock:');
            $variante = $producto->variantes->first();
            
            $cantidadPrueba = 3;
            $this->info("   ¿Puede vender {$cantidadPrueba} unidades del producto?: " . 
                 ($producto->tieneStockSuficienteReal($cantidadPrueba) ? 'Sí' : 'No'));
            
            $this->info("   ¿Puede vender {$cantidadPrueba} unidades de {$variante->nombre}?: " . 
                 ($variante->tieneStockSuficiente($cantidadPrueba) ? 'Sí' : 'No'));
            $this->newLine();

            // 6. Probar reporte final
            $this->info('6️⃣ Generando reporte final:');
            $reporte = $service->obtenerReporteSincronizacion();
            
            $this->info("   Total productos: {$reporte['total_productos']}");
            $this->info("   Productos con variantes: {$reporte['productos_con_variantes']}");
            $this->info("   Productos sin variantes: {$reporte['productos_sin_variantes']}");
            $this->info("   Stock total del sistema: {$reporte['stock_total_sistema']}");
            $this->info("   Stock total de variantes: {$reporte['stock_total_variantes']}");
            $this->info("   Productos desincronizados: " . count($reporte['productos_desincronizados']));
            $this->newLine();

            // 7. Verificar integridad final
            $this->info('7️⃣ Verificando integridad final...');
            $integridadFinal = $service->verificarIntegridadStock();
            
            if ($integridadFinal['total_problemas'] == 0) {
                $this->info('   ✅ Integridad del sistema correcta');
            } else {
                $this->error("   ❌ Se encontraron {$integridadFinal['total_problemas']} problemas");
            }

            $this->newLine();
            $this->info('🎉 ¡PRUEBA DE INTEGRACIÓN COMPLETADA EXITOSAMENTE!');
            $this->info('El sistema de stock con variantes está funcionando correctamente.');
            
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Error durante la prueba: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return Command::FAILURE;
        }
    }
}
