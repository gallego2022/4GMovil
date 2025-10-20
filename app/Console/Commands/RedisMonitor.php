<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class RedisMonitor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'redis:monitor {--interval=5 : Intervalo en segundos} {--duration=60 : Duración en segundos}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monitorear Redis en tiempo real';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $interval = (int) $this->option('interval');
        $duration = (int) $this->option('duration');
        
        $this->info("🔍 Monitoreando Redis cada {$interval} segundos por {$duration} segundos...");
        $this->newLine();
        
        $startTime = time();
        $iteration = 0;
        
        while ((time() - $startTime) < $duration) {
            $iteration++;
            $this->line("--- Iteración {$iteration} ---");
            
            $this->checkRedisStatus();
            
            if ((time() - $startTime) < $duration) {
                sleep($interval);
            }
        }
        
        $this->newLine();
        $this->info('✅ Monitoreo completado');
    }

    protected function checkRedisStatus()
    {
        try {
            // Ping
            $pong = Redis::ping();
            $this->line("Ping: <fg=green>{$pong}</>");
            
            // Información del servidor
            $info = Redis::info();
            
            $this->line("Memoria usada: <fg=cyan>" . ($info['used_memory_human'] ?? 'N/A') . "</>");
            $this->line("Conexiones: <fg=cyan>" . ($info['connected_clients'] ?? 'N/A') . "</>");
            $this->line("Comandos procesados: <fg=cyan>" . ($info['total_commands_processed'] ?? 'N/A') . "</>");
            
            // Verificar claves
            $keys = Redis::keys('*');
            $this->line("Claves totales: <fg=cyan>" . count($keys) . "</>");
            
            // Verificar colas
            $queueSize = Redis::llen('queues:default');
            $this->line("Colas pendientes: <fg=cyan>{$queueSize}</>");
            
        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
        }
        
        $this->newLine();
    }
}
