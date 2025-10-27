<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\AuthService;
use App\Services\OtpService;
use App\Models\Usuario;
use App\Models\OtpCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthPerformanceTest extends TestCase
{
    protected $authService;
    protected $otpService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authService = new AuthService();
        $this->otpService = new OtpService();
        
        // Configurar base de datos de prueba
        $this->artisan('migrate:fresh');
        
        // Configurar sesión para las pruebas
        $this->app['config']->set('session.driver', 'array');
        
        // Mock Mail para evitar envío real de emails
        Mail::fake();
        
        // Limpiar cache
        Cache::flush();
    }

    /** @test */
    public function it_can_handle_multiple_concurrent_logins()
    {
        // Crear usuario de prueba
        $usuario = Usuario::create([
            'nombre_usuario' => 'Usuario Concurrente',
            'correo_electronico' => 'concurrent@example.com',
            'contrasena' => Hash::make('password123'),
            'telefono' => '1234567890',
            'estado' => true,
            'rol' => 'cliente',
            'fecha_registro' => now(),
            'email_verified_at' => now()
        ]);

        $startTime = microtime(true);
        
        // Simular múltiples intentos de login concurrentes
        $requests = [];
        for ($i = 0; $i < 10; $i++) {
            $requests[] = new Request([
                'correo_electronico' => 'concurrent@example.com',
                'contrasena' => 'password123'
            ]);
        }

        $results = [];
        foreach ($requests as $request) {
            $results[] = $this->authService->logear($request);
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        // Verificar que todos los logins fueron exitosos
        foreach ($results as $result) {
            $this->assertTrue($result['success']);
        }

        // Verificar que el tiempo de ejecución es razonable (menos de 5 segundos para 10 logins)
        $this->assertLessThan(5.0, $executionTime, "El tiempo de ejecución fue demasiado largo: {$executionTime}s");
        
        Log::info("Tiempo de ejecución para 10 logins concurrentes: {$executionTime}s");
    }

    /** @test */
    public function it_can_handle_large_number_of_users()
    {
        $startTime = microtime(true);
        
        // Crear 100 usuarios de prueba
        $usuarios = [];
        for ($i = 0; $i < 100; $i++) {
            $usuarios[] = Usuario::create([
                'nombre_usuario' => "Usuario {$i}",
                'correo_electronico' => "user{$i}@example.com",
                'contrasena' => Hash::make('password123'),
                'telefono' => '1234567890',
                'estado' => true,
                'rol' => 'cliente',
                'fecha_registro' => now(),
                'email_verified_at' => now()
            ]);
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        // Verificar que se crearon todos los usuarios
        $this->assertEquals(100, Usuario::count());

        // Verificar que el tiempo de ejecución es razonable (menos de 10 segundos para 100 usuarios)
        $this->assertLessThan(10.0, $executionTime, "El tiempo de creación fue demasiado largo: {$executionTime}s");
        
        Log::info("Tiempo de creación para 100 usuarios: {$executionTime}s");
    }

    /** @test */
    public function it_can_handle_rapid_password_changes()
    {
        // Crear usuario de prueba
        $usuario = Usuario::create([
            'nombre_usuario' => 'Usuario Password',
            'correo_electronico' => 'password@example.com',
            'contrasena' => Hash::make('password123'),
            'telefono' => '1234567890',
            'estado' => true,
            'rol' => 'cliente',
            'fecha_registro' => now(),
            'email_verified_at' => now()
        ]);

        $startTime = microtime(true);
        
        // Simular múltiples cambios de contraseña
        $passwords = [
            'NewPassword123!',
            'AnotherPassword456@',
            'ThirdPassword789#',
            'FourthPassword012$',
            'FifthPassword345%'
        ];

        foreach ($passwords as $index => $newPassword) {
            $request = new Request([
                'contrasena_actual' => $index === 0 ? 'password123' : $passwords[$index - 1],
                'nueva_contrasena' => $newPassword
            ]);

            // Simular usuario autenticado
            Auth::login($usuario);
            
            $result = $this->authService->cambiarContrasena($request);
            $this->assertTrue($result['success']);
            
            // Actualizar la contraseña en el objeto usuario para el siguiente cambio
            $usuario->contrasena = Hash::make($newPassword);
            $usuario->save();
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        // Verificar que el tiempo de ejecución es razonable (menos de 3 segundos para 5 cambios)
        $this->assertLessThan(3.0, $executionTime, "El tiempo de cambios de contraseña fue demasiado largo: {$executionTime}s");
        
        Log::info("Tiempo de ejecución para 5 cambios de contraseña: {$executionTime}s");
    }

    /** @test */
    public function it_can_handle_bulk_user_registrations()
    {
        $startTime = microtime(true);
        
        // Simular registro masivo de usuarios
        $registrations = [];
        for ($i = 0; $i < 50; $i++) {
            $request = new Request([
                'nombre_usuario' => "Usuario Registro {$i}",
                'correo_electronico' => "registro{$i}@example.com",
                'contrasena' => 'SecurePassword123!',
                'telefono' => '1234567890'
            ]);

            $result = $this->authService->registrar($request);
            $registrations[] = $result;
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        // Verificar que todos los registros fueron exitosos
        foreach ($registrations as $result) {
            $this->assertTrue($result['success']);
        }

        // Verificar que se crearon todos los usuarios
        $this->assertEquals(50, Usuario::count());

        // Verificar que el tiempo de ejecución es razonable (menos de 15 segundos para 50 registros)
        $this->assertLessThan(15.0, $executionTime, "El tiempo de registro masivo fue demasiado largo: {$executionTime}s");
        
        Log::info("Tiempo de ejecución para 50 registros masivos: {$executionTime}s");
    }

    /** @test */
    public function it_can_handle_database_query_performance()
    {
        // Crear usuarios de prueba
        for ($i = 0; $i < 1000; $i++) {
            Usuario::create([
                'nombre_usuario' => "Usuario Query {$i}",
                'correo_electronico' => "query{$i}@example.com",
                'contrasena' => Hash::make('password123'),
                'telefono' => '1234567890',
                'estado' => true,
                'rol' => 'cliente',
                'fecha_registro' => now(),
                'email_verified_at' => now()
            ]);
        }

        $startTime = microtime(true);
        
        // Realizar consultas complejas
        $activeUsers = Usuario::where('estado', true)->count();
        $verifiedUsers = Usuario::whereNotNull('email_verified_at')->count();
        $clientUsers = Usuario::where('rol', 'cliente')->count();
        
        // Consulta con joins (simulando consulta de perfil)
        $usersWithProfiles = Usuario::with(['direcciones'])
            ->where('estado', true)
            ->limit(100)
            ->get();

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        // Verificar resultados
        $this->assertEquals(1000, $activeUsers);
        $this->assertEquals(1000, $verifiedUsers);
        $this->assertEquals(1000, $clientUsers);
        $this->assertCount(100, $usersWithProfiles);

        // Verificar que el tiempo de ejecución es razonable (menos de 2 segundos)
        $this->assertLessThan(2.0, $executionTime, "El tiempo de consultas fue demasiado largo: {$executionTime}s");
        
        Log::info("Tiempo de ejecución para consultas complejas: {$executionTime}s");
    }

    /** @test */
    public function it_can_handle_otp_generation_performance()
    {
        // Crear usuario de prueba
        $usuario = Usuario::create([
            'nombre_usuario' => 'Usuario OTP',
            'correo_electronico' => 'otp@example.com',
            'contrasena' => Hash::make('password123'),
            'telefono' => '1234567890',
            'estado' => true,
            'rol' => 'cliente',
            'fecha_registro' => now(),
            'email_verified_at' => now()
        ]);

        $startTime = microtime(true);
        
        // Generar múltiples códigos OTP
        $otpCodes = [];
        for ($i = 0; $i < 100; $i++) {
            $otpCode = OtpCode::create([
                'usuario_id' => $usuario->usuario_id,
                'codigo' => OtpCode::generarCodigo(),
                'tipo' => 'email_verification',
                'fecha_expiracion' => now()->addMinutes(10),
                'usado' => false
            ]);
            $otpCodes[] = $otpCode;
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        // Verificar que se crearon todos los códigos
        $this->assertCount(100, $otpCodes);
        $this->assertEquals(100, OtpCode::count());

        // Verificar que el tiempo de ejecución es razonable (menos de 5 segundos para 100 códigos)
        $this->assertLessThan(5.0, $executionTime, "El tiempo de generación de OTP fue demasiado largo: {$executionTime}s");
        
        Log::info("Tiempo de ejecución para 100 códigos OTP: {$executionTime}s");
    }

    /** @test */
    public function it_can_handle_memory_usage_efficiently()
    {
        $initialMemory = memory_get_usage();
        
        // Crear muchos usuarios para probar el uso de memoria
        $usuarios = [];
        for ($i = 0; $i < 500; $i++) {
            $usuarios[] = Usuario::create([
                'nombre_usuario' => "Usuario Memoria {$i}",
                'correo_electronico' => "memoria{$i}@example.com",
                'contrasena' => Hash::make('password123'),
                'telefono' => '1234567890',
                'estado' => true,
                'rol' => 'cliente',
                'fecha_registro' => now(),
                'email_verified_at' => now()
            ]);
        }

        $peakMemory = memory_get_peak_usage();
        $memoryUsed = $peakMemory - $initialMemory;
        $memoryUsedMB = $memoryUsed / 1024 / 1024;

        // Verificar que el uso de memoria es razonable (menos de 50MB para 500 usuarios)
        $this->assertLessThan(50, $memoryUsedMB, "El uso de memoria fue demasiado alto: {$memoryUsedMB}MB");
        
        Log::info("Uso de memoria para 500 usuarios: {$memoryUsedMB}MB");
    }

    /** @test */
    public function it_can_handle_concurrent_otp_requests()
    {
        // Crear usuario de prueba
        $usuario = Usuario::create([
            'nombre_usuario' => 'Usuario Concurrent OTP',
            'correo_electronico' => 'concurrentotp@example.com',
            'contrasena' => Hash::make('password123'),
            'telefono' => '1234567890',
            'estado' => true,
            'rol' => 'cliente',
            'fecha_registro' => now(),
            'email_verified_at' => now()
        ]);

        $startTime = microtime(true);
        
        // Simular múltiples solicitudes de OTP concurrentes
        $requests = [];
        for ($i = 0; $i < 20; $i++) {
            $requests[] = new Request([
                'email' => 'concurrentotp@example.com'
            ]);
        }

        $results = [];
        foreach ($requests as $request) {
            $results[] = $this->otpService->sendPasswordResetOtp($request);
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        // Verificar que el tiempo de ejecución es razonable (menos de 10 segundos para 20 solicitudes)
        $this->assertLessThan(10.0, $executionTime, "El tiempo de solicitudes OTP concurrentes fue demasiado largo: {$executionTime}s");
        
        Log::info("Tiempo de ejecución para 20 solicitudes OTP concurrentes: {$executionTime}s");
    }

    /** @test */
    public function it_can_handle_large_profile_updates()
    {
        // Crear usuario de prueba
        $usuario = Usuario::create([
            'nombre_usuario' => 'Usuario Profile',
            'correo_electronico' => 'profile@example.com',
            'contrasena' => Hash::make('password123'),
            'telefono' => '1234567890',
            'estado' => true,
            'rol' => 'cliente',
            'fecha_registro' => now(),
            'email_verified_at' => now()
        ]);

        $startTime = microtime(true);
        
        // Simular múltiples actualizaciones de perfil
        for ($i = 0; $i < 50; $i++) {
            $request = new Request([
                'nombre_usuario' => "Usuario Profile Actualizado {$i}",
                'correo_electronico' => "profile{$i}@example.com",
                'telefono' => '1234567890'
            ]);

            // Simular usuario autenticado
            Auth::login($usuario);
            
            $result = $this->authService->actualizarPerfil($request);
            $this->assertTrue($result['success']);
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        // Verificar que el tiempo de ejecución es razonable (menos de 5 segundos para 50 actualizaciones)
        $this->assertLessThan(5.0, $executionTime, "El tiempo de actualizaciones de perfil fue demasiado largo: {$executionTime}s");
        
        Log::info("Tiempo de ejecución para 50 actualizaciones de perfil: {$executionTime}s");
    }

    /** @test */
    public function it_can_handle_database_connection_pooling()
    {
        $startTime = microtime(true);
        
        // Simular múltiples conexiones a la base de datos
        $connections = [];
        for ($i = 0; $i < 10; $i++) {
            $connections[] = DB::connection();
        }

        // Realizar operaciones en diferentes conexiones
        foreach ($connections as $index => $connection) {
            $connection->table('usuarios')->insert([
                'nombre_usuario' => "Usuario Conexión {$index}",
                'correo_electronico' => "conexion{$index}@example.com",
                'contrasena' => Hash::make('password123'),
                'telefono' => '1234567890',
                'estado' => true,
                'rol' => 'cliente',
                'fecha_registro' => now(),
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        // Verificar que se crearon todos los usuarios
        $this->assertEquals(10, Usuario::count());

        // Verificar que el tiempo de ejecución es razonable (menos de 3 segundos)
        $this->assertLessThan(3.0, $executionTime, "El tiempo de conexiones múltiples fue demasiado largo: {$executionTime}s");
        
        Log::info("Tiempo de ejecución para 10 conexiones múltiples: {$executionTime}s");
    }

    protected function tearDown(): void
    {
        // Limpiar datos de prueba
        Usuario::truncate();
        OtpCode::truncate();
        
        // Limpiar cache
        Cache::flush();
        
        parent::tearDown();
    }
}
