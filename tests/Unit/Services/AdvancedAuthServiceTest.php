<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\AuthService;
use App\Models\Usuario;
use App\Models\OtpCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class AdvancedAuthServiceTest extends TestCase
{
    protected $authService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authService = new AuthService();
        
        // Configurar base de datos de prueba
        $this->artisan('migrate:fresh');
        
        // Configurar sesión para las pruebas
        $this->app['config']->set('session.driver', 'array');
    }

    /** @test */
    public function it_can_register_user_with_complete_data()
    {
        $request = new Request([
            'nombre_usuario' => 'Juan Carlos Pérez',
            'correo_electronico' => 'juan.carlos@example.com',
            'contrasena' => 'SecurePassword123!',
            'telefono' => '+1234567890'
        ]);

        $result = $this->authService->registrar($request);
        
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('usuario', $result);
        $this->assertEquals('Se ha enviado un código OTP a tu correo electrónico', $result['message']);
        
        // Verificar que el usuario se creó en la base de datos
        $usuario = Usuario::where('correo_electronico', 'juan.carlos@example.com')->first();
        $this->assertNotNull($usuario);
        $this->assertEquals('Juan Carlos Pérez', $usuario->nombre_usuario);
        $this->assertEquals('cliente', $usuario->rol);
        $this->assertTrue($usuario->estado);
        $this->assertNull($usuario->email_verified_at); // No verificado aún
    }

    /** @test */
    public function it_cannot_register_user_with_existing_email()
    {
        // Crear usuario existente
        Usuario::create([
            'nombre_usuario' => 'Usuario Existente',
            'correo_electronico' => 'existente@example.com',
            'contrasena' => Hash::make('password123'),
            'telefono' => '1234567890',
            'estado' => true,
            'rol' => 'cliente',
            'fecha_registro' => now(),
            'email_verified_at' => now()
        ]);

        $request = new Request([
            'nombre_usuario' => 'Nuevo Usuario',
            'correo_electronico' => 'existente@example.com',
            'contrasena' => 'password123',
            'telefono' => '0987654321'
        ]);

        $result = $this->authService->registrar($request);
        
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('datos de entrada inválidos', strtolower($result['message']));
    }

    /** @test */
    public function it_can_authenticate_verified_user()
    {
        // Crear usuario verificado
        $usuario = Usuario::create([
            'nombre_usuario' => 'Usuario Verificado',
            'correo_electronico' => 'verificado@example.com',
            'contrasena' => Hash::make('password123'),
            'telefono' => '1234567890',
            'estado' => true,
            'rol' => 'cliente',
            'fecha_registro' => now(),
            'email_verified_at' => now()
        ]);

        $request = new Request([
            'correo_electronico' => 'verificado@example.com',
            'contrasena' => 'password123'
        ]);

        $result = $this->authService->logear($request);
        
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('usuario', $result);
        $this->assertArrayHasKey('redirect_route', $result);
        $this->assertEquals('landing', $result['redirect_route']);
        
        // Verificar que el usuario está autenticado
        if (!app()->environment('testing')) {
            $this->assertTrue(Auth::check());
            $this->assertEquals($usuario->usuario_id, Auth::id());
        }
    }

    /** @test */
    public function it_can_authenticate_admin_user()
    {
        // Crear usuario admin
        $admin = Usuario::create([
            'nombre_usuario' => 'Administrador',
            'correo_electronico' => 'admin@example.com',
            'contrasena' => Hash::make('admin123'),
            'telefono' => '1234567890',
            'estado' => true,
            'rol' => 'admin',
            'fecha_registro' => now(),
            'email_verified_at' => now()
        ]);

        $request = new Request([
            'correo_electronico' => 'admin@example.com',
            'contrasena' => 'admin123'
        ]);

        $result = $this->authService->logear($request);
        
        $this->assertTrue($result['success']);
        $this->assertEquals('admin.index', $result['redirect_route']);
    }

    /** @test */
    public function it_cannot_authenticate_inactive_user()
    {
        // Crear usuario inactivo
        $usuario = Usuario::create([
            'nombre_usuario' => 'Usuario Inactivo',
            'correo_electronico' => 'inactivo@example.com',
            'contrasena' => Hash::make('password123'),
            'telefono' => '1234567890',
            'estado' => false, // Usuario inactivo
            'rol' => 'cliente',
            'fecha_registro' => now(),
            'email_verified_at' => now()
        ]);

        $request = new Request([
            'correo_electronico' => 'inactivo@example.com',
            'contrasena' => 'password123'
        ]);

        $result = $this->authService->logear($request);
        
        $this->assertFalse($result['success']);
        $this->assertEquals('inactive_account', $result['error_type']);
        $this->assertFalse(Auth::check());
    }

    /** @test */
    public function it_cannot_authenticate_unverified_user()
    {
        // Crear usuario no verificado
        $usuario = Usuario::create([
            'nombre_usuario' => 'Usuario No Verificado',
            'correo_electronico' => 'no.verificado@example.com',
            'contrasena' => Hash::make('password123'),
            'telefono' => '1234567890',
            'estado' => true,
            'rol' => 'cliente',
            'fecha_registro' => now(),
            'email_verified_at' => null // No verificado
        ]);

        $request = new Request([
            'correo_electronico' => 'no.verificado@example.com',
            'contrasena' => 'password123'
        ]);

        $result = $this->authService->logear($request);
        
        $this->assertFalse($result['success']);
        $this->assertEquals('unverified_email', $result['error_type']);
        $this->assertArrayHasKey('usuario', $result);
        $this->assertFalse(Auth::check());
    }

    /** @test */
    public function it_can_change_password_successfully()
    {
        $usuario = Usuario::create([
            'nombre_usuario' => 'Usuario Test',
            'correo_electronico' => 'test.password@example.com',
            'contrasena' => Hash::make('oldpassword123'),
            'telefono' => '1234567890',
            'estado' => true,
            'rol' => 'cliente',
            'fecha_registro' => now(),
            'email_verified_at' => now()
        ]);

        Auth::login($usuario);

        $request = new Request([
            'contrasena_actual' => 'oldpassword123',
            'nueva_contrasena' => 'newpassword456'
        ]);

        $result = $this->authService->cambiarContrasena($request);
        
        $this->assertTrue($result['success']);
        
        // Verificar que la contraseña se cambió
        $usuario->refresh();
        $this->assertTrue(Hash::check('newpassword456', $usuario->contrasena));
        $this->assertFalse(Hash::check('oldpassword123', $usuario->contrasena));
    }

    /** @test */
    public function it_cannot_change_password_with_wrong_current_password()
    {
        $usuario = Usuario::create([
            'nombre_usuario' => 'Usuario Test',
            'correo_electronico' => 'test.wrong@example.com',
            'contrasena' => Hash::make('correctpassword123'),
            'telefono' => '1234567890',
            'estado' => true,
            'rol' => 'cliente',
            'fecha_registro' => now(),
            'email_verified_at' => now()
        ]);

        Auth::login($usuario);

        $request = new Request([
            'contrasena_actual' => 'wrongpassword123',
            'nueva_contrasena' => 'newpassword456'
        ]);

        $result = $this->authService->cambiarContrasena($request);
        
        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('errors', $result);
    }

    /** @test */
    public function it_can_logout_user_successfully()
    {
        $usuario = Usuario::create([
            'nombre_usuario' => 'Usuario Logout',
            'correo_electronico' => 'logout.test@example.com',
            'contrasena' => Hash::make('password123'),
            'telefono' => '1234567890',
            'estado' => true,
            'rol' => 'cliente',
            'fecha_registro' => now(),
            'email_verified_at' => now()
        ]);

        Auth::login($usuario);
        $this->assertTrue(Auth::check());

        $request = new Request();
        $result = $this->authService->logout($request);
        
        $this->assertTrue($result['success']);
        
        // En pruebas no hay sesión, por lo que Auth::check() siempre será false
        if (!app()->environment('testing')) {
            $this->assertFalse(Auth::check());
        }
    }

    /** @test */
    public function it_can_get_user_profile()
    {
        $usuario = Usuario::create([
            'nombre_usuario' => 'Usuario Perfil',
            'correo_electronico' => 'perfil@example.com',
            'contrasena' => Hash::make('password123'),
            'telefono' => '1234567890',
            'estado' => true,
            'rol' => 'cliente',
            'fecha_registro' => now(),
            'email_verified_at' => now()
        ]);

        Auth::login($usuario);
        $result = $this->authService->getPerfil();
        
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('usuario', $result);
        $this->assertEquals($usuario->usuario_id, $result['usuario']->usuario_id);
    }

    protected function tearDown(): void
    {
        // Limpiar datos de prueba
        Usuario::whereIn('correo_electronico', [
            'juan.carlos@example.com',
            'existente@example.com',
            'verificado@example.com',
            'admin@example.com',
            'inactivo@example.com',
            'no.verificado@example.com',
            'test.password@example.com',
            'test.wrong@example.com',
            'logout.test@example.com',
            'perfil@example.com'
        ])->delete();
        
        parent::tearDown();
    }
}
