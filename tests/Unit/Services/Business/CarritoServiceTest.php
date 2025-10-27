<?php

namespace Tests\Unit\Services\Business;

use Tests\TestCase;
use App\Services\Business\CarritoService;
use App\Models\Producto;
use App\Models\VarianteProducto;
use App\Models\Carrito;
use App\Models\CarritoItem;
use App\Models\Usuario;
use App\Models\Categoria;
use App\Models\Marca;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;

class CarritoServiceTest extends TestCase
{
    protected $carritoService;
    protected $usuario;
    protected $producto;
    protected $variante;

    protected function setUp(): void
    {
        parent::setUp();
        $this->carritoService = new CarritoService();
        
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
            'nombre_usuario' => 'Usuario Carrito',
            'correo_electronico' => 'carrito@example.com',
            'contrasena' => Hash::make('password123'),
            'telefono' => '1234567890',
            'estado' => true,
            'rol' => 'cliente',
            'fecha_registro' => now(),
            'email_verified_at' => now()
        ]);

        // Crear producto
        $this->producto = Producto::create([
            'nombre_producto' => 'Producto Test',
            'descripcion' => 'Descripción del producto test',
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
            'nombre' => 'Variante Test',
            'codigo_color' => '#FF0000',
            'descripcion' => 'Descripción de variante test',
            'precio_adicional' => 10.00,
            'stock' => 5,
            'disponible' => true,
            'sku' => 'VAR001'
        ]);
    }

    /** @test */
    public function it_can_create_cart_service()
    {
        $this->assertInstanceOf(CarritoService::class, $this->carritoService);
    }

    /** @test */
    public function it_can_get_empty_cart_for_authenticated_user()
    {
        Auth::login($this->usuario);

        $result = $this->carritoService->getCart();

        $this->assertTrue($result['success']);
        $this->assertEquals('Carrito obtenido exitosamente', $result['message']);
        $this->assertArrayHasKey('data', $result);
        $this->assertEquals(0, $result['data']['total_items']);
        $this->assertEquals(0, $result['data']['total_precio']);
    }

    /** @test */
    public function it_can_get_empty_cart_for_session_user()
    {
        // Usuario no autenticado
        $result = $this->carritoService->getCart();

        $this->assertTrue($result['success']);
        $this->assertEquals('Carrito obtenido exitosamente', $result['message']);
        $this->assertArrayHasKey('data', $result);
        $this->assertEquals(0, $result['data']['total_items']);
        $this->assertEquals(0, $result['data']['total_precio']);
    }

    /** @test */
    public function it_can_add_product_to_authenticated_user_cart()
    {
        Auth::login($this->usuario);

        $request = new Request([
            'producto_id' => $this->producto->producto_id,
            'cantidad' => 2
        ]);

        $result = $this->carritoService->addToCart($request);

        $this->assertTrue($result['success']);
        $this->assertEquals('Producto agregado al carrito exitosamente', $result['message']);
        
        // Verificar que se creó el carrito en la base de datos
        $carrito = Carrito::where('usuario_id', $this->usuario->usuario_id)->first();
        $this->assertNotNull($carrito);
        
        // Verificar que se agregó el item
        $item = CarritoItem::where('carrito_id', $carrito->id)
            ->where('producto_id', $this->producto->producto_id)
            ->first();
        $this->assertNotNull($item);
        $this->assertEquals(2, $item->cantidad);
    }

    /** @test */
    public function it_can_add_product_to_session_cart()
    {
        // Usuario no autenticado
        $request = new Request([
            'producto_id' => $this->producto->producto_id,
            'cantidad' => 3
        ]);

        $result = $this->carritoService->addToCart($request);

        $this->assertTrue($result['success']);
        $this->assertEquals('Producto agregado al carrito exitosamente', $result['message']);
        
        // Verificar que se guardó en la sesión
        $cart = Session::get('cart', []);
        $this->assertNotEmpty($cart);
        $this->assertCount(1, $cart);
        $this->assertEquals($this->producto->producto_id, $cart[0]['producto_id']);
        $this->assertEquals(3, $cart[0]['cantidad']);
    }

    /** @test */
    public function it_can_add_product_with_variant_to_cart()
    {
        Auth::login($this->usuario);

        $request = new Request([
            'producto_id' => $this->producto->producto_id,
            'variante_id' => $this->variante->variante_id,
            'cantidad' => 1
        ]);

        $result = $this->carritoService->addToCart($request);

        $this->assertTrue($result['success']);
        
        // Verificar que se agregó con la variante
        $carrito = Carrito::where('usuario_id', $this->usuario->usuario_id)->first();
        $item = CarritoItem::where('carrito_id', $carrito->id)
            ->where('producto_id', $this->producto->producto_id)
            ->where('variante_id', $this->variante->variante_id)
            ->first();
        $this->assertNotNull($item);
        $this->assertEquals(1, $item->cantidad);
    }

    /** @test */
    public function it_can_update_cart_item_quantity()
    {
        Auth::login($this->usuario);

        // Primero agregar un producto
        $request = new Request([
            'producto_id' => $this->producto->producto_id,
            'cantidad' => 2
        ]);
        $this->carritoService->addToCart($request);

        // Obtener el item ID
        $carrito = Carrito::where('usuario_id', $this->usuario->usuario_id)->first();
        $item = CarritoItem::where('carrito_id', $carrito->id)->first();

        // Actualizar cantidad
        $updateRequest = new Request(['cantidad' => 5]);
        $result = $this->carritoService->updateCartItem($item->id, $updateRequest);

        $this->assertTrue($result['success']);
        $this->assertEquals('Carrito actualizado exitosamente', $result['message']);
        
        // Verificar que se actualizó
        $item->refresh();
        $this->assertEquals(5, $item->cantidad);
    }

    /** @test */
    public function it_can_remove_item_from_cart()
    {
        Auth::login($this->usuario);

        // Primero agregar un producto
        $request = new Request([
            'producto_id' => $this->producto->producto_id,
            'cantidad' => 2
        ]);
        $this->carritoService->addToCart($request);

        // Obtener el item ID
        $carrito = Carrito::where('usuario_id', $this->usuario->usuario_id)->first();
        $item = CarritoItem::where('carrito_id', $carrito->id)->first();

        // Eliminar item
        $result = $this->carritoService->removeFromCart($item->id);

        $this->assertTrue($result['success']);
        $this->assertEquals('Producto eliminado del carrito exitosamente', $result['message']);
        
        // Verificar que se eliminó
        $this->assertNull(CarritoItem::find($item->id));
    }

    /** @test */
    public function it_can_clear_cart()
    {
        Auth::login($this->usuario);

        // Agregar productos al carrito
        $request = new Request([
            'producto_id' => $this->producto->producto_id,
            'cantidad' => 2
        ]);
        $this->carritoService->addToCart($request);

        // Limpiar carrito
        $result = $this->carritoService->clearCart();

        $this->assertTrue($result['success']);
        $this->assertEquals('Carrito limpiado exitosamente', $result['message']);
        
        // Verificar que se limpió
        $carrito = Carrito::where('usuario_id', $this->usuario->usuario_id)->first();
        $this->assertEquals(0, $carrito->items()->count());
    }

    /** @test */
    public function it_can_get_cart_summary()
    {
        Auth::login($this->usuario);

        // Agregar productos al carrito
        $request = new Request([
            'producto_id' => $this->producto->producto_id,
            'cantidad' => 2
        ]);
        $this->carritoService->addToCart($request);

        $result = $this->carritoService->getCartSummary();

        $this->assertTrue($result['success']);
        $this->assertEquals('Resumen del carrito obtenido', $result['message']);
        $this->assertArrayHasKey('data', $result);
        $this->assertEquals(2, $result['data']['total_items']);
        $this->assertEquals(200.00, $result['data']['total_precio']); // 2 * 100.00
    }

    /** @test */
    public function it_can_sync_session_cart_with_user()
    {
        // Primero agregar productos al carrito de sesión
        $request = new Request([
            'producto_id' => $this->producto->producto_id,
            'cantidad' => 3
        ]);
        $this->carritoService->addToCart($request);

        // Verificar que está en sesión
        $sessionCart = Session::get('cart', []);
        $this->assertNotEmpty($sessionCart);

        // Ahora autenticar usuario y sincronizar
        Auth::login($this->usuario);
        $result = $this->carritoService->syncSessionCartWithUser();

        $this->assertTrue($result['success']);
        $this->assertEquals('Carrito sincronizado exitosamente', $result['message']);
        
        // Verificar que se sincronizó
        $carrito = Carrito::where('usuario_id', $this->usuario->usuario_id)->first();
        $this->assertNotNull($carrito);
        $this->assertEquals(1, $carrito->items()->count());
        
        // Verificar que se limpió la sesión
        $this->assertEmpty(Session::get('cart', []));
    }

    /** @test */
    public function it_cannot_add_product_with_insufficient_stock()
    {
        Auth::login($this->usuario);

        $request = new Request([
            'producto_id' => $this->producto->producto_id,
            'cantidad' => 15 // Más que el stock disponible (10)
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Stock insuficiente para Producto Test. Disponible: 5, Solicitado: 15');
        
        $this->carritoService->addToCart($request);
    }

    /** @test */
    public function it_cannot_add_nonexistent_product()
    {
        Auth::login($this->usuario);

        $request = new Request([
            'producto_id' => 99999, // ID que no existe
            'cantidad' => 1
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('El producto no existe');
        
        $this->carritoService->addToCart($request);
    }

    /** @test */
    public function it_cannot_add_product_with_invalid_variant()
    {
        Auth::login($this->usuario);

        $request = new Request([
            'producto_id' => $this->producto->producto_id,
            'variante_id' => 99999, // ID de variante que no existe
            'cantidad' => 1
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Datos inválidos: La variante del producto no existe');
        
        $this->carritoService->addToCart($request);
    }

    /** @test */
    public function it_can_combine_quantities_when_adding_same_product()
    {
        Auth::login($this->usuario);

        // Agregar producto por primera vez
        $request1 = new Request([
            'producto_id' => $this->producto->producto_id,
            'cantidad' => 2
        ]);
        $this->carritoService->addToCart($request1);

        // Agregar el mismo producto otra vez
        $request2 = new Request([
            'producto_id' => $this->producto->producto_id,
            'cantidad' => 3
        ]);
        $result = $this->carritoService->addToCart($request2);

        $this->assertTrue($result['success']);
        
        // Verificar que se combinaron las cantidades
        $carrito = Carrito::where('usuario_id', $this->usuario->usuario_id)->first();
        $item = CarritoItem::where('carrito_id', $carrito->id)->first();
        $this->assertEquals(5, $item->cantidad); // 2 + 3
    }

    /** @test */
    public function it_calculates_correct_total_with_variants()
    {
        Auth::login($this->usuario);

        // Agregar producto con variante
        $request = new Request([
            'producto_id' => $this->producto->producto_id,
            'variante_id' => $this->variante->variante_id,
            'cantidad' => 2
        ]);
        $this->carritoService->addToCart($request);

        $result = $this->carritoService->getCartSummary();

        $this->assertTrue($result['success']);
        // Precio base (100) + precio adicional variante (10) = 110 * cantidad (2) = 220
        $this->assertEquals(220.00, $result['data']['total_precio']);
    }

    protected function tearDown(): void
    {
        // Limpiar datos de prueba
        CarritoItem::truncate();
        Carrito::truncate();
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
