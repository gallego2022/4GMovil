<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\AuthService;

class AuthServiceTest extends TestCase
{
    protected $authService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authService = new AuthService();
    }

    /** @test */
    public function it_can_create_auth_service()
    {
        $this->assertInstanceOf(AuthService::class, $this->authService);
    }

    /** @test */
    public function it_has_required_methods()
    {
        $this->assertTrue(method_exists($this->authService, 'registrar'));
        $this->assertTrue(method_exists($this->authService, 'logear'));
        $this->assertTrue(method_exists($this->authService, 'logout'));
        $this->assertTrue(method_exists($this->authService, 'cambiarContrasena'));
        $this->assertTrue(method_exists($this->authService, 'getPerfil'));
        $this->assertTrue(method_exists($this->authService, 'actualizarPerfil'));
    }

    /** @test */
    public function it_can_validate_password()
    {
        $request = new \Illuminate\Http\Request([
            'password' => 'testpassword123'
        ]);

        // Simular usuario autenticado
        $user = new \App\Models\Usuario([
            'contrasena' => \Illuminate\Support\Facades\Hash::make('testpassword123')
        ]);

        \Illuminate\Support\Facades\Auth::shouldReceive('user')
            ->andReturn($user);

        $result = $this->authService->validarContrasenaActual($request);
        
        $this->assertTrue($result['success']);
        $this->assertTrue($result['valid']);
    }
}
