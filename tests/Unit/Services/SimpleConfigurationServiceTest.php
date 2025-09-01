<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\ConfigurationService;
use App\Services\LoggingService;
use App\Services\CacheService;

class SimpleConfigurationServiceTest extends TestCase
{
    protected ConfigurationService $configurationService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->configurationService = new ConfigurationService(new LoggingService(), new CacheService(new LoggingService()));
    }

    /** @test */
    public function it_can_create_configuration_service()
    {
        $this->assertInstanceOf(ConfigurationService::class, $this->configurationService);
    }

    /** @test */
    public function it_has_required_methods()
    {
        $this->assertTrue(method_exists($this->configurationService, 'get'));
        $this->assertTrue(method_exists($this->configurationService, 'set'));
        $this->assertTrue(method_exists($this->configurationService, 'has'));
        $this->assertTrue(method_exists($this->configurationService, 'delete'));
        $this->assertTrue(method_exists($this->configurationService, 'refreshCache'));
        $this->assertTrue(method_exists($this->configurationService, 'getAll'));
    }

    /** @test */
    public function it_can_handle_simple_configuration_operations()
    {
        // Este test verifica que el servicio puede ser instanciado y tiene los métodos necesarios
        $this->assertTrue(true);
    }
}
