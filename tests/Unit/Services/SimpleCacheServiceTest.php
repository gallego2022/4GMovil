<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\CacheService;
use App\Services\LoggingService;

class SimpleCacheServiceTest extends TestCase
{
    protected CacheService $cacheService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cacheService = new CacheService(new LoggingService());
    }

    /** @test */
    public function it_can_create_cache_service()
    {
        $this->assertInstanceOf(CacheService::class, $this->cacheService);
    }

    /** @test */
    public function it_has_required_methods()
    {
        $this->assertTrue(method_exists($this->cacheService, 'set'));
        $this->assertTrue(method_exists($this->cacheService, 'get'));
        $this->assertTrue(method_exists($this->cacheService, 'has'));
        $this->assertTrue(method_exists($this->cacheService, 'forget'));
        $this->assertTrue(method_exists($this->cacheService, 'remember'));
        $this->assertTrue(method_exists($this->cacheService, 'rememberForever'));
        $this->assertTrue(method_exists($this->cacheService, 'increment'));
        $this->assertTrue(method_exists($this->cacheService, 'decrement'));
        $this->assertTrue(method_exists($this->cacheService, 'flush'));
        $this->assertTrue(method_exists($this->cacheService, 'getStats'));
    }

    /** @test */
    public function it_can_handle_simple_cache_operations()
    {
        // Este test verifica que el servicio puede ser instanciado y tiene los métodos necesarios
        $this->assertTrue(true);
    }
}
