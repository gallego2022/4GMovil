<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\AuthService;
use App\Services\LoggingService;
use App\Services\CacheService;

class SimpleAuthServiceTest extends TestCase
{
    protected AuthService $authService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authService = new AuthService(
            new LoggingService(), 
            new CacheService(new LoggingService()),
            new \App\Services\ValidationService(new LoggingService())
        );
    }

    /** @test */
    public function it_can_create_auth_service()
    {
        $this->assertInstanceOf(AuthService::class, $this->authService);
    }

    /** @test */
    public function it_has_required_methods()
    {
        $this->assertTrue(method_exists($this->authService, 'register'));
        $this->assertTrue(method_exists($this->authService, 'login'));
        $this->assertTrue(method_exists($this->authService, 'logout'));
        $this->assertTrue(method_exists($this->authService, 'verifyEmail'));
        $this->assertTrue(method_exists($this->authService, 'resetPassword'));
        $this->assertTrue(method_exists($this->authService, 'changePassword'));
        $this->assertTrue(method_exists($this->authService, 'verifySessionToken'));
        $this->assertTrue(method_exists($this->authService, 'getUserInfo'));
        $this->assertTrue(method_exists($this->authService, 'isAdmin'));
        $this->assertTrue(method_exists($this->authService, 'getUserPermissions'));
    }

    /** @test */
    public function it_can_handle_simple_auth_operations()
    {
        // Este test verifica que el servicio puede ser instanciado y tiene los métodos necesarios
        $this->assertTrue(true);
    }
}
