<?php

namespace App\Console\Commands;

use App\Services\Infrastructure\RedisCacheService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ClearDashboardCache extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'dashboard:clear-cache {--all : Clear all dashboard cache}';

    /**
     * The console command description.
     */
    protected $description = 'Clear dashboard cache to force refresh of statistics';

    protected $cacheService;

    public function __construct(RedisCacheService $cacheService)
    {
        parent::__construct();
        $this->cacheService = $cacheService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $patterns = [
                'dashboard:basic_stats',
                'dashboard:webhook_stats', 
                'dashboard:pedido_stats',
                'dashboard:performance_metrics'
            ];

            if ($this->option('all')) {
                $patterns[] = 'dashboard:*';
            }

            $cleared = 0;
            
            // Limpiar caché usando el servicio de caché directamente
            foreach ($patterns as $pattern) {
                if ($pattern === 'dashboard:*') {
                    // Limpiar todo el caché del dashboard
                    $this->cacheService->forgetPattern('dashboard:*');
                    $cleared++;
                } else {
                    // Limpiar claves específicas
                    if ($this->cacheService->forget($pattern)) {
                        $cleared++;
                    }
                }
            }

            $this->info("✅ Dashboard cache cleared successfully!");
            $this->info("🗑️  Cleared cache entries for patterns: " . implode(', ', $patterns));
            
            Log::info('Dashboard cache cleared via command', [
                'patterns' => $patterns
            ]);

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error("❌ Error clearing dashboard cache: " . $e->getMessage());
            Log::error('Error clearing dashboard cache', [
                'error' => $e->getMessage()
            ]);
            
            return Command::FAILURE;
        }
    }
}
