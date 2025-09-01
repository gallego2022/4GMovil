<?php

namespace App\Observers;

use App\Models\Producto;
use App\Mail\StockBajo;
use App\Jobs\ProcesarAlertaStockBajo;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ProductoObserver
{
    /**
     * Handle the Producto "created" event.
     */
    public function created(Producto $producto): void
    {
        // Verificar stock bajo al crear un producto
        $this->verificarStockBajo($producto, 'creado');
    }

    /**
     * Handle the Producto "updated" event.
     */
    public function updated(Producto $producto): void
    {
        // Verificar si el stock cambió
        if ($producto->wasChanged('stock')) {
            $stockAnterior = $producto->getOriginal('stock');
            $stockNuevo = $producto->stock;
            
            Log::info('Stock de producto actualizado', [
                'producto_id' => $producto->producto_id,
                'producto_nombre' => $producto->nombre_producto,
                'stock_anterior' => $stockAnterior,
                'stock_nuevo' => $stockNuevo
            ]);
            
            // Verificar stock bajo después de la actualización
            $this->verificarStockBajo($producto, 'actualizado');
        }
    }

    /**
     * Handle the Producto "deleted" event.
     */
    public function deleted(Producto $producto): void
    {
        Log::info('Producto eliminado', [
            'producto_id' => $producto->producto_id,
            'producto_nombre' => $producto->nombre_producto
        ]);
    }

    /**
     * Handle the Producto "restored" event.
     */
    public function restored(Producto $producto): void
    {
        // Verificar stock bajo al restaurar un producto
        $this->verificarStockBajo($producto, 'restaurado');
    }

    /**
     * Handle the Producto "force deleted" event.
     */
    public function forceDeleted(Producto $producto): void
    {
        Log::info('Producto eliminado permanentemente', [
            'producto_id' => $producto->producto_id,
            'producto_nombre' => $producto->nombre_producto
        ]);
    }

    /**
     * Verificar si el producto tiene stock bajo y enviar alerta
     */
    private function verificarStockBajo(Producto $producto, string $accion): void
    {
        try {
            // Obtener el stock mínimo configurado
            $stockMinimo = $producto->stock_minimo ?? config('inventory.stock_alerts.default_min_stock', 10);
            
            // Verificar si el stock está bajo (por unidades)
            if ($producto->stock <= $stockMinimo) {
                $porcentajeActual = $this->calcularPorcentajeStock($producto);
                
                Log::warning('Stock bajo detectado', [
                    'producto_id' => $producto->producto_id,
                    'producto_nombre' => $producto->nombre_producto,
                    'stock_actual' => $producto->stock,
                    'stock_minimo' => $stockMinimo,
                    'porcentaje_actual' => $porcentajeActual,
                    'accion' => $accion
                ]);
                
                // Enviar alerta de stock bajo usando Job en segundo plano
                ProcesarAlertaStockBajo::dispatch($producto, $porcentajeActual, $stockMinimo);
            }
            
        } catch (\Exception $e) {
            Log::error('Error al verificar stock bajo', [
                'producto_id' => $producto->producto_id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Enviar alerta de stock bajo por email
     */
    private function enviarAlertaStockBajo(Producto $producto, int $stockActual, int $stockMinimo): void
    {
        try {
            // Lista de emails para enviar alertas (puedes configurarlo en .env)
            $emailsDestino = $this->obtenerEmailsDestino();
            
            if (empty($emailsDestino)) {
                Log::warning('No hay emails configurados para alertas de stock bajo');
                return;
            }
            
            // Enviar email a cada destinatario
            foreach ($emailsDestino as $email) {
                Mail::to($email)->send(new StockBajo($producto, $stockActual, $stockMinimo));
                
                Log::info('Alerta de stock bajo enviada', [
                    'producto_id' => $producto->producto_id,
                    'producto_nombre' => $producto->nombre_producto,
                    'email_destino' => $email,
                    'stock_actual' => $stockActual,
                    'stock_minimo' => $stockMinimo
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('Error al enviar alerta de stock bajo', [
                'producto_id' => $producto->producto_id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener lista de emails para enviar alertas
     */
    private function obtenerEmailsDestino(): array
    {
        // Puedes configurar estos emails en tu archivo .env
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
     * Calcular el porcentaje de stock actual
     */
    private function calcularPorcentajeStock(Producto $producto): float
    {
        // Obtener stock máximo del producto o usar el valor por defecto
        $stockMaximo = $producto->stock_maximo ?? config('inventory.stock_alerts.default_max_stock', 100);
        
        if ($stockMaximo <= 0) {
            return 0;
        }

        return round(($producto->stock / $stockMaximo) * 100, 2);
    }
}
