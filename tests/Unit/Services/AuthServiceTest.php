<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\AuthService;
use App\Services\LoggingService;
use App\Services\CacheService;
use App\Services\ValidationService;
use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;


class AuthServiceTest extends TestCase
{

    protected AuthService $authService;
    protected LoggingService $loggingService;
    protected CacheService $cacheService;
    protected ValidationService $validationService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loggingService = new LoggingService();
        $this->cacheService = new CacheService($this->loggingService);
        $this->validationService = new ValidationService($this->loggingService);
        $this->authService = new AuthService($this->loggingService, $this->cacheService, $this->validationService);
    }

    /** @test */
    public function it_can_register_new_user()
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        $result = $this->authService->registerUser($userData);
        
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('user', $result);
        $this->assertArrayHasKey('verification_token', $result);
        $this->assertEquals('Usuario registrado exitosamente', $result['message']);
    }

    /** @test */
    public function it_cannot_register_user_with_existing_email()
    {
        // Crear usuario existente
        Usuario::factory()->create(['email' => 'john@example.com']);

        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        $result = $this->authService->registerUser($userData);
        
        $this->assertFalse($result['success']);
        $this->assertEquals('El usuario ya existe con este email', $result['error']);
    }

    /** @test */
    public function it_can_authenticate_user()
    {
        $user = Usuario::factory()->create([
            'email' => 'john@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now()
        ]);

        $credentials = [
            'email' => 'john@example.com',
            'password' => 'password123'
        ];

        $result = $this->authService->authenticateUser($credentials);
        
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('user', $result);
        $this->assertArrayHasKey('session_token', $result);
        $this->assertEquals('Autenticación exitosa', $result['message']);
    }

    /** @test */
    public function it_cannot_authenticate_user_with_invalid_credentials()
    {
        $credentials = [
            'email' => 'john@example.com',
            'password' => 'wrongpassword'
        ];

        $result = $this->authService->authenticateUser($credentials);
        
        $this->assertFalse($result['success']);
        $this->assertEquals('Credenciales inválidas', $result['error']);
    }

    /** @test */
    public function it_cannot_authenticate_unverified_user()
    {
        $user = Usuario::factory()->create([
            'email' => 'john@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => null
        ]);

        $credentials = [
            'email' => 'john@example.com',
            'password' => 'password123'
        ];

        $result = $this->authService->authenticateUser($credentials);
        
        $this->assertFalse($result['success']);
        $this->assertEquals('Debe verificar su email antes de iniciar sesión', $result['error']);
    }

    /** @test */
    public function it_can_logout_user()
    {
        $user = Usuario::factory()->create();
        Auth::login($user);

        $result = $this->authService->logoutUser();
        
        $this->assertTrue($result['success']);
        $this->assertEquals('Sesión cerrada exitosamente', $result['message']);
    }

    /** @test */
    public function it_can_verify_email_with_valid_token()
    {
        $user = Usuario::factory()->create([
            'email_verification_token' => 'valid_token_123'
        ]);

        $result = $this->authService->verifyEmail('valid_token_123');
        
        $this->assertTrue($result['success']);
        $this->assertEquals('Email verificado exitosamente', $result['message']);
        $this->assertNotNull($result['user']->email_verified_at);
    }

    /** @test */
    public function it_cannot_verify_email_with_invalid_token()
    {
        $result = $this->authService->verifyEmail('invalid_token');
        
        $this->assertFalse($result['success']);
        $this->assertEquals('Token de verificación inválido', $result['error']);
    }

    /** @test */
    public function it_can_request_password_reset()
    {
        $user = Usuario::factory()->create(['email' => 'john@example.com']);

        $result = $this->authService->requestPasswordReset('john@example.com');
        
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('token', $result);
        $this->assertArrayHasKey('reset_link', $result);
        $this->assertEquals('Se ha enviado un enlace de restablecimiento a su email', $result['message']);
    }

    /** @test */
    public function it_cannot_request_password_reset_for_nonexistent_user()
    {
        $result = $this->authService->requestPasswordReset('nonexistent@example.com');
        
        $this->assertFalse($result['success']);
        $this->assertEquals('No existe un usuario con este email', $result['error']);
    }

    /** @test */
    public function it_can_reset_password_with_valid_token()
    {
        $user = Usuario::factory()->create(['email' => 'john@example.com']);
        $token = 'valid_reset_token';

        Password::shouldReceive('tokenExists')
            ->once()
            ->with($user, $token)
            ->andReturn(true);

        Password::shouldReceive('deleteToken')
            ->once()
            ->with($user);

        $result = $this->authService->resetPassword($token, 'john@example.com', 'newpassword123');
        
        $this->assertTrue($result['success']);
        $this->assertEquals('Contraseña restablecida exitosamente', $result['message']);
    }

    /** @test */
    public function it_cannot_reset_password_with_invalid_token()
    {
        $user = Usuario::factory()->create(['email' => 'john@example.com']);
        $token = 'invalid_reset_token';

        Password::shouldReceive('tokenExists')
            ->once()
            ->with($user, $token)
            ->andReturn(false);

        $result = $this->authService->resetPassword($token, 'john@example.com', 'newpassword123');
        
        $this->assertFalse($result['success']);
        $this->assertEquals('Token de restablecimiento inválido', $result['error']);
    }

    /** @test */
    public function it_can_change_password()
    {
        $user = Usuario::factory()->create([
            'password' => Hash::make('oldpassword123')
        ]);

        $result = $this->authService->changePassword($user, 'oldpassword123', 'newpassword123');
        
        $this->assertTrue($result['success']);
        $this->assertEquals('Contraseña cambiada exitosamente', $result['message']);
    }

    /** @test */
    public function it_cannot_change_password_with_wrong_current_password()
    {
        $user = Usuario::factory()->create([
            'password' => Hash::make('oldpassword123')
        ]);

        $result = $this->authService->changePassword($user, 'wrongpassword', 'newpassword123');
        
        $this->assertFalse($result['success']);
        $this->assertEquals('La contraseña actual es incorrecta', $result['error']);
    }

    /** @test */
    public function it_can_verify_session_token()
    {
        $user = Usuario::factory()->create();
        $token = 'valid_session_token';

        $this->cacheService->set("session_token_{$user->id}", $token, 3600);

        $result = $this->authService->verifySessionToken($user, $token);
        
        $this->assertTrue($result);
    }

    /** @test */
    public function it_can_get_authenticated_user()
    {
        $user = Usuario::factory()->create();
        Auth::login($user);

        $result = $this->authService->getAuthenticatedUser();
        
        $this->assertEquals($user->id, $result->id);
    }

    /** @test */
    public function it_can_check_if_user_is_authenticated()
    {
        $this->assertFalse($this->authService->isAuthenticated());

        $user = Usuario::factory()->create();
        Auth::login($user);

        $this->assertTrue($this->authService->isAuthenticated());
    }

    /** @test */
    public function it_can_check_if_user_is_admin()
    {
        $regularUser = Usuario::factory()->create(['role' => 'user']);
        $adminUser = Usuario::factory()->create(['role' => 'admin']);

        $this->assertFalse($this->authService->isAdmin($regularUser));
        $this->assertTrue($this->authService->isAdmin($adminUser));
    }

    /** @test */
    public function it_can_get_user_permissions()
    {
        $user = Usuario::factory()->create(['permissions' => ['read', 'write']]);

        $result = $this->authService->getUserPermissions($user);
        
        $this->assertIsArray($result);
        $this->assertContains('read', $result);
        $this->assertContains('write', $result);
    }
}
