<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Verificar stock diariamente a las 8:00 AM
        $schedule->command('inventario:verificar-stock --notificar')
                 ->dailyAt('08:00')
                 ->appendOutputTo(storage_path('logs/inventario.log'));
        
        // Verificar stock sin notificaciones cada 4 horas
        $schedule->command('inventario:verificar-stock')
                 ->everyFourHours()
                 ->appendOutputTo(storage_path('logs/inventario.log'));

        // ===== ALERTAS DE VARIANTES =====
        
        // Verificar alertas de variantes diariamente a las 9:00 AM
        $schedule->command('variantes:verificar-alertas')
                 ->dailyAt('09:00')
                 ->appendOutputTo(storage_path('logs/variantes-alertas.log'));
        
        // Verificar stock agotado cada 2 horas
        $schedule->command('variantes:verificar-alertas --tipo=agotado')
                 ->everyTwoHours()
                 ->appendOutputTo(storage_path('logs/variantes-agotado.log'));
        
        // Verificar stock crítico cada 4 horas
        $schedule->command('variantes:verificar-alertas --tipo=critico')
                 ->everyFourHours()
                 ->appendOutputTo(storage_path('logs/variantes-critico.log'));
        
        // Verificar stock bajo cada 6 horas
        $schedule->command('variantes:verificar-alertas --tipo=bajo')
                 ->everySixHours()
                 ->appendOutputTo(storage_path('logs/variantes-bajo.log'));

        // ===== LIMPIEZA DE RESERVAS =====
        
        // Limpiar reservas expiradas cada hora
        $schedule->command('reservas:limpiar-expiradas')
                 ->hourly()
                 ->appendOutputTo(storage_path('logs/reservas.log'));
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
} 