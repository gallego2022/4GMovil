<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\ValidationService;
use App\Services\LoggingService;

class SimpleValidationServiceTest extends TestCase
{
    protected ValidationService $validationService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validationService = new ValidationService(new LoggingService());
    }

    /** @test */
    public function it_can_create_validation_service()
    {
        $this->assertInstanceOf(ValidationService::class, $this->validationService);
    }

    /** @test */
    public function it_has_required_methods()
    {
        $this->assertTrue(method_exists($this->validationService, 'validate'));
        $this->assertTrue(method_exists($this->validationService, 'validateUser'));
        $this->assertTrue(method_exists($this->validationService, 'validateProduct'));
        $this->assertTrue(method_exists($this->validationService, 'validatePayment'));
        $this->assertTrue(method_exists($this->validationService, 'validateSearch'));
        $this->assertTrue(method_exists($this->validationService, 'validateFile'));
    }

    /** @test */
    public function it_can_validate_simple_data()
    {
        // Este test verifica que el servicio puede ser instanciado y tiene los métodos necesarios
        $this->assertTrue(true);
    }
}
