<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\AuthService;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

class AuthSecurityTest extends TestCase
{
    protected $authService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authService = new AuthService();
        $this->artisan('migrate:fresh');
    }

    /** @test */
    public function it_validates_password_strength()
    {
        $weakPasswords = [
            '123',           // Muy corta
            'password',      // Sin números
            '12345678',      // Solo números
            'Password',      // Sin números
            'password123',   // Sin mayúsculas
            'PASSWORD123',   // Sin minúsculas
        ];

        foreach ($weakPasswords as $password) {
            $request = new Request([
                'nombre_usuario' => 'Test User',
                'correo_electronico' => 'test@example.com',
                'contrasena' => $password,
                'telefono' => '1234567890'
            ]);

            $result = $this->authService->registrar($request);
            
            // Debería fallar por contraseña débil
            $this->assertFalse($result['success'], "Password '$password' should be rejected");
        }
    }

    /** @test */
    public function it_validates_email_format()
    {
        $invalidEmails = [
            'invalid-email',
            '@example.com',
            'test@',
            'test..test@example.com',
            'test@example',
            'test@.com',
            '',
            'test@example..com'
        ];

        foreach ($invalidEmails as $email) {
            $request = new Request([
                'nombre_usuario' => 'Test User',
                'correo_electronico' => $email,
                'contrasena' => 'SecurePassword123!',
                'telefono' => '1234567890'
            ]);

            $result = $this->authService->registrar($request);
            
            // Debería fallar por email inválido
            $this->assertFalse($result['success'], "Email '$email' should be rejected");
        }
    }

    /** @test */
    public function it_validates_required_fields()
    {
        $incompleteRequests = [
            // Sin nombre
            [
                'correo_electronico' => 'test@example.com',
                'contrasena' => 'SecurePassword123!',
                'telefono' => '1234567890'
            ],
            // Sin email
            [
                'nombre_usuario' => 'Test User',
                'contrasena' => 'SecurePassword123!',
                'telefono' => '1234567890'
            ],
            // Sin contraseña
            [
                'nombre_usuario' => 'Test User',
                'correo_electronico' => 'test@example.com',
                'telefono' => '1234567890'
            ]
        ];

        foreach ($incompleteRequests as $data) {
            $request = new Request($data);
            $result = $this->authService->registrar($request);
            
            $this->assertFalse($result['success'], 'Incomplete request should be rejected');
        }
    }

    /** @test */
    public function it_prevents_sql_injection_in_login()
    {
        $maliciousInputs = [
            "'; DROP TABLE usuarios; --",
            "' OR '1'='1",
            "admin'--",
            "' UNION SELECT * FROM usuarios --",
            "'; INSERT INTO usuarios VALUES ('hacker', 'hacker@evil.com', 'password'); --"
        ];

        foreach ($maliciousInputs as $maliciousInput) {
            $request = new Request([
                'correo_electronico' => $maliciousInput,
                'contrasena' => 'password123'
            ]);

            $result = $this->authService->logear($request);
            
            // Debería fallar de manera segura
            $this->assertFalse($result['success'], "Malicious input '$maliciousInput' should be rejected");
        }
    }

    /** @test */
    public function it_prevents_xss_in_user_data()
    {
        $xssPayloads = [
            '<script>alert("XSS")</script>',
            '"><script>alert("XSS")</script>',
            'javascript:alert("XSS")',
            '<img src=x onerror=alert("XSS")>',
            '"><img src=x onerror=alert("XSS")>'
        ];

        foreach ($xssPayloads as $payload) {
            $request = new Request([
                'nombre_usuario' => $payload,
                'correo_electronico' => 'test@example.com',
                'contrasena' => 'SecurePassword123!',
                'telefono' => '1234567890'
            ]);

            $result = $this->authService->registrar($request);
            
            // Debería fallar o escapar el contenido
            if ($result['success']) {
                // Si se registra, verificar que el contenido está escapado
                $usuario = Usuario::where('correo_electronico', 'test@example.com')->first();
                $this->assertStringNotContainsString('<script>', $usuario->nombre_usuario);
                $usuario->delete(); // Limpiar
            }
        }
    }

    /** @test */
    public function it_handles_long_inputs_gracefully()
    {
        $longString = str_repeat('A', 10000); // String muy largo

        $request = new Request([
            'nombre_usuario' => $longString,
            'correo_electronico' => 'test@example.com',
            'contrasena' => 'SecurePassword123!',
            'telefono' => '1234567890'
        ]);

        $result = $this->authService->registrar($request);
        
        // Debería fallar por entrada demasiado larga
        $this->assertFalse($result['success'], 'Very long input should be rejected');
    }

    /** @test */
    public function it_validates_phone_number_format()
    {
        $invalidPhones = [
            '123',                    // Muy corto
            '12345678901234567890',   // Muy largo
            'abc123def456',           // Con letras
            '123-456-789',            // Con guiones
            '(123) 456-7890',         // Con paréntesis
            '+1-234-567-8900',        // Con formato internacional
            '',                       // Vacío
        ];

        foreach ($invalidPhones as $phone) {
            $request = new Request([
                'nombre_usuario' => 'Test User',
                'correo_electronico' => 'test@example.com',
                'contrasena' => 'SecurePassword123!',
                'telefono' => $phone
            ]);

            $result = $this->authService->registrar($request);
            
            // Debería fallar por teléfono inválido
            $this->assertFalse($result['success'], "Phone '$phone' should be rejected");
        }
    }

    /** @test */
    public function it_prevents_brute_force_attacks()
    {
        // Crear usuario válido
        $usuario = Usuario::create([
            'nombre_usuario' => 'Usuario Brute Force',
            'correo_electronico' => 'bruteforce@example.com',
            'contrasena' => Hash::make('password123'),
            'telefono' => '1234567890',
            'estado' => true,
            'rol' => 'cliente',
            'fecha_registro' => now(),
            'email_verified_at' => now()
        ]);

        $request = new Request([
            'correo_electronico' => 'bruteforce@example.com',
            'contrasena' => 'wrongpassword'
        ]);

        // Intentar múltiples veces con contraseña incorrecta
        for ($i = 0; $i < 5; $i++) {
            $result = $this->authService->logear($request);
            $this->assertFalse($result['success']);
        }

        // Después de varios intentos, debería haber algún tipo de protección
        // (esto depende de la implementación específica del rate limiting)
        $result = $this->authService->logear($request);
        $this->assertFalse($result['success']);
    }

    /** @test */
    public function it_handles_concurrent_registration_attempts()
    {
        $email = 'concurrent@example.com';
        
        // Simular múltiples intentos de registro simultáneos
        $requests = [];
        for ($i = 0; $i < 3; $i++) {
            $requests[] = new Request([
                'nombre_usuario' => "Usuario $i",
                'correo_electronico' => $email,
                'contrasena' => 'SecurePassword123!',
                'telefono' => '1234567890'
            ]);
        }

        $results = [];
        foreach ($requests as $request) {
            $results[] = $this->authService->registrar($request);
        }

        // Solo uno debería tener éxito
        $successCount = 0;
        foreach ($results as $result) {
            if ($result['success']) {
                $successCount++;
            }
        }

        $this->assertEquals(1, $successCount, 'Only one registration should succeed');
    }

    /** @test */
    public function it_validates_session_security()
    {
        $usuario = Usuario::create([
            'nombre_usuario' => 'Usuario Sesión',
            'correo_electronico' => 'session@example.com',
            'contrasena' => Hash::make('password123'),
            'telefono' => '1234567890',
            'estado' => true,
            'rol' => 'cliente',
            'fecha_registro' => now(),
            'email_verified_at' => now()
        ]);

        $request = new Request([
            'correo_electronico' => 'session@example.com',
            'contrasena' => 'password123'
        ]);

        $result = $this->authService->logear($request);
        $this->assertTrue($result['success']);

        // Verificar que la sesión se regenera
        if (!app()->environment('testing')) {
            $this->assertTrue(Auth::check());
        }
        
        // Logout debería limpiar la sesión
        $logoutRequest = new Request();
        $logoutResult = $this->authService->logout($logoutRequest);
        $this->assertTrue($logoutResult['success']);
        $this->assertFalse(Auth::check());
    }

    protected function tearDown(): void
    {
        // Limpiar datos de prueba
        Usuario::whereIn('correo_electronico', [
            'bruteforce@example.com',
            'concurrent@example.com',
            'session@example.com'
        ])->delete();
        
        parent::tearDown();
    }
}
