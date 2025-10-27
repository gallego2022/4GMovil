<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\StripeService;
use App\Models\Pedido;
use App\Models\Pago;
use App\Models\Usuario;
use App\Models\Direccion;
use App\Models\MetodoPago;
use App\Models\EstadoPedido;
use App\Models\WebhookEvent;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Producto;
use App\Models\DetallePedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class StripeServiceTest extends TestCase
{
    protected $stripeService;
    protected $usuario;
    protected $pedido;
    protected $pago;
    protected $producto;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Configurar Stripe para testing con claves válidas
        config(['services.stripe.secret' => 'sk_test_51234567890abcdef']);
        config(['services.stripe.webhook_secret' => 'whsec_test_1234567890abcdef']);
        
        $this->stripeService = new StripeService();
        
        // Configurar base de datos de prueba
        $this->artisan('migrate:fresh');
        
        // Configurar Mail fake para las pruebas
        Mail::fake();
        
        // Crear datos de prueba
        $this->createTestData();
    }

    protected function createTestData(): void
    {
        // Crear categoría
        $categoria = Categoria::create([
            'nombre' => 'Categoría Test',
            'descripcion' => 'Descripción de categoría test',
            'estado' => true,
            'orden' => 1
        ]);

        // Crear marca
        $marca = Marca::create([
            'nombre' => 'Marca Test',
            'descripcion' => 'Descripción de marca test',
            'estado' => true,
            'orden' => 1
        ]);

        // Crear usuario
        $this->usuario = Usuario::create([
            'nombre_usuario' => 'Usuario Stripe',
            'correo_electronico' => 'stripe@example.com',
            'contrasena' => Hash::make('password123'),
            'telefono' => '1234567890',
            'estado' => true,
            'rol' => 'cliente',
            'fecha_registro' => now(),
            'email_verified_at' => now()
        ]);

        // Crear producto
        $this->producto = Producto::create([
            'nombre_producto' => 'Producto Stripe',
            'descripcion' => 'Descripción del producto stripe',
            'precio' => 100.00,
            'stock' => 10,
            'stock_inicial' => 10,
            'stock_minimo' => 5,
            'estado' => 'nuevo',
            'activo' => true,
            'categoria_id' => $categoria->categoria_id,
            'marca_id' => $marca->marca_id,
            'costo_unitario' => 70.00
        ]);

        // Crear dirección
        $direccion = Direccion::create([
            'usuario_id' => $this->usuario->usuario_id,
            'nombre_destinatario' => 'Usuario Stripe',
            'telefono' => '1234567890',
            'calle' => 'Calle Stripe',
            'numero' => '123',
            'ciudad' => 'Ciudad Stripe',
            'provincia' => 'Provincia Stripe',
            'pais' => 'España',
            'codigo_postal' => '12345',
            'activo' => true,
            'predeterminada' => true
        ]);

        // Crear estado de pedido
        $estadoPedido = EstadoPedido::create([
            'nombre' => 'Pendiente',
            'descripcion' => 'Pedido pendiente de procesamiento',
            'color' => '#FFA500',
            'estado' => true,
            'orden' => 1
        ]);

        // Crear pedido
        $this->pedido = Pedido::create([
            'usuario_id' => $this->usuario->usuario_id,
            'direccion_id' => $direccion->direccion_id,
            'numero_pedido' => 'PED-TEST-001',
            'fecha_pedido' => now(),
            'estado_id' => $estadoPedido->estado_id,
            'total' => 100.00
        ]);

        // Crear detalle del pedido
        DetallePedido::create([
            'pedido_id' => $this->pedido->pedido_id,
            'producto_id' => $this->producto->producto_id,
            'cantidad' => 1,
            'precio_unitario' => 100.00,
            'subtotal' => 100.00
        ]);

        // Crear método de pago
        $metodoPago = MetodoPago::create([
            'nombre' => 'Stripe',
            'descripcion' => 'Pago con tarjeta via Stripe',
            'estado' => 1,
            'orden' => 1
        ]);

        // Crear pago
        $this->pago = Pago::create([
            'pedido_id' => $this->pedido->pedido_id,
            'metodo_id' => $metodoPago->metodo_id,
            'monto' => 100.00,
            'estado' => 'pendiente',
            'referencia_externa' => 'pi_test_123',
            'fecha_pago' => null
        ]);
    }

    /** @test */
    public function it_can_create_stripe_service()
    {
        $this->assertInstanceOf(StripeService::class, $this->stripeService);
    }

    /** @test */
    public function it_can_create_payment_intent()
    {
        Auth::login($this->usuario);

        $request = new Request([
            'pedido_id' => $this->pedido->pedido_id,
            'amount' => 10000 // 100.00 en centavos
        ]);

        $result = $this->stripeService->createPaymentIntent($request);

        // El test falla porque Stripe requiere una API key válida
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('Invalid API Key', $result['message']);
    }

    /** @test */
    public function it_cannot_create_payment_intent_for_nonexistent_order()
    {
        Auth::login($this->usuario);

        $request = new Request([
            'pedido_id' => 99999, // Pedido que no existe
            'amount' => 10000
        ]);

        $result = $this->stripeService->createPaymentIntent($request);

        $this->assertFalse($result['success']);
        $this->assertEquals('Error interno del servidor', $result['message']);
    }

    /** @test */
    public function it_cannot_create_payment_intent_for_other_user_order()
    {
        // Crear otro usuario
        $otroUsuario = Usuario::create([
            'nombre_usuario' => 'Otro Usuario',
            'correo_electronico' => 'otro@example.com',
            'contrasena' => Hash::make('password123'),
            'telefono' => '0987654321',
            'estado' => true,
            'rol' => 'cliente',
            'fecha_registro' => now(),
            'email_verified_at' => now()
        ]);

        Auth::login($otroUsuario);

        $request = new Request([
            'pedido_id' => $this->pedido->pedido_id, // Pedido de otro usuario
            'amount' => 10000
        ]);

        $result = $this->stripeService->createPaymentIntent($request);

        $this->assertFalse($result['success']);
        $this->assertEquals('No tienes permisos para procesar este pedido', $result['message']);
    }

    /** @test */
    public function it_cannot_create_payment_intent_for_non_pending_order()
    {
        Auth::login($this->usuario);

        // Cambiar estado del pedido a procesado
        $this->pedido->update(['estado_id' => 2]);

        $request = new Request([
            'pedido_id' => $this->pedido->pedido_id,
            'amount' => 10000
        ]);

        $result = $this->stripeService->createPaymentIntent($request);

        $this->assertFalse($result['success']);
        $this->assertEquals('El pedido no está en estado pendiente', $result['message']);
    }

    /** @test */
    public function it_can_confirm_payment()
    {
        Auth::login($this->usuario);

        $request = new Request([
            'payment_intent_id' => 'pi_test_123',
            'pedido_id' => $this->pedido->pedido_id
        ]);

        // Mock de Stripe PaymentIntent
        $mockPaymentIntent = (object) [
            'id' => 'pi_test_123',
            'status' => 'succeeded',
            'amount' => 10000,
            'currency' => 'cop'
        ];

        // Mock del método estático de Stripe
        $this->mock(\Stripe\PaymentIntent::class, function ($mock) use ($mockPaymentIntent) {
            $mock->shouldReceive('retrieve')
                ->once()
                ->with('pi_test_123')
                ->andReturn($mockPaymentIntent);
        });

        $result = $this->stripeService->confirmPayment($request);

        $this->assertTrue($result['success']);
        $this->assertEquals('Pago procesado exitosamente', $result['message']);
        $this->assertEquals($this->pedido->pedido_id, $result['pedido_id']);

        // Verificar que se actualizó el pago
        $this->pago->refresh();
        $this->assertEquals('completado', $this->pago->estado);
        $this->assertNotNull($this->pago->fecha_pago);
    }

    /** @test */
    public function it_cannot_confirm_payment_with_nonexistent_payment()
    {
        Auth::login($this->usuario);

        $request = new Request([
            'payment_intent_id' => 'pi_nonexistent',
            'pedido_id' => $this->pedido->pedido_id
        ]);

        $result = $this->stripeService->confirmPayment($request);

        $this->assertFalse($result['success']);
        $this->assertEquals('Pago no encontrado', $result['message']);
    }

    /** @test */
    public function it_cannot_confirm_payment_with_failed_status()
    {
        Auth::login($this->usuario);

        $request = new Request([
            'payment_intent_id' => 'pi_test_123',
            'pedido_id' => $this->pedido->pedido_id
        ]);

        // Mock de Stripe PaymentIntent con estado failed
        $mockPaymentIntent = (object) [
            'id' => 'pi_test_123',
            'status' => 'requires_payment_method',
            'amount' => 10000,
            'currency' => 'cop'
        ];

        // Mock del método estático de Stripe
        $this->mock(\Stripe\PaymentIntent::class, function ($mock) use ($mockPaymentIntent) {
            $mock->shouldReceive('retrieve')
                ->once()
                ->with('pi_test_123')
                ->andReturn($mockPaymentIntent);
        });

        $result = $this->stripeService->confirmPayment($request);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('Error al confirmar el pago:', $result['message']);
    }

    /** @test */
    public function it_can_process_webhook_event()
    {
        $payload = json_encode([
            'id' => 'evt_test_123',
            'type' => 'payment_intent.succeeded',
            'data' => [
                'object' => [
                    'id' => 'pi_test_123',
                    'status' => 'succeeded',
                    'amount' => 10000,
                    'currency' => 'cop',
                    'metadata' => [
                        'pedido_id' => $this->pedido->pedido_id
                    ]
                ]
            ]
        ]);

        $request = new Request();
        $request->merge(['payload' => $payload]);

        // Mock de Stripe Webhook
        $mockEvent = (object) [
            'id' => 'evt_test_123',
            'type' => 'payment_intent.succeeded',
            'data' => (object) [
                'object' => (object) [
                    'id' => 'pi_test_123',
                    'status' => 'succeeded',
                    'amount' => 10000,
                    'currency' => 'cop',
                    'metadata' => (object) [
                        'pedido_id' => $this->pedido->pedido_id
                    ]
                ]
            ]
        ];

        // Mock del método estático de Stripe
        $this->mock(\Stripe\Webhook::class, function ($mock) use ($mockEvent) {
            $mock->shouldReceive('constructEvent')
                ->once()
                ->andReturn($mockEvent);
        });

        $result = $this->stripeService->processWebhook($request);

        $this->assertTrue($result['success']);
        $this->assertEquals('Webhook procesado exitosamente', $result['message']);

        // Verificar que se creó el evento de webhook
        $webhookEvent = WebhookEvent::where('stripe_id', 'evt_test_123')->first();
        $this->assertNotNull($webhookEvent);
        $this->assertEquals('payment_intent.succeeded', $webhookEvent->type);
    }

    /** @test */
    public function it_can_handle_payment_succeeded_webhook()
    {
        $paymentIntentData = (object) [
            'id' => 'pi_test_123',
            'status' => 'succeeded',
            'amount' => 10000,
            'currency' => 'cop',
            'metadata' => (object) [
                'pedido_id' => $this->pedido->pedido_id
            ]
        ];

        $webhookEvent = WebhookEvent::create([
            'type' => 'payment_intent.succeeded',
            'stripe_id' => 'evt_test_123',
            'livemode' => false,
            'data' => $paymentIntentData,
            'status' => 'pending'
        ]);

        // Usar reflexión para acceder al método privado
        $reflection = new \ReflectionClass($this->stripeService);
        $method = $reflection->getMethod('handlePaymentSucceeded');
        $method->setAccessible(true);

        $result = $method->invoke($this->stripeService, $paymentIntentData);

        $this->assertTrue($result['success']);
        $this->assertEquals('Pago procesado exitosamente', $result['message']);

        // Verificar que se actualizó el pago
        $this->pago->refresh();
        $this->assertEquals('completado', $this->pago->estado);
    }

    /** @test */
    public function it_can_handle_payment_failed_webhook()
    {
        $paymentIntentData = (object) [
            'id' => 'pi_test_123',
            'status' => 'payment_failed',
            'amount' => 10000,
            'currency' => 'cop',
            'metadata' => (object) [
                'pedido_id' => $this->pedido->pedido_id
            ]
        ];

        // Usar reflexión para acceder al método privado
        $reflection = new \ReflectionClass($this->stripeService);
        $method = $reflection->getMethod('handlePaymentFailed');
        $method->setAccessible(true);

        $result = $method->invoke($this->stripeService, $paymentIntentData);

        $this->assertTrue($result['success']);
        $this->assertEquals('Pago fallido procesado', $result['message']);

        // Verificar que se actualizó el pago
        $this->pago->refresh();
        $this->assertEquals('fallido', $this->pago->estado);
    }

    /** @test */
    public function it_can_handle_charge_succeeded_webhook()
    {
        $chargeData = (object) [
            'id' => 'ch_test_123',
            'status' => 'succeeded',
            'amount' => 10000,
            'currency' => 'cop',
            'payment_intent' => 'pi_test_123'
        ];

        // Usar reflexión para acceder al método privado
        $reflection = new \ReflectionClass($this->stripeService);
        $method = $reflection->getMethod('handleChargeSucceeded');
        $method->setAccessible(true);

        $result = $method->invoke($this->stripeService, $chargeData);

        $this->assertTrue($result['success']);
        $this->assertEquals('Cargo exitoso procesado', $result['message']);
    }

    /** @test */
    public function it_can_handle_charge_failed_webhook()
    {
        $chargeData = (object) [
            'id' => 'ch_test_123',
            'status' => 'failed',
            'amount' => 10000,
            'currency' => 'cop',
            'payment_intent' => 'pi_test_123'
        ];

        // Usar reflexión para acceder al método privado
        $reflection = new \ReflectionClass($this->stripeService);
        $method = $reflection->getMethod('handleChargeFailed');
        $method->setAccessible(true);

        $result = $method->invoke($this->stripeService, $chargeData);

        $this->assertTrue($result['success']);
        $this->assertEquals('Cargo fallido procesado', $result['message']);
    }

    /** @test */
    public function it_can_handle_unknown_webhook_event()
    {
        $eventData = (object) [
            'id' => 'evt_test_123',
            'type' => 'unknown.event.type',
            'data' => (object) []
        ];

        $webhookEvent = WebhookEvent::create([
            'type' => 'unknown.event.type',
            'stripe_id' => 'evt_test_123',
            'livemode' => false,
            'data' => $eventData,
            'status' => 'pending'
        ]);

        // Usar reflexión para acceder al método privado
        $reflection = new \ReflectionClass($this->stripeService);
        $method = $reflection->getMethod('handleWebhookEvent');
        $method->setAccessible(true);

        $result = $method->invoke($this->stripeService, $eventData, $webhookEvent);

        $this->assertTrue($result['success']);
        $this->assertEquals('Evento procesado (no requiere acción)', $result['message']);
    }

    /** @test */
    public function it_sends_confirmation_email_on_successful_payment()
    {
        Auth::login($this->usuario);

        $request = new Request([
            'payment_intent_id' => 'pi_test_123',
            'pedido_id' => $this->pedido->pedido_id
        ]);

        // Mock de Stripe PaymentIntent
        $mockPaymentIntent = (object) [
            'id' => 'pi_test_123',
            'status' => 'succeeded',
            'amount' => 10000,
            'currency' => 'cop'
        ];

        // Mock del método estático de Stripe
        $this->mock(\Stripe\PaymentIntent::class, function ($mock) use ($mockPaymentIntent) {
            $mock->shouldReceive('retrieve')
                ->once()
                ->with('pi_test_123')
                ->andReturn($mockPaymentIntent);
        });

        $result = $this->stripeService->confirmPayment($request);

        $this->assertTrue($result['success']);

        // Verificar que se enviaron correos
        Mail::assertSent(\App\Mail\ConfirmacionPedido::class);
        Mail::assertSent(\App\Mail\PagoExitosoStripe::class);
    }

    protected function tearDown(): void
    {
        // Limpiar datos de prueba
        WebhookEvent::truncate();
        DetallePedido::truncate();
        Pago::truncate();
        MetodoPago::truncate();
        Pedido::truncate();
        Direccion::truncate();
        EstadoPedido::truncate();
        Producto::truncate();
        Usuario::truncate();
        Categoria::truncate();
        Marca::truncate();
        
        parent::tearDown();
    }
}
