<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\JwtService;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class JwtAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected JwtService $jwtService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->jwtService = app(JwtService::class);
    }

    /** @test */
    public function it_can_generate_jwt_token(): void
    {
        $usuario = Usuario::factory()->create([
            'correo_electronico' => 'test@example.com',
            'contrasena' => Hash::make('password123'),
            'rol' => 'cliente',
            'estado' => true,
        ]);

        $token = $this->jwtService->generateToken($usuario);

        $this->assertNotEmpty($token);
        $this->assertIsString($token);
    }

    /** @test */
    public function it_can_validate_jwt_token(): void
    {
        $usuario = Usuario::factory()->create([
            'correo_electronico' => 'test@example.com',
            'contrasena' => Hash::make('password123'),
            'rol' => 'cliente',
            'estado' => true,
        ]);

        $token = $this->jwtService->generateToken($usuario);
        $payload = $this->jwtService->validateToken($token);

        $this->assertNotNull($payload);
        $this->assertEquals($usuario->usuario_id, $payload['sub']);
        $this->assertEquals($usuario->rol, $payload['rol']);
        $this->assertEquals($usuario->correo_electronico, $payload['email']);
    }

    /** @test */
    public function it_rejects_invalid_token(): void
    {
        $invalidToken = 'invalid.token.here';
        $payload = $this->jwtService->validateToken($invalidToken);

        $this->assertNull($payload);
    }

    /** @test */
    public function it_can_get_user_from_token(): void
    {
        $usuario = Usuario::factory()->create([
            'correo_electronico' => 'test@example.com',
            'contrasena' => Hash::make('password123'),
            'rol' => 'cliente',
            'estado' => true,
        ]);

        $token = $this->jwtService->generateToken($usuario);
        $userFromToken = $this->jwtService->getUserFromToken($token);

        $this->assertNotNull($userFromToken);
        $this->assertEquals($usuario->usuario_id, $userFromToken->usuario_id);
        $this->assertEquals($usuario->correo_electronico, $userFromToken->correo_electronico);
    }

    /** @test */
    public function it_can_identify_admin_token(): void
    {
        $admin = Usuario::factory()->create([
            'correo_electronico' => 'admin@example.com',
            'contrasena' => Hash::make('password123'),
            'rol' => 'admin',
            'estado' => true,
        ]);

        $cliente = Usuario::factory()->create([
            'correo_electronico' => 'cliente@example.com',
            'contrasena' => Hash::make('password123'),
            'rol' => 'cliente',
            'estado' => true,
        ]);

        $adminToken = $this->jwtService->generateToken($admin);
        $clienteToken = $this->jwtService->generateToken($cliente);

        $this->assertTrue($this->jwtService->isAdminToken($adminToken));
        $this->assertFalse($this->jwtService->isAdminToken($clienteToken));
    }

    /** @test */
    public function it_can_refresh_token(): void
    {
        $usuario = Usuario::factory()->create([
            'correo_electronico' => 'test@example.com',
            'contrasena' => Hash::make('password123'),
            'rol' => 'cliente',
            'estado' => true,
        ]);

        $originalToken = $this->jwtService->generateToken($usuario);
        $newToken = $this->jwtService->refreshToken($originalToken);

        $this->assertNotNull($newToken);
        $this->assertNotEquals($originalToken, $newToken);

        // Verificar que el nuevo token es válido
        $payload = $this->jwtService->validateToken($newToken);
        $this->assertNotNull($payload);
        $this->assertEquals($usuario->usuario_id, $payload['sub']);
    }

    /** @test */
    public function it_rejects_refresh_with_invalid_token(): void
    {
        $invalidToken = 'invalid.token.here';
        $newToken = $this->jwtService->refreshToken($invalidToken);

        $this->assertNull($newToken);
    }

    /** @test */
    public function token_contains_required_claims(): void
    {
        $usuario = Usuario::factory()->create([
            'correo_electronico' => 'test@example.com',
            'contrasena' => Hash::make('password123'),
            'rol' => 'cliente',
            'estado' => true,
        ]);

        $token = $this->jwtService->generateToken($usuario);
        $payload = $this->jwtService->validateToken($token);

        $this->assertArrayHasKey('sub', $payload);
        $this->assertArrayHasKey('rol', $payload);
        $this->assertArrayHasKey('email', $payload);
        $this->assertArrayHasKey('iat', $payload);
        $this->assertArrayHasKey('exp', $payload);
        $this->assertArrayHasKey('iss', $payload);
        $this->assertArrayHasKey('aud', $payload);
    }

    /** @test */
    public function token_expires_after_configured_time(): void
    {
        // Configurar expiración corta para la prueba
        config(['jwt.expiration' => 1]); // 1 segundo

        $usuario = Usuario::factory()->create([
            'correo_electronico' => 'test@example.com',
            'contrasena' => Hash::make('password123'),
            'rol' => 'cliente',
            'estado' => true,
        ]);

        $token = $this->jwtService->generateToken($usuario);

        // Verificar que el token es válido inmediatamente
        $payload = $this->jwtService->validateToken($token);
        $this->assertNotNull($payload);

        // Esperar a que expire
        sleep(2);

        // Verificar que el token expirado es rechazado
        $expiredPayload = $this->jwtService->validateToken($token);
        $this->assertNull($expiredPayload);

        // Restaurar configuración
        config(['jwt.expiration' => 3600]);
    }
}
