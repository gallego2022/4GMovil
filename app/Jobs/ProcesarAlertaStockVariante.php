<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\VarianteProducto;
use App\Mail\StockBajoVariante;
use App\Mail\StockAgotadoVariante;

class ProcesarAlertaStockVariante implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $variante;
    public $tipoAlerta;
    public $stockAnterior;
    public $stockActual;

    /**
     * Create a new job instance.
     */
    public function __construct(VarianteProducto $variante, string $tipoAlerta, int $stockAnterior = null, int $stockActual = null)
    {
        $this->variante = $variante;
        $this->tipoAlerta = $tipoAlerta;
        $this->stockAnterior = $stockAnterior;
        $this->stockActual = $stockActual;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info('Procesando alerta de stock para variante', [
                'variante_id' => $this->variante->variante_id,
                'producto_id' => $this->variante->producto_id,
                'tipo_alerta' => $this->tipoAlerta,
                'stock_anterior' => $this->stockAnterior,
                'stock_actual' => $this->stockActual
            ]);

            $emailsDestino = $this->obtenerEmailsDestino();
            
            if (empty($emailsDestino)) {
                Log::warning('No hay emails configurados para alertas de stock de variantes');
                return;
            }

            foreach ($emailsDestino as $email) {
                $this->enviarAlerta($email);
            }

            Log::info('Alerta de stock de variante procesada exitosamente', [
                'variante_id' => $this->variante->variante_id,
                'tipo_alerta' => $this->tipoAlerta,
                'emails_enviados' => count($emailsDestino)
            ]);

        } catch (\Exception $e) {
            Log::error('Error procesando alerta de stock de variante', [
                'variante_id' => $this->variante->variante_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Enviar alerta específica según el tipo
     */
    private function enviarAlerta(string $email): void
    {
        try {
            switch ($this->tipoAlerta) {
                case 'agotado':
                    Mail::to($email)->send(new StockAgotadoVariante($this->variante));
                    break;
                    
                case 'critico':
                case 'bajo':
                default:
                    Mail::to($email)->send(new StockBajoVariante($this->variante, $this->tipoAlerta));
                    break;
            }

            Log::info('Alerta de variante enviada', [
                'variante_id' => $this->variante->variante_id,
                'email_destino' => $email,
                'tipo_alerta' => $this->tipoAlerta
            ]);

        } catch (\Exception $e) {
            Log::error('Error enviando alerta de variante', [
                'variante_id' => $this->variante->variante_id,
                'email_destino' => $email,
                'tipo_alerta' => $this->tipoAlerta,
                'error' => $e->getMessage()
            ]);
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
}
