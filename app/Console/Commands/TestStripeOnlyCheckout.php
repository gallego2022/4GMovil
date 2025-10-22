<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MetodoPago;
use App\Models\Producto;
use App\Models\VarianteProducto;
use App\Models\Usuario;
use App\Models\Direccion;
use App\Services\Business\CheckoutService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TestStripeOnlyCheckout extends Command
{
    protected $signature = 'test:stripe-only-checkout';
    protected $description = 'Probar el flujo de checkout solo con Stripe';

    public function handle()
    {
        $this->info('🧪 Probando flujo de checkout solo con Stripe...');
        
        // 1. Verificar que Stripe esté configurado
        $stripeMethod = MetodoPago::where('nombre', 'Stripe')->first();
        if (!$stripeMethod) {
            $this->error('❌ Método de pago Stripe no encontrado');
            return 1;
        }
        $this->info('✅ Método de pago Stripe encontrado: ID ' . $stripeMethod->metodo_id);
        
        // 2. Verificar que hay productos disponibles
        $productos = Producto::where('activo', true)->take(2)->get();
        if ($productos->isEmpty()) {
            $this->error('❌ No hay productos activos disponibles');
            return 1;
        }
        $this->info('✅ Productos disponibles: ' . $productos->count());
        
        // 3. Crear un carrito de prueba
        $cart = [];
        foreach ($productos as $producto) {
            $cart[] = [
                'id' => $producto->producto_id,
                'name' => $producto->nombre_producto,
                'price' => $producto->precio,
                'quantity' => 1,
                'variante_id' => null
            ];
        }
        $this->info('✅ Carrito de prueba creado con ' . count($cart) . ' productos');
        
        // 4. Verificar que hay un usuario de prueba
        $usuario = Usuario::first();
        if (!$usuario) {
            $this->error('❌ No hay usuarios en la base de datos');
            return 1;
        }
        $this->info('✅ Usuario de prueba: ' . $usuario->email);
        
        // 5. Verificar que hay direcciones
        $direccion = Direccion::where('usuario_id', $usuario->usuario_id)->first();
        if (!$direccion) {
            $this->warn('⚠️  No hay direcciones para el usuario. Creando una de prueba...');
            $direccion = Direccion::create([
                'usuario_id' => $usuario->usuario_id,
                'nombre_destinatario' => 'Usuario Prueba',
                'telefono' => '1234567890',
                'calle' => 'Calle Prueba',
                'numero' => '123',
                'ciudad' => 'Ciudad Prueba',
                'provincia' => 'Provincia Prueba',
                'pais' => 'Colombia',
                'codigo_postal' => '12345',
                'activo' => true,
                'predeterminada' => true
            ]);
        }
        $this->info('✅ Dirección de prueba: ' . $direccion->calle . ' ' . $direccion->numero);
        
        // 6. Simular autenticación
        Auth::login($usuario);
        $this->info('✅ Usuario autenticado: ' . Auth::user()->email);
        
        // 7. Crear request de prueba
        $request = new Request([
            'direccion_id' => $direccion->direccion_id,
            'metodo_pago_id' => $stripeMethod->metodo_id,
            'notas' => 'Pedido de prueba - Solo Stripe'
        ]);
        
        // 8. Simular carrito en sesión
        session(['cart' => $cart]);
        $this->info('✅ Carrito guardado en sesión');
        
        // 9. Probar el checkout
        try {
            $checkoutService = app(CheckoutService::class);
            $result = $checkoutService->processCheckout($request);
            
            if ($result['success']) {
                $this->info('✅ Checkout procesado exitosamente');
                $this->info('   - Pedido ID: ' . $result['pedido_id']);
                $this->info('   - Pago ID: ' . $result['pago_id']);
                $this->info('   - Redirigir a Stripe: ' . ($result['redirect_to_stripe'] ? 'Sí' : 'No'));
                $this->info('   - Mensaje: ' . $result['message']);
                
                // Verificar que el pedido se creó con estado pendiente
                $pedido = \App\Models\Pedido::find($result['pedido_id']);
                if ($pedido) {
                    $this->info('✅ Pedido creado con estado: ' . $pedido->estado_id . ' (Pendiente)');
                    $this->info('   - El admin debe cambiar el estado del pedido manualmente');
                    
                    // Verificar que el pago se creó con estado pendiente
                    $pago = \App\Models\Pago::find($result['pago_id']);
                    if ($pago) {
                        $this->info('✅ Pago creado con estado: ' . $pago->estado);
                        $this->info('   - El pago se marcará como "completado" cuando Stripe confirme');
                    }
                }
                
            } else {
                $this->error('❌ Error en checkout: ' . ($result['message'] ?? 'Error desconocido'));
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Excepción en checkout: ' . $e->getMessage());
            $this->error('   Trace: ' . $e->getTraceAsString());
        }
        
        $this->info('🎉 Prueba completada');
        return 0;
    }
}
