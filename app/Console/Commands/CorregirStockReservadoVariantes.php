<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CorregirStockReservadoVariantes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventario:corregir-stock-reservado-variantes 
                            {--forzar : Forzar corrección sin confirmación}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Corregir el campo stock_reservado de las variantes basándose en las reservas activas';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🔍 Iniciando corrección de stock_reservado de variantes...');

        // Obtener todas las variantes con stock_reservado > 0
        $variantesConReserva = DB::table('variantes_producto')
            ->where('stock_reservado', '>', 0)
            ->get();

        $this->info("📊 Encontradas {$variantesConReserva->count()} variantes con stock_reservado > 0");

        // Para cada variante, calcular el stock_reservado real basándose en reservas activas
        $variantesCorregidas = 0;
        $variantesConInconsistencias = [];

        foreach ($variantesConReserva as $variante) {
            // Calcular stock_reservado real desde reservas activas
            // Excluir reservas de pedidos confirmados y reservas confirmadas
            $stockReservadoReal = DB::table('reservas_stock_variantes as rsv')
                ->leftJoin('pedidos as p', 'rsv.referencia_pedido', '=', 'p.pedido_id')
                ->where('rsv.variante_id', $variante->variante_id)
                ->where('rsv.estado', 'activa')
                ->where('rsv.fecha_expiracion', '>', now())
                // Excluir reservas de pedidos confirmados (estado_id = 2)
                ->where(function ($query) {
                    $query->whereNull('p.estado_id')
                        ->orWhere('p.estado_id', '!=', 2);
                })
                // Verificar que no haya reservas confirmadas para esta variante
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('reservas_stock_variantes as rsv2')
                        ->whereColumn('rsv2.variante_id', 'rsv.variante_id')
                        ->where('rsv2.estado', 'confirmada')
                        ->whereNotNull('rsv2.referencia_pedido');
                })
                ->sum('rsv.cantidad');

            $stockReservadoActual = $variante->stock_reservado ?? 0;

            // Si hay inconsistencia, agregar a la lista
            if ($stockReservadoActual != $stockReservadoReal) {
                $variantesConInconsistencias[] = [
                    'variante_id' => $variante->variante_id,
                    'nombre' => $variante->nombre,
                    'producto_id' => $variante->producto_id,
                    'stock_reservado_actual' => $stockReservadoActual,
                    'stock_reservado_real' => $stockReservadoReal,
                ];
            }
        }

        // También verificar variantes que deberían tener stock_reservado pero no lo tienen
        $reservasActivas = DB::table('reservas_stock_variantes')
            ->where('estado', 'activa')
            ->where('fecha_expiracion', '>', now())
            ->get();

        foreach ($reservasActivas as $reserva) {
            $variante = DB::table('variantes_producto')
                ->where('variante_id', $reserva->variante_id)
                ->first();

            if ($variante && ($variante->stock_reservado ?? 0) == 0) {
                // Calcular stock_reservado real para esta variante
                // Excluir reservas de pedidos confirmados y reservas confirmadas
                $stockReservadoReal = DB::table('reservas_stock_variantes as rsv')
                    ->leftJoin('pedidos as p', 'rsv.referencia_pedido', '=', 'p.pedido_id')
                    ->where('rsv.variante_id', $reserva->variante_id)
                    ->where('rsv.estado', 'activa')
                    ->where('rsv.fecha_expiracion', '>', now())
                    // Excluir reservas de pedidos confirmados (estado_id = 2)
                    ->where(function ($query) {
                        $query->whereNull('p.estado_id')
                            ->orWhere('p.estado_id', '!=', 2);
                    })
                    // Verificar que no haya reservas confirmadas para esta variante
                    ->whereNotExists(function ($query) {
                        $query->select(DB::raw(1))
                            ->from('reservas_stock_variantes as rsv2')
                            ->whereColumn('rsv2.variante_id', 'rsv.variante_id')
                            ->where('rsv2.estado', 'confirmada')
                            ->whereNotNull('rsv2.referencia_pedido');
                    })
                    ->sum('rsv.cantidad');

                // Verificar si ya está en la lista
                $existe = false;
                foreach ($variantesConInconsistencias as $item) {
                    if ($item['variante_id'] == $reserva->variante_id) {
                        $existe = true;
                        break;
                    }
                }

                if (! $existe && $stockReservadoReal > 0) {
                    $variantesConInconsistencias[] = [
                        'variante_id' => $reserva->variante_id,
                        'nombre' => $variante->nombre ?? 'N/A',
                        'producto_id' => $variante->producto_id ?? null,
                        'stock_reservado_actual' => 0,
                        'stock_reservado_real' => $stockReservadoReal,
                    ];
                }
            }
        }

        if (empty($variantesConInconsistencias)) {
            $this->info('✅ No se encontraron inconsistencias en el stock_reservado de las variantes.');

            return Command::SUCCESS;
        }

        $this->newLine();
        $this->info('⚠️  Se encontraron '.count($variantesConInconsistencias).' variantes con inconsistencias:');
        $this->newLine();

        foreach ($variantesConInconsistencias as $item) {
            $this->line("  • Variante ID: {$item['variante_id']} ({$item['nombre']})");
            $this->line("    Producto ID: {$item['producto_id']}");
            $this->line("    Stock reservado actual: {$item['stock_reservado_actual']}");
            $this->line("    Stock reservado real: {$item['stock_reservado_real']}");
            $this->newLine();
        }

        if (! $this->option('forzar')) {
            if (! $this->confirm('¿Deseas corregir estas inconsistencias?')) {
                $this->info('❌ Operación cancelada.');

                return Command::SUCCESS;
            }
        }

        $this->newLine();
        $this->info('🔄 Corrigiendo inconsistencias...');

        foreach ($variantesConInconsistencias as $item) {
            DB::table('variantes_producto')
                ->where('variante_id', $item['variante_id'])
                ->update(['stock_reservado' => $item['stock_reservado_real']]);

            $variantesCorregidas++;

            $this->line("  ✅ Corregida variante ID: {$item['variante_id']} ({$item['nombre']})");
            $this->line("     Stock reservado actualizado de {$item['stock_reservado_actual']} a {$item['stock_reservado_real']}");
        }

        $this->newLine();
        $this->info("✅ Corrección completada. {$variantesCorregidas} variantes corregidas.");

        Log::info('Corrección de stock_reservado de variantes completada', [
            'variantes_corregidas' => $variantesCorregidas,
            'total_inconsistencias' => count($variantesConInconsistencias),
        ]);

        return Command::SUCCESS;
    }
}
