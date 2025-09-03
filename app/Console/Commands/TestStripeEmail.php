<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Usuario;
use App\Models\Pedido;
use App\Models\Direccion;
use App\Services\StripeService;
use Illuminate\Support\Facades\Log;

class TestStripeEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:stripe-email {email} {--pedido-id= : ID del pedido específico a probar}';

    /**
     * The console console description.
     *
     * @var string
     */
    protected $description = 'Probar el envío de correos desde StripeService';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $pedidoId = $this->option('pedido-id');

        $this->info("🧪 Probando envío de correos desde StripeService");
        $this->info("📧 Email de destino: {$email}");

        try {
            // Buscar o crear usuario
            $usuario = Usuario::where('correo_electronico', $email)->first();
            
            if (!$usuario) {
                $this->error("❌ Usuario con email {$email} no encontrado");
                return 1;
            }

            $this->info("✅ Usuario encontrado: {$usuario->nombre_usuario}");

            // Buscar o crear pedido
            if ($pedidoId) {
                $pedido = Pedido::with('usuario')->find($pedidoId);
                if (!$pedido) {
                    $this->error("❌ Pedido #{$pedidoId} no encontrado");
                    return 1;
                }
                if ($pedido->usuario_id !== $usuario->usuario_id) {
                    $this->error("❌ El pedido #{$pedidoId} no pertenece al usuario {$usuario->nombre_usuario}");
                    return 1;
                }
            } else {
                $pedido = $this->createTestPedido($usuario);
            }

            $this->info("📦 Pedido encontrado: #{$pedido->pedido_id}");
            $this->info("💰 Total: $" . number_format($pedido->total, 0, ',', '.'));
            $this->info("📅 Estado actual: {$pedido->estado_id}");

            // Simular confirmación de pago exitoso
            $this->info("\n🔄 Simulando confirmación de pago exitoso...");
            
            // Crear un pago de prueba
            $pago = $this->createTestPago($pedido);
            
            // Simular PaymentIntent
            $paymentIntent = (object) [
                'id' => 'pi_test_' . uniqid(),
                'status' => 'succeeded'
            ];

            // Llamar al método privado usando reflection
            $this->testEnvioCorreo($pedido, $pago, $paymentIntent);

            $this->info("\n🎉 Prueba completada exitosamente!");
            $this->info("📧 Revisa tu bandeja de entrada (y carpeta de spam)");
            $this->info("🔗 URL del pedido: " . route('pedidos.show', $pedido->pedido_id));

        } catch (\Exception $e) {
            $this->error("❌ Error durante la prueba: " . $e->getMessage());
            Log::error('Error en TestStripeEmail: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }

    /**
     * Crear pedido de prueba
     */
    private function createTestPedido(Usuario $usuario): Pedido
    {
        // Buscar o crear una dirección de prueba
        $direccion = Direccion::where('usuario_id', $usuario->usuario_id)->first();
        
        if (!$direccion) {
            $direccion = Direccion::create([
                'usuario_id' => $usuario->usuario_id,
                'calle' => 'Calle de Prueba Stripe',
                'numero' => '456',
                'ciudad' => 'Ciudad de Prueba Stripe',
                'estado' => 'Estado de Prueba Stripe',
                'codigo_postal' => '54321',
                'pais' => 'Colombia'
            ]);
        }

        // Crear pedido en estado PENDIENTE (estado_id = 1)
        $pedido = Pedido::create([
            'usuario_id' => $usuario->usuario_id,
            'direccion_id' => $direccion->direccion_id,
            'fecha_pedido' => now(),
            'estado_id' => 1, // PENDIENTE
            'total' => 250000
        ]);

        $this->info("   📦 Pedido de prueba creado: #{$pedido->pedido_id}");
        $this->info("   💰 Total: $" . number_format($pedido->total, 0, ',', '.'));
        $this->info("   📍 Dirección: {$direccion->calle} {$direccion->numero}, {$direccion->ciudad}");

        return $pedido;
    }

    /**
     * Crear pago de prueba
     */
    private function createTestPago(Pedido $pedido): object
    {
        // Crear un objeto pago simulado
        $pago = (object) [
            'pago_id' => uniqid(),
            'pedido_id' => $pedido->pedido_id,
            'estado' => 'pending',
            'stripe_payment_intent_id' => 'pi_test_' . uniqid()
        ];

        $this->info("   💳 Pago de prueba creado: {$pago->pago_id}");

        return $pago;
    }

    /**
     * Probar envío de correo usando reflection
     */
    private function testEnvioCorreo(Pedido $pedido, $pago, $paymentIntent): void
    {
        try {
            $this->info("   📧 Probando envío de correo...");

            // Usar reflection para acceder al método privado
            $stripeService = new StripeService();
            $reflection = new \ReflectionClass($stripeService);
            $method = $reflection->getMethod('enviarCorreoConfirmacion');
            $method->setAccessible(true);

            // Llamar al método privado
            $method->invoke($stripeService, $pedido);

            $this->info("   ✅ Correo enviado exitosamente!");

        } catch (\Exception $e) {
            $this->error("   ❌ Error al enviar correo: " . $e->getMessage());
            throw $e;
        }
    }
}
