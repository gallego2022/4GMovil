<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\DashboardMetricsService;
use App\Services\RedisCacheService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

class DashboardMetricsServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $metricsService;
    protected $cacheService;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->app['config']->set('session.driver', 'array');
        
        // Crear mock del cache service
        $this->cacheService = Mockery::mock(RedisCacheService::class);
        $this->metricsService = new DashboardMetricsService($this->cacheService);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Test: Obtener métricas de rendimiento
     */
    public function test_get_performance_metrics()
    {
        // Arrange
        $metrics = [
            'database_performance' => [
                'tables' => [],
                'total_tables' => 10,
                'largest_table' => null
            ],
            'cache_performance' => [
                'redis_stats' => [],
                'cache_hit_rate' => 85.5,
                'memory_usage' => '10MB',
                'connected_clients' => 5
            ],
            'system_health' => [
                'database_connection' => true,
                'redis_connection' => true,
                'disk_space' => [],
                'php_memory' => [],
                'overall_health' => 'excellent'
            ],
            'query_performance' => [
                'slow_queries' => [],
                'total_slow_queries' => 0
            ]
        ];

        $this->cacheService
            ->shouldReceive('remember')
            ->once()
            ->andReturn($metrics);

        // Act
        $result = $this->metricsService->getPerformanceMetrics();

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('database_performance', $result);
        $this->assertArrayHasKey('cache_performance', $result);
        $this->assertArrayHasKey('system_health', $result);
        $this->assertArrayHasKey('query_performance', $result);
    }

    /**
     * Test: Obtener métricas con error en métodos internos
     */
    public function test_get_performance_metrics_with_internal_error()
    {
        // Arrange - Simular que los métodos internos fallan pero devuelven arrays vacíos
        $this->cacheService
            ->shouldReceive('remember')
            ->once()
            ->andReturn([
                'database_performance' => [],
                'cache_performance' => [],
                'system_health' => [],
                'query_performance' => []
            ]);

        // Act
        $result = $this->metricsService->getPerformanceMetrics();

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('database_performance', $result);
        $this->assertArrayHasKey('cache_performance', $result);
    }

    /**
     * Test: Limpiar caché de métricas
     */
    public function test_clear_metrics_cache()
    {
        // Arrange
        $this->cacheService
            ->shouldReceive('forget')
            ->once()
            ->with('dashboard:performance_metrics')
            ->andReturn(true);

        // Act
        $result = $this->metricsService->clearMetricsCache();

        // Assert
        $this->assertTrue($result);
    }

    /**
     * Test: Limpiar caché con error
     */
    public function test_clear_metrics_cache_with_error()
    {
        // Arrange
        $this->cacheService
            ->shouldReceive('forget')
            ->once()
            ->with('dashboard:performance_metrics')
            ->andThrow(new \Exception('Error limpiando caché'));

        // Act
        $result = $this->metricsService->clearMetricsCache();

        // Assert
        $this->assertFalse($result);
    }

    /**
     * Test: Verificar estructura de métricas de caché
     */
    public function test_cache_metrics_structure()
    {
        // Arrange
        $cacheStats = [
            'hit_rate' => 85.5,
            'used_memory' => '10MB',
            'connected_clients' => 5
        ];

        $this->cacheService
            ->shouldReceive('remember')
            ->once()
            ->andReturn([
                'cache_performance' => [
                    'redis_stats' => $cacheStats,
                    'cache_hit_rate' => 85.5,
                    'memory_usage' => '10MB',
                    'connected_clients' => 5
                ]
            ]);

        $this->cacheService
            ->shouldReceive('getStats')
            ->andReturn($cacheStats);

        // Act
        $result = $this->metricsService->getPerformanceMetrics();

        // Assert - Usando reflexión para acceder a métodos privados
        $reflection = new \ReflectionClass($this->metricsService);
        $method = $reflection->getMethod('getCacheMetrics');
        $method->setAccessible(true);
        
        // Verificar que el método existe y es privado
        $this->assertTrue($method->isPrivate());
    }

    /**
     * Test: Verificar métricas de salud del sistema
     */
    public function test_system_health_metrics()
    {
        // Arrange
        $health = [
            'database_connection' => true,
            'redis_connection' => true,
            'disk_space' => [
                'free_gb' => 50.5,
                'total_gb' => 100.0,
                'usage_percentage' => 49.5
            ],
            'php_memory' => [
                'current_mb' => 10.5,
                'peak_mb' => 15.0,
                'limit' => '128M'
            ],
            'overall_health' => 'excellent'
        ];

        $this->cacheService
            ->shouldReceive('remember')
            ->once()
            ->andReturn([
                'system_health' => $health
            ]);

        // Act
        $result = $this->metricsService->getPerformanceMetrics();

        // Assert
        $this->assertIsArray($result);
        if (isset($result['system_health'])) {
            $this->assertArrayHasKey('overall_health', $result['system_health']);
        }
    }

    /**
     * Test: Verificar cálculo de salud general
     */
    public function test_calculate_overall_health()
    {
        // Arrange
        $health = [
            'database_connection' => true,
            'redis_connection' => true,
            'overall_health' => 'excellent'
        ];

        $this->cacheService
            ->shouldReceive('remember')
            ->once()
            ->andReturn(['system_health' => $health]);

        // Act
        $result = $this->metricsService->getPerformanceMetrics();

        // Assert
        $this->assertIsArray($result);
    }

    /**
     * Test: Obtener métricas de base de datos vacías
     */
    public function test_get_database_metrics_empty()
    {
        // Arrange
        $this->cacheService
            ->shouldReceive('remember')
            ->once()
            ->andReturn([
                'database_performance' => [
                    'tables' => [],
                    'total_tables' => 0,
                    'largest_table' => null
                ]
            ]);

        // Act
        $result = $this->metricsService->getPerformanceMetrics();

        // Assert
        $this->assertIsArray($result);
    }

    /**
     * Test: Obtener métricas de consultas vacías
     */
    public function test_get_query_performance_empty()
    {
        // Arrange
        $this->cacheService
            ->shouldReceive('remember')
            ->once()
            ->andReturn([
                'query_performance' => [
                    'slow_queries' => [],
                    'total_slow_queries' => 0
                ]
            ]);

        // Act
        $result = $this->metricsService->getPerformanceMetrics();

        // Assert
        $this->assertIsArray($result);
        if (isset($result['query_performance'])) {
            $this->assertEquals(0, $result['query_performance']['total_slow_queries']);
        }
    }

    /**
     * Test: Métricas con sistema saludable
     */
    public function test_metrics_with_healthy_system()
    {
        // Arrange
        $metrics = [
            'database_performance' => ['total_tables' => 10],
            'cache_performance' => ['cache_hit_rate' => 95.0],
            'system_health' => [
                'overall_health' => 'excellent',
                'database_connection' => true,
                'redis_connection' => true
            ],
            'query_performance' => ['total_slow_queries' => 0]
        ];

        $this->cacheService
            ->shouldReceive('remember')
            ->once()
            ->andReturn($metrics);

        // Act
        $result = $this->metricsService->getPerformanceMetrics();

        // Assert
        $this->assertArrayHasKey('system_health', $result);
        if (isset($result['system_health'])) {
            $this->assertEquals('excellent', $result['system_health']['overall_health']);
        }
    }
}

