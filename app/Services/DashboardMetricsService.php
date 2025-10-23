<?php

namespace App\Services;

use App\Services\RedisCacheService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardMetricsService
{
    protected $cacheService;

    public function __construct(RedisCacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Obtener métricas de rendimiento del dashboard
     */
    public function getPerformanceMetrics(): array
    {
        $cacheKey = 'dashboard:performance_metrics';
        
        return $this->cacheService->remember($cacheKey, 600, function () {
            try {
                $metrics = [
                    'database_performance' => $this->getDatabaseMetrics(),
                    'cache_performance' => $this->getCacheMetrics(),
                    'system_health' => $this->getSystemHealthMetrics(),
                    'query_performance' => $this->getQueryPerformanceMetrics(),
                ];

                return $metrics;
            } catch (\Exception $e) {
                Log::error('Error obteniendo métricas de rendimiento: ' . $e->getMessage());
                return [];
            }
        });
    }

    /**
     * Métricas de base de datos
     */
    private function getDatabaseMetrics(): array
    {
        try {
            $dbStats = DB::select("
                SELECT 
                    table_name,
                    table_rows,
                    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS size_mb
                FROM information_schema.tables 
                WHERE table_schema = DATABASE()
                ORDER BY (data_length + index_length) DESC
                LIMIT 10
            ");

            return [
                'tables' => $dbStats,
                'total_tables' => count($dbStats),
                'largest_table' => $dbStats[0] ?? null,
            ];
        } catch (\Exception $e) {
            Log::error('Error obteniendo métricas de BD: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Métricas de caché
     */
    private function getCacheMetrics(): array
    {
        try {
            $cacheStats = $this->cacheService->getStats();
            
            return [
                'redis_stats' => $cacheStats,
                'cache_hit_rate' => $cacheStats['hit_rate'] ?? 0,
                'memory_usage' => $cacheStats['used_memory'] ?? 'N/A',
                'connected_clients' => $cacheStats['connected_clients'] ?? 0,
            ];
        } catch (\Exception $e) {
            Log::error('Error obteniendo métricas de caché: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Métricas de salud del sistema
     */
    private function getSystemHealthMetrics(): array
    {
        try {
            $health = [
                'database_connection' => $this->checkDatabaseConnection(),
                'redis_connection' => $this->checkRedisConnection(),
                'disk_space' => $this->getDiskSpace(),
                'php_memory' => $this->getPhpMemoryUsage(),
            ];

            $health['overall_health'] = $this->calculateOverallHealth($health);

            return $health;
        } catch (\Exception $e) {
            Log::error('Error obteniendo métricas de salud: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Métricas de rendimiento de consultas
     */
    private function getQueryPerformanceMetrics(): array
    {
        try {
            $slowQueries = DB::select("
                SELECT 
                    query,
                    avg_timer_wait/1000000000 as avg_time_seconds,
                    count_star as executions
                FROM performance_schema.events_statements_summary_by_digest 
                WHERE avg_timer_wait > 1000000000
                ORDER BY avg_timer_wait DESC
                LIMIT 5
            ");

            return [
                'slow_queries' => $slowQueries,
                'total_slow_queries' => count($slowQueries),
            ];
        } catch (\Exception $e) {
            Log::error('Error obteniendo métricas de consultas: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Verificar conexión a base de datos
     */
    private function checkDatabaseConnection(): bool
    {
        try {
            DB::connection()->getPdo();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Verificar conexión a Redis
     */
    private function checkRedisConnection(): bool
    {
        try {
            $this->cacheService->get('test_connection');
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Obtener espacio en disco
     */
    private function getDiskSpace(): array
    {
        try {
            $bytes = disk_free_space(storage_path());
            $total = disk_total_space(storage_path());
            
            return [
                'free_bytes' => $bytes,
                'total_bytes' => $total,
                'free_gb' => round($bytes / 1024 / 1024 / 1024, 2),
                'total_gb' => round($total / 1024 / 1024 / 1024, 2),
                'usage_percentage' => round((($total - $bytes) / $total) * 100, 2),
            ];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Obtener uso de memoria PHP
     */
    private function getPhpMemoryUsage(): array
    {
        return [
            'current_memory' => memory_get_usage(true),
            'peak_memory' => memory_get_peak_usage(true),
            'current_mb' => round(memory_get_usage(true) / 1024 / 1024, 2),
            'peak_mb' => round(memory_get_peak_usage(true) / 1024 / 1024, 2),
            'limit' => ini_get('memory_limit'),
        ];
    }

    /**
     * Calcular salud general del sistema
     */
    private function calculateOverallHealth(array $health): string
    {
        $checks = [
            $health['database_connection'],
            $health['redis_connection'],
        ];

        $passed = array_sum($checks);
        $total = count($checks);

        if ($passed === $total) {
            return 'excellent';
        } elseif ($passed >= $total * 0.8) {
            return 'good';
        } elseif ($passed >= $total * 0.6) {
            return 'fair';
        } else {
            return 'poor';
        }
    }

    /**
     * Limpiar caché de métricas
     */
    public function clearMetricsCache(): bool
    {
        try {
            $this->cacheService->forget('dashboard:performance_metrics');
            return true;
        } catch (\Exception $e) {
            Log::error('Error limpiando caché de métricas: ' . $e->getMessage());
            return false;
        }
    }
}
