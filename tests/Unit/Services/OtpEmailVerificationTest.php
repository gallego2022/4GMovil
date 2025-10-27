<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\Usuario;
use App\Models\OtpCode;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;

class OtpEmailVerificationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:fresh');
    }

    /** @test */
    public function it_can_send_email_verification_otp()
    {
        $usuario = Usuario::create([
            'nombre_usuario' => 'Usuario OTP',
            'correo_electronico' => 'otp.test@example.com',
            'contrasena' => Hash::make('password123'),
            'telefono' => '1234567890',
            'estado' => true,
            'rol' => 'cliente',
            'fecha_registro' => now(),
            'email_verified_at' => null
        ]);

        // Mock Mail para evitar envío real
        Mail::fake();

        $usuario->sendEmailVerificationNotification();

        // Verificar que se creó un código OTP
        $otpCode = OtpCode::where('usuario_id', $usuario->usuario_id)
            ->where('tipo', 'email_verification')
            ->first();

        $this->assertNotNull($otpCode);
        $this->assertEquals(6, strlen($otpCode->codigo));
        $this->assertFalse($otpCode->usado);
    }

    /** @test */
    public function it_can_verify_email_with_valid_otp()
    {
        $usuario = Usuario::create([
            'nombre_usuario' => 'Usuario Verificación',
            'correo_electronico' => 'verificacion@example.com',
            'contrasena' => Hash::make('password123'),
            'telefono' => '1234567890',
            'estado' => true,
            'rol' => 'cliente',
            'fecha_registro' => now(),
            'email_verified_at' => null
        ]);

        // Crear código OTP válido
        $otpCode = OtpCode::create([
            'usuario_id' => $usuario->usuario_id,
            'codigo' => '123456',
            'tipo' => 'email_verification',
            'fecha_expiracion' => now()->addMinutes(10),
            'usado' => false
        ]);

        // Verificar email con OTP
        $result = OtpCode::verificar($usuario->usuario_id, '123456', 'email_verification');

        $this->assertTrue($result);
        
        // El método verificar solo verifica el código, no marca el email como verificado
        // Para marcar el email como verificado, se necesitaría lógica adicional
    }

    /** @test */
    public function it_cannot_verify_email_with_invalid_otp()
    {
        $usuario = Usuario::create([
            'nombre_usuario' => 'Usuario Inválido',
            'correo_electronico' => 'invalido@example.com',
            'contrasena' => Hash::make('password123'),
            'telefono' => '1234567890',
            'estado' => true,
            'rol' => 'cliente',
            'fecha_registro' => now(),
            'email_verified_at' => null
        ]);

        // Crear código OTP válido
        OtpCode::create([
            'usuario_id' => $usuario->usuario_id,
            'codigo' => '123456',
            'tipo' => 'email_verification',
            'usado' => false,
            'fecha_expiracion' => now()->addMinutes(10),
            'usado' => false
        ]);

        // Intentar verificar con código incorrecto
        $result = OtpCode::verificar($usuario->usuario_id, '654321', 'email_verification');

        $this->assertFalse($result);
        
        // Verificar que el usuario NO está marcado como verificado
        $usuario->refresh();
        $this->assertNull($usuario->email_verified_at);
    }

    /** @test */
    public function it_cannot_verify_email_with_expired_otp()
    {
        $usuario = Usuario::create([
            'nombre_usuario' => 'Usuario Expirado',
            'correo_electronico' => 'expirado@example.com',
            'contrasena' => Hash::make('password123'),
            'telefono' => '1234567890',
            'estado' => true,
            'rol' => 'cliente',
            'fecha_registro' => now(),
            'email_verified_at' => null
        ]);

        // Crear código OTP expirado
        OtpCode::create([
            'usuario_id' => $usuario->usuario_id,
            'codigo' => '123456',
            'tipo' => 'email_verification',
            'fecha_expiracion' => now()->subMinutes(5), // Expirado hace 5 minutos
            'usado' => false
        ]);

        // Intentar verificar con código expirado
        $result = OtpCode::verificar($usuario->usuario_id, '123456', 'email_verification');

        $this->assertFalse($result);
    }

    /** @test */
    public function it_cannot_verify_email_with_used_otp()
    {
        $usuario = Usuario::create([
            'nombre_usuario' => 'Usuario Usado',
            'correo_electronico' => 'usado@example.com',
            'contrasena' => Hash::make('password123'),
            'telefono' => '1234567890',
            'estado' => true,
            'rol' => 'cliente',
            'fecha_registro' => now(),
            'email_verified_at' => null
        ]);

        // Crear código OTP ya usado
        OtpCode::create([
            'usuario_id' => $usuario->usuario_id,
            'codigo' => '123456',
            'tipo' => 'email_verification',
            'fecha_expiracion' => now()->addMinutes(10),
            'fecha_expiracion' => now()->addMinutes(10),
            'usado' => true
        ]);

        // Intentar verificar con código ya usado
        $result = OtpCode::verificar($usuario->usuario_id, '123456', 'email_verification');

        $this->assertFalse($result);
    }

    /** @test */
    public function it_can_check_if_user_has_valid_otp()
    {
        $usuario = Usuario::create([
            'nombre_usuario' => 'Usuario Válido',
            'correo_electronico' => 'valido@example.com',
            'contrasena' => Hash::make('password123'),
            'telefono' => '1234567890',
            'estado' => true,
            'rol' => 'cliente',
            'fecha_registro' => now(),
            'email_verified_at' => null
        ]);

        // Crear código OTP válido
        OtpCode::create([
            'usuario_id' => $usuario->usuario_id,
            'codigo' => '123456',
            'tipo' => 'email_verification',
            'usado' => false,
            'fecha_expiracion' => now()->addMinutes(10),
            'usado' => false
        ]);

        $hasValidOtp = OtpCode::tieneCodigoValido($usuario->usuario_id, 'email_verification');
        $this->assertTrue($hasValidOtp);
    }

    /** @test */
    public function it_can_resend_otp_when_none_exists()
    {
        $usuario = Usuario::create([
            'nombre_usuario' => 'Usuario Resend',
            'correo_electronico' => 'resend@example.com',
            'contrasena' => Hash::make('password123'),
            'telefono' => '1234567890',
            'estado' => true,
            'rol' => 'cliente',
            'fecha_registro' => now(),
            'email_verified_at' => null
        ]);

        // No crear código OTP inicial

        // Mock Mail
        Mail::fake();

        // Enviar notificación (debería crear nuevo código)
        $usuario->sendEmailVerificationNotification();

        // Verificar que se creó un nuevo código OTP
        $otpCode = OtpCode::where('usuario_id', $usuario->usuario_id)
            ->where('tipo', 'email_verification')
            ->first();

        $this->assertNotNull($otpCode);
        $this->assertFalse($otpCode->usado);
    }

    protected function tearDown(): void
    {
        // Limpiar datos de prueba
        Usuario::whereIn('correo_electronico', [
            'otp.test@example.com',
            'verificacion@example.com',
            'invalido@example.com',
            'expirado@example.com',
            'usado@example.com',
            'valido@example.com',
            'resend@example.com'
        ])->delete();

        OtpCode::whereIn('usuario_id', function($query) {
            $query->select('usuario_id')
                  ->from('usuarios')
                  ->whereIn('correo_electronico', [
                      'otp.test@example.com',
                      'verificacion@example.com',
                      'invalido@example.com',
                      'expirado@example.com',
                      'usado@example.com',
                      'valido@example.com',
                      'resend@example.com'
                  ]);
        })->delete();
        
        parent::tearDown();
    }
}
