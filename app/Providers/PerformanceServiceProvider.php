<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class PerformanceServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->configureDatabaseOptimizations();
        $this->configureCacheOptimizations();
        $this->configureLoggingOptimizations();
        $this->configureQueueOptimizations();
    }

    /**
     * Configure database performance optimizations
     */
    private function configureDatabaseOptimizations()
    {
        if (Config::get('optimization.database.query_cache')) {
            // Enable query result caching
            DB::whenQueryingForLongerThan(500, function () {
                Log::info('Slow query detected', [
                    'queries' => DB::getQueryLog(),
                    'time' => microtime(true)
                ]);
            });
        }

        if (Config::get('optimization.database.slow_query_log')) {
            $threshold = Config::get('optimization.database.slow_query_threshold', 1000);
            
            DB::listen(function ($query) use ($threshold) {
                if ($query->time > $threshold) {
                    Log::warning('Slow query detected', [
                        'sql' => $query->sql,
                        'bindings' => $query->bindings,
                        'time' => $query->time,
                        'connection' => $query->connection->getName()
                    ]);
                }
            });
        }
    }

    /**
     * Configure cache optimizations
     */
    private function configureCacheOptimizations()
    {
        if (Config::get('optimization.cache.enabled')) {
            // Configure cache TTL instead of using setPrefix
            $ttl = Config::get('optimization.cache.ttl', 3600);
            config(['cache.ttl' => $ttl]);
            
            // Note: Cache prefix is now configured in config/cache.php
            // and cannot be changed dynamically in newer Laravel versions
        }
    }

    /**
     * Configure logging optimizations
     */
    private function configureLoggingOptimizations()
    {
        if (Config::get('optimization.logging.optimize')) {
            // Set maximum log files
            $maxFiles = Config::get('optimization.logging.max_files', 30);
            config(['logging.channels.daily.files' => $maxFiles]);
            
            // Set log level
            $level = Config::get('optimization.logging.level', 'info');
            config(['logging.default' => $level]);
        }
    }

    /**
     * Configure queue optimizations
     */
    private function configureQueueOptimizations()
    {
        if (Config::get('optimization.queue.optimize')) {
            // Set queue batch size
            $batchSize = Config::get('optimization.queue.batch_size', 100);
            config(['queue.batch_size' => $batchSize]);
            
            // Set queue timeout
            $timeout = Config::get('optimization.queue.timeout', 60);
            config(['queue.timeout' => $timeout]);
        }
    }
} 