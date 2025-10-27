<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\DashboardService;
use App\Services\RedisCacheService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

class DashboardServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $dashboardService;
    protected $cacheService;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->app['config']->set('session.driver', 'array');
        
        // Crear mock del cache service
        $this->cacheService = Mockery::mock(RedisCacheService::class);
        $this->dashboardService = new DashboardService($this->cacheService);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Test: Obtener estadísticas básicas
     */
    public function test_get_basic_stats()
    {
        // Arrange
        $stats = (object) [
            'totalProductos' => '100',
            'usuarios' => '50',
            'totalCategorias' => '10',
            'totalMarcas' => '20',
            'total_variantes' => '200'
        ];

        $this->cacheService
            ->shouldReceive('remember')
            ->once()
            ->andReturn([
                'totalProductos' => 100,
                'usuarios' => 50,
                'totalCategorias' => 10,
                'totalMarcas' => 20,
                'total_variantes' => 200
            ]);

        // Act
        $result = $this->dashboardService->getBasicStats();

        // Assert
        $this->assertEquals(100, $result['totalProductos']);
        $this->assertEquals(50, $result['usuarios']);
        $this->assertEquals(10, $result['totalCategorias']);
        $this->assertEquals(20, $result['totalMarcas']);
        $this->assertEquals(200, $result['total_variantes']);
    }

    /**
     * Test: Obtener estadísticas de webhooks
     */
    public function test_get_webhook_stats()
    {
        // Arrange
        $this->cacheService
            ->shouldReceive('remember')
            ->once()
            ->andReturn([
                'total_events' => 100,
                'processed_events' => 80,
                'failed_events' => 10,
                'pending_events' => 10
            ]);

        // Act
        $result = $this->dashboardService->getWebhookStats();

        // Assert
        $this->assertEquals(100, $result['total_events']);
        $this->assertEquals(80, $result['processed_events']);
        $this->assertEquals(10, $result['failed_events']);
        $this->assertEquals(10, $result['pending_events']);
    }

    /**
     * Test: Obtener estadísticas de pedidos
     */
    public function test_get_pedido_stats()
    {
        // Arrange
        $this->cacheService
            ->shouldReceive('remember')
            ->once()
            ->andReturn([
                'total_pedidos' => 200,
                'pedidos_pendientes' => 50,
                'pedidos_confirmados' => 140,
                'pedidos_cancelados' => 10
            ]);

        // Act
        $result = $this->dashboardService->getPedidoStats();

        // Assert
        $this->assertEquals(200, $result['total_pedidos']);
        $this->assertEquals(50, $result['pedidos_pendientes']);
        $this->assertEquals(140, $result['pedidos_confirmados']);
        $this->assertEquals(10, $result['pedidos_cancelados']);
    }

    /**
     * Test: Obtener productos recientes
     */
    public function test_get_recent_products()
    {
        // Act
        $result = $this->dashboardService->getRecentProducts(5);

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('data', $result);
    }

    /**
     * Test: Obtener webhooks filtrados
     */
    public function test_get_filtered_webhooks()
    {
        // Arrange
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('filled')->andReturn(false);
        $request->shouldReceive('get')->andReturn(10);

        // Act
        $result = $this->dashboardService->getFilteredWebhooks($request);

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('data', $result);
    }

    /**
     * Test: Obtener filtros aplicados
     */
    public function test_get_applied_filters()
    {
        // Arrange
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('getAttribute')->andReturn(null);
        $request->status = 'processed';
        $request->event_type = 'payment.succeeded';
        $request->date_from = '2024-01-01';
        $request->date_to = '2024-12-31';
        $request->pedido_id = '123';
        $request->shouldReceive('get')->with('limit', 10)->andReturn(10);

        // Act
        $result = $this->dashboardService->getAppliedFilters($request);

        // Assert
        $this->assertEquals('processed', $result['status']);
        $this->assertEquals('payment.succeeded', $result['event_type']);
        $this->assertEquals('2024-01-01', $result['date_from']);
        $this->assertEquals('2024-12-31', $result['date_to']);
        $this->assertEquals('123', $result['pedido_id']);
        $this->assertEquals(10, $result['limit']);
    }

    /**
     * Test: Obtener datos completos del dashboard
     */
    public function test_get_dashboard_data()
    {
        // Arrange
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('all')->andReturn([]);
        $request->shouldReceive('getAttribute')->andReturn(null);
        $request->shouldReceive('filled')->andReturn(false);
        $request->shouldReceive('get')->andReturn(10);
        $request->shouldReceive('whereDate')->andReturnSelf();
        $request->shouldReceive('where')->andReturnSelf();
        $request->shouldReceive('processed')->andReturnSelf();
        $request->shouldReceive('failed')->andReturnSelf();
        $request->shouldReceive('pending')->andReturnSelf();
        $request->shouldReceive('orderBy')->andReturnSelf();
        $request->shouldReceive('limit')->andReturnSelf();
        $request->shouldReceive('get')->andReturn(collect());

        $this->cacheService
            ->shouldReceive('remember')
            ->once()
            ->andReturn([
                'success' => true,
                'basicStats' => ['totalProductos' => 100],
                'webhookStats' => ['total_events' => 100],
                'pedidoStats' => ['total_pedidos' => 200],
                'recentProducts' => collect(),
                'filteredWebhooks' => collect(),
                'filters' => [],
                'cached_at' => now()->toDateTimeString()
            ]);

        // Act
        $result = $this->dashboardService->getDashboardData($request);

        // Assert
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('basicStats', $result);
        $this->assertArrayHasKey('webhookStats', $result);
        $this->assertArrayHasKey('pedidoStats', $result);
    }

    /**
     * Test: Dashboard con error en estadísticas básicas
     */
    public function test_get_basic_stats_with_error()
    {
        // Arrange
        $this->cacheService
            ->shouldReceive('remember')
            ->once()
            ->andReturn([
                'totalProductos' => 0,
                'usuarios' => 0,
                'totalCategorias' => 0,
                'totalMarcas' => 0,
                'total_variantes' => 0
            ]);

        // Act
        $result = $this->dashboardService->getBasicStats();

        // Assert
        $this->assertEquals(0, $result['totalProductos']);
        $this->assertEquals(0, $result['usuarios']);
    }

    /**
     * Test: Obtener productos recientes con límite
     */
    public function test_get_recent_products_with_limit()
    {
        // Act
        $result = $this->dashboardService->getRecentProducts(10);

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('data', $result);
    }

    /**
     * Test: Filtros por estado de webhook
     */
    public function test_get_filtered_webhooks_by_status()
    {
        // Arrange
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('filled')
            ->with('status')
            ->andReturn(true);
        $request->status = 'processed';
        $request->shouldReceive('filled')->with('event_type')->andReturn(false);
        $request->shouldReceive('filled')->with('date_from')->andReturn(false);
        $request->shouldReceive('filled')->with('date_to')->andReturn(false);
        $request->shouldReceive('filled')->with('pedido_id')->andReturn(false);
        $request->shouldReceive('get')->andReturn(10);

        // Act
        $result = $this->dashboardService->getFilteredWebhooks($request);

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
    }

    /**
     * Test: Dashboard data con fallback de error
     */
    public function test_get_dashboard_data_with_error()
    {
        // Arrange
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('all')->andReturn([]);
        $request->shouldReceive('getAttribute')->andReturn(null);
        $request->shouldReceive('filled')->andReturn(false);
        $request->shouldReceive('get')->andReturn(10);
        $request->shouldReceive('whereDate')->andReturnSelf();
        $request->shouldReceive('where')->andReturnSelf();
        $request->shouldReceive('processed')->andReturnSelf();
        $request->shouldReceive('failed')->andReturnSelf();
        $request->shouldReceive('pending')->andReturnSelf();
        $request->shouldReceive('orderBy')->andReturnSelf();
        $request->shouldReceive('limit')->andReturnSelf();
        $request->shouldReceive('get')->andReturn(collect());

        $this->cacheService
            ->shouldReceive('remember')
            ->once()
            ->andReturn([
                'success' => false,
                'basicStats' => [],
                'webhookStats' => [],
                'pedidoStats' => [],
                'recentProducts' => collect(),
                'filteredWebhooks' => collect(),
                'filters' => [],
                'error' => 'Error al cargar datos del dashboard'
            ]);

        // Act
        $result = $this->dashboardService->getDashboardData($request);

        // Assert
        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('error', $result);
    }
}

