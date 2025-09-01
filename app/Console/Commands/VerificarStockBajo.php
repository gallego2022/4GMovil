<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Producto;
use App\Mail\StockBajo;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class VerificarStockBajo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventario:verificar-stock-bajo {--send-emails : Enviar emails de alerta} {--stock-bajo=60 : Porcentaje para stock bajo} {--stock-critico=20 : Porcentaje para stock crítico}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verificar productos con stock bajo y enviar alertas';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $porcentajeStockBajo = (int) $this->option('stock-bajo');
        $porcentajeStockCritico = (int) $this->option('stock-critico');
        $enviarEmails = $this->option('send-emails');
        
        $this->info("🔍 Verificando productos con stock bajo...");
        $this->info("📊 Stock bajo configurado: {$porcentajeStockBajo}%");
        $this->info("🚨 Stock crítico configurado: {$porcentajeStockCritico}%");
        $this->info("📧 Enviar emails: " . ($enviarEmails ? 'SÍ' : 'NO'));
        
        // Buscar productos con stock bajo (por porcentaje)
        $productosStockBajo = $this->obtenerProductosStockBajo($porcentajeStockBajo, $porcentajeStockCritico);
        
        if ($productosStockBajo->isEmpty()) {
            $this->info("✅ No hay productos con stock bajo");
            return 0;
        }
        
        $this->warn("⚠️ Se encontraron {$productosStockBajo->count()} productos con stock bajo:");
        
        $tableData = [];
        foreach ($productosStockBajo as $producto) {
            $porcentajeActual = $this->calcularPorcentajeStock($producto);
            $estado = $this->determinarEstadoStock($porcentajeActual, $porcentajeStockBajo, $porcentajeStockCritico);
            $tableData[] = [
                $producto->producto_id,
                $producto->nombre_producto,
                $producto->stock,
                $porcentajeActual . '%',
                $estado,
                $producto->categoria->nombre_categoria ?? 'N/A',
                $producto->marca->nombre_marca ?? 'N/A'
            ];
            
            $this->line("   • {$producto->nombre_producto}: {$producto->stock} unidades ({$porcentajeActual}% del máximo) - {$estado}");
        }
        
        // Mostrar tabla de productos
        $this->table(
            ['ID', 'Producto', 'Stock', 'Porcentaje', 'Estado', 'Categoría', 'Marca'],
            $tableData
        );
        
        // Enviar emails si está habilitado
        if ($enviarEmails) {
            $this->info("\n📧 Enviando alertas por email...");
            $this->enviarAlertas($productosStockBajo, $porcentajeStockBajo, $porcentajeStockCritico);
        }
        
        // Registrar en logs
        Log::info('Verificación de stock bajo completada', [
            'productos_encontrados' => $productosStockBajo->count(),
            'porcentaje_stock_bajo' => $porcentajeStockBajo,
            'porcentaje_stock_critico' => $porcentajeStockCritico,
            'emails_enviados' => $enviarEmails
        ]);
        
        $this->info("\n✅ Verificación completada. Se encontraron {$productosStockBajo->count()} productos con stock bajo.");
        
        return 0;
    }
    
    /**
     * Enviar alertas por email para productos con stock bajo
     */
    private function enviarAlertas($productos, $porcentajeStockBajo, $porcentajeStockCritico): void
    {
        $emailsDestino = $this->obtenerEmailsDestino();
        
        if (empty($emailsDestino)) {
            $this->warn("⚠️ No hay emails configurados para alertas");
            return;
        }
        
        $emailsEnviados = 0;
        
        foreach ($productos as $producto) {
            try {
                foreach ($emailsDestino as $email) {
                    $porcentajeActual = $this->calcularPorcentajeStock($producto);
                    $estado = $this->determinarEstadoStock($porcentajeActual, $porcentajeStockBajo, $porcentajeStockCritico);
                    Mail::to($email)->send(new StockBajo($producto, $porcentajeActual, $estado));
                    $emailsEnviados++;
                    
                    $this->line("   ✅ Email enviado a {$email} para {$producto->nombre_producto} ({$porcentajeActual}%) - {$estado}");
                }
                
                // Pequeña pausa para evitar sobrecarga del servidor de email
                sleep(1);
                
            } catch (\Exception $e) {
                $this->error("❌ Error enviando email para {$producto->nombre_producto}: " . $e->getMessage());
                Log::error('Error enviando alerta de stock bajo', [
                    'producto_id' => $producto->producto_id,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        $this->info("📬 Total de emails enviados: {$emailsEnviados}");
    }
    
    /**
     * Obtener lista de emails para enviar alertas
     */
    private function obtenerEmailsDestino(): array
    {
        // Intentar obtener de la configuración
        $emails = config('inventory.stock_alerts.emails', []);
        
        // Si es un string, convertirlo a array
        if (is_string($emails)) {
            $emails = array_filter(array_map('trim', explode(',', $emails)));
        }
        
        // Si no hay configuración o está vacío, usar emails por defecto
        if (empty($emails)) {
            $emails = [
                'osmandavidgallego@gmail.com'
            ];
        }
        
        // Asegurar que sea un array
        if (!is_array($emails)) {
            $emails = [$emails];
        }
        
        // Filtrar emails válidos
        return array_filter($emails, function($email) {
            return filter_var($email, FILTER_VALIDATE_EMAIL);
        });
    }

    /**
     * Obtener productos con stock bajo basado en porcentaje del stock actual
     */
    private function obtenerProductosStockBajo(int $porcentajeStockBajo, int $porcentajeStockCritico): \Illuminate\Support\Collection
    {
        $productos = Producto::all();
        $productosStockBajo = collect();

        foreach ($productos as $producto) {
            // Calcular el stock mínimo basado en el stock actual
            $stockMinimo = ($producto->stock * $porcentajeStockBajo) / 100;
            
            // Si el stock actual está por debajo del mínimo, agregar a la lista
            if ($producto->stock < $stockMinimo) {
                $productosStockBajo->push($producto);
            }
        }

        return $productosStockBajo;
    }

    /**
     * Calcular el porcentaje de stock actual
     */
    private function calcularPorcentajeStock(Producto $producto): float
    {
        // El stock actual es siempre el 100%
        // No necesitamos stock_maximo, solo el stock actual
        return 100.0;
    }

    /**
     * Determinar el estado del stock basado en porcentaje del stock actual
     */
    private function determinarEstadoStock(float $porcentajeActual, int $porcentajeStockBajo, int $porcentajeStockCritico): string
    {
        // El stock actual siempre es 100%
        // Calculamos el estado basado en los porcentajes configurados
        if ($porcentajeStockBajo <= 60 && $porcentajeStockCritico <= 20) {
            return '⚠️ BAJO';
        } elseif ($porcentajeStockBajo <= 20) {
            return '🚨 CRÍTICO';
        } else {
            return '✅ NORMAL';
        }
    }
}
