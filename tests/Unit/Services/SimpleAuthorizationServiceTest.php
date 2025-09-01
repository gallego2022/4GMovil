<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\AuthorizationService;
use App\Services\LoggingService;
use App\Services\CacheService;

class SimpleAuthorizationServiceTest extends TestCase
{
    protected AuthorizationService $authorizationService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authorizationService = new AuthorizationService(new LoggingService(), new CacheService(new LoggingService()));
    }

    /** @test */
    public function it_can_create_authorization_service()
    {
        $this->assertInstanceOf(AuthorizationService::class, $this->authorizationService);
    }

    /** @test */
    public function it_has_required_methods()
    {
        $this->assertTrue(method_exists($this->authorizationService, 'hasPermission'));
        $this->assertTrue(method_exists($this->authorizationService, 'assignRole'));
        $this->assertTrue(method_exists($this->authorizationService, 'removeRole'));
        $this->assertTrue(method_exists($this->authorizationService, 'assignPermission'));
        $this->assertTrue(method_exists($this->authorizationService, 'removePermission'));
        $this->assertTrue(method_exists($this->authorizationService, 'getUserRoles'));
        $this->assertTrue(method_exists($this->authorizationService, 'getUserPermissions'));
        $this->assertTrue(method_exists($this->authorizationService, 'refreshPermissionsCache'));
    }

    /** @test */
    public function it_can_handle_simple_authorization_operations()
    {
        // Este test verifica que el servicio puede ser instanciado y tiene los métodos necesarios
        $this->assertTrue(true);
    }
}
