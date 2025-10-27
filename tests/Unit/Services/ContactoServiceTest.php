<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\ContactoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Mockery;

class ContactoServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $contactoService;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->app['config']->set('session.driver', 'array');
        $this->contactoService = new ContactoService();
        
        Mail::fake();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Test: Enviar formulario de contacto exitosamente
     */
    public function test_enviar_formulario_exitoso()
    {
        // Arrange
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('getAttribute')->andReturn(null);
        $request->nombre = 'Juan';
        $request->apellido = 'Pérez';
        $request->email = 'juan@example.com';
        $request->telefono = '1234567890';
        $request->asunto = 'Consulta sobre productos';
        $request->mensaje = 'Quisiera información sobre...';
        $request->shouldReceive('ip')->andReturn('127.0.0.1');
        $request->shouldReceive('userAgent')->andReturn('Test Browser');

        // Act
        $result = $this->contactoService->enviarFormulario($request);

        // Assert
        $this->assertTrue($result['success']);
        $this->assertStringContainsString('exitosamente', $result['message']);
        
        Mail::assertSent(\App\Mail\ContactoFormulario::class, function ($mail) {
            return $mail->hasTo('4gmoviltest@gmail.com');
        });

        Mail::assertSent(\App\Mail\ContactoConfirmacion::class, function ($mail) {
            return $mail->hasTo('juan@example.com');
        });
    }

    /**
     * Test: Enviar formulario de contacto con error
     */
    public function test_enviar_formulario_con_error()
    {
        // Arrange
        Mail::shouldReceive('to')->andThrow(new \Exception('Error de conexión SMTP'));
        
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('getAttribute')->andReturn(null);
        $request->nombre = 'Juan';
        $request->apellido = 'Pérez';
        $request->email = 'juan@example.com';
        $request->telefono = '1234567890';
        $request->asunto = 'Consulta';
        $request->mensaje = 'Mensaje de prueba';
        $request->shouldReceive('ip')->andReturn('127.0.0.1');
        $request->shouldReceive('userAgent')->andReturn('Test Browser');

        // Act
        $result = $this->contactoService->enviarFormulario($request);

        // Assert
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('error', strtolower($result['message']));
    }

    /**
     * Test: Enviar servicio técnico exitosamente con email
     */
    public function test_enviar_servicio_tecnico_con_email()
    {
        // Arrange
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('getAttribute')->andReturn(null);
        $request->name = 'María González';
        $request->phone = '0987654321';
        $request->email = 'maria@example.com';
        $request->device = 'celular';
        $request->model = 'iPhone 14';
        $request->problem = 'Pantalla rota, no enciende';
        $request->shouldReceive('ip')->andReturn('192.168.1.1');
        $request->shouldReceive('userAgent')->andReturn('Chrome Browser');

        // Act
        $result = $this->contactoService->enviarServicioTecnico($request);

        // Assert
        $this->assertTrue($result['success']);
        $this->assertStringContainsString('exitosamente', $result['message']);
        $this->assertStringContainsString('confirmación', $result['message']);
        
        Mail::assertSent(\App\Mail\ServicioTecnicoFormulario::class, function ($mail) {
            return $mail->hasTo('4gmoviltest@gmail.com');
        });

        Mail::assertSent(\App\Mail\ServicioTecnicoConfirmacion::class, function ($mail) {
            return $mail->hasTo('maria@example.com');
        });
    }

    /**
     * Test: Enviar servicio técnico exitosamente sin email
     */
    public function test_enviar_servicio_tecnico_sin_email()
    {
        // Arrange
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('getAttribute')->andReturn(null);
        $request->name = 'Carlos Ruiz';
        $request->phone = '0123456789';
        $request->email = null;
        $request->device = 'laptop';
        $request->model = 'Dell Inspiron';
        $request->problem = 'No arranca';
        $request->shouldReceive('ip')->andReturn('10.0.0.1');
        $request->shouldReceive('userAgent')->andReturn('Firefox Browser');

        // Act
        $result = $this->contactoService->enviarServicioTecnico($request);

        // Assert
        $this->assertTrue($result['success']);
        $this->assertStringContainsString('exitosamente', $result['message']);
        $this->assertStringNotContainsString('confirmación', $result['message']);
        
        Mail::assertSent(\App\Mail\ServicioTecnicoFormulario::class, 1);
        Mail::assertNotSent(\App\Mail\ServicioTecnicoConfirmacion::class);
    }

    /**
     * Test: Enviar servicio técnico con email inválido
     */
    public function test_enviar_servicio_tecnico_email_invalido()
    {
        // Arrange
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('getAttribute')->andReturn(null);
        $request->name = 'Ana López';
        $request->phone = '5555555555';
        $request->email = 'email-invalido';
        $request->device = 'tablet';
        $request->model = 'iPad Pro';
        $request->problem = 'Batería no carga';
        $request->shouldReceive('ip')->andReturn('172.16.0.1');
        $request->shouldReceive('userAgent')->andReturn('Safari Browser');

        // Act
        $result = $this->contactoService->enviarServicioTecnico($request);

        // Assert
        $this->assertTrue($result['success']);
        Mail::assertSent(\App\Mail\ServicioTecnicoFormulario::class, 1);
        Mail::assertNotSent(\App\Mail\ServicioTecnicoConfirmacion::class);
    }

    /**
     * Test: Enviar servicio técnico con error
     */
    public function test_enviar_servicio_tecnico_con_error()
    {
        // Arrange
        Mail::shouldReceive('to')->andThrow(new \Exception('Error SMTP'));

        $request = Mockery::mock(Request::class);
        $request->shouldReceive('getAttribute')->andReturn(null);
        $request->name = 'Pedro Sánchez';
        $request->phone = '9998887777';
        $request->email = 'pedro@example.com';
        $request->device = 'computadora';
        $request->model = 'HP Pavilion';
        $request->problem = 'Virus detectado';
        $request->shouldReceive('ip')->andReturn('8.8.8.8');
        $request->shouldReceive('userAgent')->andReturn('Edge Browser');

        // Act
        $result = $this->contactoService->enviarServicioTecnico($request);

        // Assert
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('error', strtolower($result['message']));
    }

    /**
     * Test: Verificar estructura de datos del formulario de contacto
     */
    public function test_estructura_datos_formulario()
    {
        // Arrange
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('getAttribute')->andReturn(null);
        $request->nombre = 'Test';
        $request->apellido = 'User';
        $request->email = 'test@example.com';
        $request->telefono = '1234567890';
        $request->asunto = 'Test Subject';
        $request->mensaje = 'Test Message';
        $request->shouldReceive('ip')->andReturn('127.0.0.1');
        $request->shouldReceive('userAgent')->andReturn('Test Agent');

        // Act
        $this->contactoService->enviarFormulario($request);

        // Assert
        Mail::assertSent(\App\Mail\ContactoFormulario::class, function ($mail) {
            return $mail->datos['nombre'] === 'Test' &&
                   $mail->datos['apellido'] === 'User' &&
                   $mail->datos['email'] === 'test@example.com' &&
                   isset($mail->datos['fecha']) &&
                   isset($mail->datos['ip']);
        });
    }

    /**
     * Test: Verificar estructura de datos del servicio técnico
     */
    public function test_estructura_datos_servicio_tecnico()
    {
        // Arrange
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('getAttribute')->andReturn(null);
        $request->name = 'Test Name';
        $request->phone = '1234567890';
        $request->email = 'test@example.com';
        $request->device = 'celular';
        $request->model = 'Test Model';
        $request->problem = 'Test Problem';
        $request->shouldReceive('ip')->andReturn('127.0.0.1');
        $request->shouldReceive('userAgent')->andReturn('Test Agent');

        // Act
        $this->contactoService->enviarServicioTecnico($request);

        // Assert
        Mail::assertSent(\App\Mail\ServicioTecnicoFormulario::class, function ($mail) {
            return $mail->datos['nombre'] === 'Test Name' &&
                   $mail->datos['telefono'] === '1234567890' &&
                   $mail->datos['email'] === 'test@example.com' &&
                   $mail->datos['dispositivo'] === 'celular' &&
                   $mail->datos['modelo'] === 'Test Model' &&
                   $mail->datos['problema'] === 'Test Problem' &&
                   isset($mail->datos['fecha']) &&
                   isset($mail->datos['ip']);
        });
    }
}

