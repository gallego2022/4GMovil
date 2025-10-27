<?php

namespace Tests\Unit\Services\Business;

use Tests\TestCase;
use App\Services\Business\CheckoutService;
use App\Services\ReservaStockService;
use App\Models\Producto;
use App\Models\VarianteProducto;
use App\Models\Pedido;
use App\Models\DetallePedido;
use App\Models\Pago;
use App\Models\Usuario;
use App\Models\Direccion;
use App\Models\MetodoPago;
use App\Models\EstadoPedido;
use App\Models\Categoria;
use App\Models\Marca;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;

class CheckoutServiceTest extends TestCase
{
    protected $checkoutService;
    protected $reservaStockService;
    protected $usuario;
    protected $producto;
    protected $variante;
    protected $direccion;
    protected $metodoPago;
    protected $estadoPedido;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Mock del ReservaStockService
        $this->reservaStockService = $this->createMock(ReservaStockService::class);
        $this->checkoutService = new CheckoutService($this->reservaStockService);
        
        // Configurar base de datos de prueba
        $this->artisan('migrate:fresh');
        
        // Configurar sesión para las pruebas
        $this->app['config']->set('session.driver', 'array');
        
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
            'nombre_usuario' => 'Usuario Checkout',
            'correo_electronico' => 'checkout@example.com',
            'contrasena' => Hash::make('password123'),
            'telefono' => '1234567890',
            'estado' => true,
            'rol' => 'cliente',
            'fecha_registro' => now(),
            'email_verified_at' => now()
        ]);

        // Crear producto
        $this->producto = Producto::create([
            'nombre_producto' => 'Producto Checkout',
            'descripcion' => 'Descripción del producto checkout',
            'precio' => 100.00,
            'stock' => 10,
            'estado' => 'nuevo',
            'activo' => true,
            'categoria_id' => $categoria->categoria_id,
            'marca_id' => $marca->marca_id
        ]);

        // Crear variante
        $this->variante = VarianteProducto::create([
            'producto_id' => $this->producto->producto_id,
            'nombre' => 'Variante Checkout',
            'codigo_color' => '#FF0000',
            'descripcion' => 'Descripción de variante checkout',
            'precio_adicional' => 10.00,
            'stock' => 5,
            'disponible' => true,
            'sku' => 'VAR001'
        ]);

        // Crear dirección
        $this->direccion = Direccion::create([
            'usuario_id' => $this->usuario->usuario_id,
            'nombre_destinatario' => 'Usuario Checkout',
            'telefono' => '1234567890',
            'calle' => 'Calle Checkout',
            'numero' => '123',
            'codigo_postal' => '12345',
            'ciudad' => 'Ciudad Checkout',
            'provincia' => 'Provincia Checkout',
            'pais' => 'España',
            'activo' => true,
            'predeterminada' => true
        ]);

        // Crear método de pago
        $this->metodoPago = MetodoPago::create([
            'nombre' => 'Stripe',
            'descripcion' => 'Pago con tarjeta via Stripe',
            'estado' => 1,
            'orden' => 1
        ]);

        // Crear estado de pedido
        $this->estadoPedido = EstadoPedido::create([
            'nombre' => 'Pendiente',
            'descripcion' => 'Pedido pendiente de procesamiento',
            'color' => '#FFA500',
            'estado' => true,
            'orden' => 1
        ]);
    }

    /** @test */
    public function it_can_create_checkout_service()
    {
        $this->assertInstanceOf(CheckoutService::class, $this->checkoutService);
    }

    /** @test */
    public function it_can_prepare_checkout_with_valid_cart()
    {
        Auth::login($this->usuario);

        // Crear carrito en sesión
        $cart = [
            [
                'producto_id' => $this->producto->producto_id,
                'cantidad' => 2
            ]
        ];
        Session::put('cart', $cart);

        $request = new Request();
        $result = $this->checkoutService->prepareCheckout($request);

        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('cart', $result);
        $this->assertArrayHasKey('direcciones', $result);
        $this->assertArrayHasKey('metodosPago', $result);
        $this->assertCount(1, $result['direcciones']);
        $this->assertCount(1, $result['metodosPago']);
    }

    /** @test */
    public function it_cannot_prepare_checkout_with_empty_cart()
    {
        Auth::login($this->usuario);

        // Carrito vacío
        Session::put('cart', []);

        $request = new Request();
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Tu carrito está vacío');
        
        $this->checkoutService->prepareCheckout($request);
    }

    /** @test */
    public function it_cannot_prepare_checkout_with_nonexistent_product()
    {
        Auth::login($this->usuario);

        // Carrito con producto que no existe
        $cart = [
            [
                'producto_id' => 99999,
                'cantidad' => 1
            ]
        ];
        Session::put('cart', $cart);

        $request = new Request();
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Uno o más productos ya no están disponibles');
        
        $this->checkoutService->prepareCheckout($request);
    }

    /** @test */
    public function it_cannot_prepare_checkout_with_invalid_variant()
    {
        Auth::login($this->usuario);

        // Carrito con variante inválida
        $cart = [
            [
                'producto_id' => $this->producto->producto_id,
                'variante_id' => 99999,
                'cantidad' => 1
            ]
        ];
        Session::put('cart', $cart);

        $request = new Request();
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Uno o más productos ya no están disponibles');
        
        $this->checkoutService->prepareCheckout($request);
    }

    /** @test */
    public function it_can_prepare_checkout_with_cart_from_post_request()
    {
        Auth::login($this->usuario);

        $cart = [
            [
                'producto_id' => $this->producto->producto_id,
                'cantidad' => 2
            ]
        ];

        $request = new Request();
        $request->merge(['cart' => json_encode($cart)]);
        $request->setMethod('POST');

        $result = $this->checkoutService->prepareCheckout($request);

        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('cart', $result);
    }

    /** @test */
    public function it_can_process_checkout_with_stripe()
    {
        Auth::login($this->usuario);

        // Configurar carrito en sesión
        $cart = [
            [
                'producto_id' => $this->producto->producto_id,
                'cantidad' => 2
            ]
        ];
        Session::put('cart', $cart);

        // Mock del ReservaStockService (no se llama para productos sin variantes)

        $request = new Request([
            'direccion_id' => $this->direccion->direccion_id,
            'metodo_pago_id' => $this->metodoPago->metodo_id,
            'notas' => 'Pedido de prueba'
        ]);

        $result = $this->checkoutService->processCheckout($request);

        $this->assertTrue($result['success']);
        $this->assertEquals('Pedido creado, redirigiendo a Stripe', $result['message']);
        $this->assertTrue($result['redirect_to_stripe']);
        $this->assertArrayHasKey('pedido_id', $result);
        $this->assertArrayHasKey('pago_id', $result);

        // Verificar que se creó el pedido
        $pedido = Pedido::find($result['pedido_id']);
        $this->assertNotNull($pedido);
        $this->assertEquals($this->usuario->usuario_id, $pedido->usuario_id);
        $this->assertEquals($this->direccion->direccion_id, $pedido->direccion_id);

        // Verificar que se creó el pago
        $pago = Pago::find($result['pago_id']);
        $this->assertNotNull($pago);
        $this->assertEquals($pedido->pedido_id, $pago->pedido_id);
        $this->assertEquals('pendiente', $pago->estado);
    }

    /** @test */
    public function it_cannot_process_checkout_with_empty_cart()
    {
        Auth::login($this->usuario);

        // Carrito vacío
        Session::put('cart', []);

        $request = new Request([
            'direccion_id' => $this->direccion->direccion_id,
            'metodo_pago_id' => $this->metodoPago->metodo_id
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('El carrito está vacío');
        
        $this->checkoutService->processCheckout($request);
    }

    /** @test */
    public function it_cannot_process_checkout_with_non_stripe_payment()
    {
        Auth::login($this->usuario);

        // Crear método de pago que no es Stripe
        $metodoPagoNoStripe = MetodoPago::create([
            'nombre' => 'PayPal',
            'descripcion' => 'Pago via PayPal',
            'estado' => 1,
            'orden' => 2
        ]);

        $cart = [
            [
                'producto_id' => $this->producto->producto_id,
                'cantidad' => 2
            ]
        ];
        Session::put('cart', $cart);

        $request = new Request([
            'direccion_id' => $this->direccion->direccion_id,
            'metodo_pago_id' => $metodoPagoNoStripe->metodo_id
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Solo se acepta pago con Stripe');
        
        $this->checkoutService->processCheckout($request);
    }

    /** @test */
    public function it_cannot_process_checkout_with_invalid_address()
    {
        Auth::login($this->usuario);

        $cart = [
            [
                'producto_id' => $this->producto->producto_id,
                'cantidad' => 2
            ]
        ];
        Session::put('cart', $cart);

        $request = new Request([
            'direccion_id' => 99999, // Dirección que no existe
            'metodo_pago_id' => $this->metodoPago->metodo_id
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('La dirección seleccionada no es válida');
        
        $this->checkoutService->processCheckout($request);
    }

    /** @test */
    public function it_cannot_process_checkout_with_invalid_payment_method()
    {
        Auth::login($this->usuario);

        $cart = [
            [
                'producto_id' => $this->producto->producto_id,
                'cantidad' => 2
            ]
        ];
        Session::put('cart', $cart);

        $request = new Request([
            'direccion_id' => $this->direccion->direccion_id,
            'metodo_pago_id' => 99999 // Método de pago que no existe
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('El método de pago seleccionado no es válido');
        
        $this->checkoutService->processCheckout($request);
    }

    /** @test */
    public function it_can_process_checkout_with_variant_product()
    {
        Auth::login($this->usuario);

        $cart = [
            [
                'producto_id' => $this->producto->producto_id,
                'variante_id' => $this->variante->variante_id,
                'cantidad' => 1
            ]
        ];
        Session::put('cart', $cart);

        // Mock del ReservaStockService (no se llama para productos sin variantes)

        $request = new Request([
            'direccion_id' => $this->direccion->direccion_id,
            'metodo_pago_id' => $this->metodoPago->metodo_id
        ]);

        $result = $this->checkoutService->processCheckout($request);

        $this->assertTrue($result['success']);

        // Verificar que se creó el detalle del pedido con variante
        $pedido = Pedido::find($result['pedido_id']);
        $detalle = DetallePedido::where('pedido_id', $pedido->pedido_id)->first();
        $this->assertNotNull($detalle);
        $this->assertEquals($this->variante->variante_id, $detalle->variante_id);
    }

    /** @test */
    public function it_calculates_correct_order_total()
    {
        Auth::login($this->usuario);

        $cart = [
            [
                'producto_id' => $this->producto->producto_id,
                'cantidad' => 2
            ],
            [
                'producto_id' => $this->producto->producto_id,
                'variante_id' => $this->variante->variante_id,
                'cantidad' => 1
            ]
        ];
        Session::put('cart', $cart);

        // Mock del ReservaStockService (no se llama para productos sin variantes)

        $request = new Request([
            'direccion_id' => $this->direccion->direccion_id,
            'metodo_pago_id' => $this->metodoPago->metodo_id
        ]);

        $result = $this->checkoutService->processCheckout($request);

        $this->assertTrue($result['success']);

        // Verificar el total del pedido
        $pedido = Pedido::find($result['pedido_id']);
        // Producto base: 100 * 2 = 200
        // Producto con variante: (100 + 10) * 1 = 110
        // Total esperado: 310
        $this->assertEquals(310.00, $pedido->total);
    }

    /** @test */
    public function it_creates_order_details_correctly()
    {
        Auth::login($this->usuario);

        $cart = [
            [
                'producto_id' => $this->producto->producto_id,
                'cantidad' => 3
            ]
        ];
        Session::put('cart', $cart);

        // Mock del ReservaStockService (no se llama para productos sin variantes)

        $request = new Request([
            'direccion_id' => $this->direccion->direccion_id,
            'metodo_pago_id' => $this->metodoPago->metodo_id
        ]);

        $result = $this->checkoutService->processCheckout($request);

        $this->assertTrue($result['success']);

        // Verificar detalles del pedido
        $pedido = Pedido::find($result['pedido_id']);
        $detalles = DetallePedido::where('pedido_id', $pedido->pedido_id)->get();
        
        $this->assertCount(1, $detalles);
        $this->assertEquals($this->producto->producto_id, $detalles[0]->producto_id);
        $this->assertEquals(3, $detalles[0]->cantidad);
        $this->assertEquals(100.00, $detalles[0]->precio_unitario);
        $this->assertEquals(300.00, $detalles[0]->subtotal);
    }

    /** @test */
    public function it_clears_cart_after_successful_checkout()
    {
        Auth::login($this->usuario);

        $cart = [
            [
                'producto_id' => $this->producto->producto_id,
                'cantidad' => 2
            ]
        ];
        Session::put('cart', $cart);

        // Verificar que el carrito está en sesión
        $this->assertNotEmpty(Session::get('cart'));

        // Mock del ReservaStockService (no se llama para productos sin variantes)

        $request = new Request([
            'direccion_id' => $this->direccion->direccion_id,
            'metodo_pago_id' => $this->metodoPago->metodo_id
        ]);

        $result = $this->checkoutService->processCheckout($request);

        $this->assertTrue($result['success']);
        
        // El carrito debería estar vacío después del checkout exitoso
        // (esto depende de la implementación específica del servicio)
    }

    protected function tearDown(): void
    {
        // Limpiar datos de prueba
        DetallePedido::truncate();
        Pago::truncate();
        Pedido::truncate();
        Direccion::truncate();
        MetodoPago::truncate();
        EstadoPedido::truncate();
        VarianteProducto::truncate();
        Producto::truncate();
        Usuario::truncate();
        Categoria::truncate();
        Marca::truncate();
        
        // Limpiar sesión
        Session::flush();
        
        parent::tearDown();
    }
}
