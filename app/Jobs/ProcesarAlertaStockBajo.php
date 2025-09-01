<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Producto;
use App\Mail\StockBajo;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ProcesarAlertaStockBajo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $producto;
    public $porcentajeActual;
    public $stockMinimo;
    public $tries = 3; // Intentar 3 veces si falla
    public $timeout = 60; // Timeout de 60 segundos

    /**
     * Create a new job instance.
     */
    public function __construct(Producto $producto, float $porcentajeActual, int $stockMinimo)
    {
        $this->producto = $producto;
        $this->porcentajeActual = $porcentajeActual;
        $this->stockMinimo = $stockMinimo;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info('Procesando alerta de stock bajo', [
                'producto_id' => $this->producto->producto_id,
                'producto_nombre' => $this->producto->nombre_producto,
                'porcentaje_actual' => $this->porcentajeActual,
                'stock_minimo' => $this->stockMinimo
            ]);

            // Verificar que el producto aún existe y tiene stock bajo
            $productoActual = Producto::find($this->producto->producto_id);
            
            if (!$productoActual) {
                Log::warning('Producto no encontrado al procesar alerta', [
                    'producto_id' => $this->producto->producto_id
                ]);
                return;
            }

            // Verificar si aún tiene stock bajo (por unidades)
            if ($productoActual->stock > $this->stockMinimo) {
                Log::info('Stock ya no está bajo, cancelando alerta', [
                    'producto_id' => $this->producto->producto_id,
                    'stock_actual' => $productoActual->stock
                ]);
                return;
            }

            // Enviar alertas por email
            $this->enviarAlertas();

            Log::info('Alerta de stock bajo procesada exitosamente', [
                'producto_id' => $this->producto->producto_id
            ]);

        } catch (\Exception $e) {
            Log::error('Error procesando alerta de stock bajo', [
                'producto_id' => $this->producto->producto_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Re-lanzar la excepción para que el job falle y se reintente
            throw $e;
        }
    }

    /**
     * Enviar alertas por email
     */
    private function enviarAlertas(): void
    {
        $emailsDestino = $this->obtenerEmailsDestino();
        
        if (empty($emailsDestino)) {
            Log::warning('No hay emails configurados para alertas de stock bajo');
            return;
        }

        foreach ($emailsDestino as $email) {
            try {
                Mail::to($email)->send(new StockBajo($this->producto, $this->porcentajeActual, $this->stockMinimo));
                
                Log::info('Alerta de stock bajo enviada', [
                    'producto_id' => $this->producto->producto_id,
                    'email_destino' => $email,
                    'porcentaje_actual' => $this->porcentajeActual,
                    'stock_minimo' => $this->stockMinimo
                ]);

            } catch (\Exception $e) {
                Log::error('Error enviando alerta de stock bajo', [
                    'producto_id' => $this->producto->producto_id,
                    'email_destino' => $email,
                    'error' => $e->getMessage()
                ]);
            }
        }
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

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Job de alerta de stock bajo falló', [
            'producto_id' => $this->producto->producto_id,
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts()
        ]);
    }
}
