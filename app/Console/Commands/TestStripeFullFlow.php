<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pedido;
use App\Models\Usuario;
use App\Models\Producto;
use App\Models\DetallePedido;
use Illuminate\Support\Facades\DB;

class TestStripeFullFlow extends Command
{
    protected $signature = 'stripe:test-full-flow {--user=4} {--amount=70000}';
    protected $description = 'Probar el flujo completo de Stripe desde checkout hasta webhook';

    public function handle()
    {
        $this->info('🧪 Probando flujo completo de Stripe...');
        
        $userId = $this->option('user');
        $amount = $this->option('amount');
        
        // Verificar que el usuario existe
        $usuario = Usuario::find($userId);
        if (!$usuario) {
            $this->error("❌ Usuario {$userId} no encontrado");
            return 1;
        }
        
        $this->info("👤 Usuario: {$usuario->nombre_usuario} ({$usuario->correo_electronico})");
        $this->info("💰 Monto: $" . number_format($amount, 0, ',', '.'));
        
        try {
            DB::beginTransaction();
            
            // Crear un pedido de prueba
            $pedido = $this->createTestOrder($usuario, $amount);
            
            $this->info("📦 Pedido creado: #{$pedido->pedido_id}");
            $this->info("📊 Estado inicial: " . $pedido->estado->nombre_estado);
            
            DB::commit();
            
            // Mostrar información para el usuario
            $this->newLine();
            $this->info('🎯 Pasos para probar:');
            $this->info('1. Ve a: http://localhost:8000/stripe/payment-form/' . $pedido->pedido_id);
            $this->info('2. Usa una tarjeta de prueba:');
            $this->info('   • Éxito: 4242 4242 4242 4242');
            $this->info('   • Fallido: 4000 0000 0000 0002');
            $this->info('3. Completa el pago');
            $this->info('4. Verifica el estado del pedido con: php artisan check:pedidos');
            
            // Probar webhook automáticamente
            $this->newLine();
            if ($this->confirm('¿Deseas probar el webhook automáticamente?')) {
                $this->info('🧪 Probando webhook...');
                $this->call('stripe:test-webhook-local', [
                    '--event' => 'payment_intent.succeeded',
                    '--pedido' => $pedido->pedido_id
                ]);
                
                // Verificar estado final
                $pedido->refresh();
                $this->info("📊 Estado final: " . $pedido->estado->nombre_estado);
            }
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Error: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
    
    private function createTestOrder($usuario, $amount)
    {
        // Buscar un producto existente o crear uno de prueba
        $producto = Producto::first();
        if (!$producto) {
            $this->warn('⚠️ No hay productos, creando uno de prueba...');
            $producto = Producto::create([
                'nombre_producto' => 'Producto de Prueba',
                'descripcion' => 'Producto para pruebas de Stripe',
                'precio' => $amount,
                'stock' => 100,
                'categoria_id' => 1,
                'marca_id' => 1,
                'estado' => 'activo'
            ]);
        }
        
        // Crear pedido
        $pedido = Pedido::create([
            'usuario_id' => $usuario->usuario_id,
            'direccion_id' => 1, // Asumiendo que existe
            'fecha_pedido' => now(),
            'estado_id' => 1, // Pendiente
            'total' => $amount
        ]);
        
        // Crear detalle del pedido
        DetallePedido::create([
            'pedido_id' => $pedido->pedido_id,
            'producto_id' => $producto->producto_id,
            'cantidad' => 1,
            'precio_unitario' => $amount
        ]);
        
        return $pedido;
    }
}
