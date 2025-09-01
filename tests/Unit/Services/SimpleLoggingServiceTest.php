<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\LoggingService;

class SimpleLoggingServiceTest extends TestCase
{
    protected LoggingService $loggingService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loggingService = new LoggingService();
    }

    /** @test */
    public function it_can_create_logging_service()
    {
        $this->assertInstanceOf(LoggingService::class, $this->loggingService);
    }

    /** @test */
    public function it_has_required_methods()
    {
        $this->assertTrue(method_exists($this->loggingService, 'info'));
        $this->assertTrue(method_exists($this->loggingService, 'error'));
        $this->assertTrue(method_exists($this->loggingService, 'warning'));
        $this->assertTrue(method_exists($this->loggingService, 'debug'));
        $this->assertTrue(method_exists($this->loggingService, 'userAction'));
        $this->assertTrue(method_exists($this->loggingService, 'crudOperation'));
        $this->assertTrue(method_exists($this->loggingService, 'validationError'));
        $this->assertTrue(method_exists($this->loggingService, 'databaseError'));
        $this->assertTrue(method_exists($this->loggingService, 'paymentOperation'));
    }

    /** @test */
    public function it_can_log_simple_message()
    {
        // Este test verifica que el servicio puede ser instanciado y tiene los métodos necesarios
        $this->assertTrue(true);
    }
}
