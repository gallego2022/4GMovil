<?php

namespace App\Console\Commands;

use App\Models\VarianteProducto;
use App\Jobs\ProcesarAlertaStockVariante;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class VerificarAlertasStockVariantes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'variantes:verificar-alertas {--tipo=all : Tipo de alerta a verificar (bajo, critico, agotado, all)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verificar y enviar alertas de stock para variantes de productos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tipo = $this->option('tipo');
        $this->info("🔍 Verificando alertas de stock para variantes...");
        $this->info("Tipo de alerta: {$tipo}");

        $contador = 0;

        try {
            switch ($tipo) {
                case 'agotado':
                    $contador = $this->verificarStockAgotado();
                    break;
                case 'critico':
                    $contador = $this->verificarStockCritico();
                    break;
                case 'bajo':
                    $contador = $this->verificarStockBajo();
                    break;
                case 'all':
                default:
                    $contador = $this->verificarTodasLasAlertas();
                    break;
            }

            if ($contador > 0) {
                $this->info("✅ Se procesaron {$contador} alertas de stock para variantes.");
                Log::info("Comando verificar alertas de variantes ejecutado", [
                    'tipo' => $tipo,
                    'alertas_procesadas' => $contador
                ]);
            } else {
                $this->info("ℹ️ No se encontraron variantes que requieran alertas.");
            }

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error al verificar alertas de stock de variantes: ' . $e->getMessage());
            Log::error('Error en comando verificar alertas de variantes', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return Command::FAILURE;
        }
    }

    /**
     * Verificar stock agotado
     */
    private function verificarStockAgotado(): int
    {
        $variantes = VarianteProducto::with('producto')
            ->where('stock', '<=', 0)
            ->where('disponible', true)
            ->get();

        $contador = 0;
        foreach ($variantes as $variante) {
            $this->info("🚨 Variante agotada: {$variante->producto->nombre_producto} ({$variante->nombre})");
            
            dispatch(new ProcesarAlertaStockVariante(
                $variante,
                'agotado'
            ));
            
            $contador++;
        }

        return $contador;
    }

    /**
     * Verificar stock crítico (≤20% del stock inicial del producto)
     */
    private function verificarStockCritico(): int
    {
        $variantes = VarianteProducto::with('producto')
            ->where('disponible', true)
            ->where('stock', '>', 0)
            ->get();

        $contador = 0;
        foreach ($variantes as $variante) {
            $stockInicial = $variante->producto->stock_inicial ?? 0;
            $umbralCritico = $stockInicial > 0 ? (int) ceil(($stockInicial * 20) / 100) : 5;
            
            if ($variante->stock <= $umbralCritico) {
                $porcentaje = $stockInicial > 0 
                    ? round(($variante->stock / $stockInicial) * 100, 1) 
                    : 0;
                
                $this->info("⚠️ Stock crítico: {$variante->producto->nombre_producto} ({$variante->nombre}) - {$variante->stock} unidades ({$porcentaje}% del inicial)");
                
                dispatch(new ProcesarAlertaStockVariante(
                    $variante,
                    'critico'
                ));
                
                $contador++;
            }
        }

        return $contador;
    }

    /**
     * Verificar stock bajo (≤60% del stock inicial del producto)
     */
    private function verificarStockBajo(): int
    {
        $variantes = VarianteProducto::with('producto')
            ->where('disponible', true)
            ->where('stock', '>', 0)
            ->get();

        $contador = 0;
        foreach ($variantes as $variante) {
            $stockInicial = $variante->producto->stock_inicial ?? 0;
            $umbralBajo = $stockInicial > 0 ? (int) ceil(($stockInicial * 60) / 100) : 10;
            $umbralCritico = $stockInicial > 0 ? (int) ceil(($stockInicial * 20) / 100) : 5;
            
            if ($variante->stock <= $umbralBajo && $variante->stock > $umbralCritico) {
                $porcentaje = $stockInicial > 0 
                    ? round(($variante->stock / $stockInicial) * 100, 1) 
                    : 0;
                
                $this->info("📉 Stock bajo: {$variante->producto->nombre_producto} ({$variante->nombre}) - {$variante->stock} unidades ({$porcentaje}% del inicial)");
                
                dispatch(new ProcesarAlertaStockVariante(
                    $variante,
                    'bajo'
                ));
                
                $contador++;
            }
        }

        return $contador;
    }

    /**
     * Verificar todas las alertas
     */
    private function verificarTodasLasAlertas(): int
    {
        $contador = 0;
        
        $this->info("🔍 Verificando stock agotado...");
        $contador += $this->verificarStockAgotado();
        
        $this->info("🔍 Verificando stock crítico...");
        $contador += $this->verificarStockCritico();
        
        $this->info("🔍 Verificando stock bajo...");
        $contador += $this->verificarStockBajo();
        
        return $contador;
    }
}
