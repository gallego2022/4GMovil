<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Usuario;
use App\Models\Pedido;
use App\Mail\StripePagoExitoso;
use App\Mail\StripePagoFallido;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class TestStripeMailables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:stripe-mailables {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Probar los Mailable de Stripe (pago exitoso y fallido)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info("💳 Probando Mailable de Stripe...");
        $this->info("📧 Email: {$email}");
        
        // Verificar si el usuario existe
        $usuario = Usuario::where('correo_electronico', $email)->first();
        
        if (!$usuario) {
            $this->error("❌ Usuario no encontrado con el email: {$email}");
            return 1;
        }
        
        $this->info("✅ Usuario encontrado: {$usuario->nombre_usuario}");
        
        // Crear un pedido de prueba
        $pedido = $this->createTestPedido($usuario);
        
        // Crear un PaymentIntent de prueba
        $paymentIntent = $this->createTestPaymentIntent();
        
        $this->info("\n📧 Enviando emails de prueba de Stripe...");
        
        try {
            // 1. Pago exitoso
            $this->testPagoExitoso($usuario, $pedido, $paymentIntent);
            
            // 2. Pago fallido
            $this->testPagoFallido($usuario, $pedido, $paymentIntent);
            
            $this->info("\n✅ Todos los Mailable de Stripe se enviaron exitosamente!");
            $this->info("📬 Revisa tu bandeja de entrada (y carpeta de spam)");
            
        } catch (\Exception $e) {
            $this->error("❌ Error al enviar emails: " . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
    
    private function createTestPedido($usuario)
    {
        // Crear un pedido de prueba
        $pedido = new Pedido();
        $pedido->pedido_id = 'STRIPE-' . Str::random(8);
        $pedido->usuario_id = $usuario->usuario_id;
        $pedido->total = 250000;
        $pedido->fecha_pedido = now();
        $pedido->estado = 'pendiente';
        
        return $pedido;
    }
    
    private function createTestPaymentIntent()
    {
        // Crear un PaymentIntent de prueba
        $paymentIntent = new \stdClass();
        $paymentIntent->id = 'pi_' . Str::random(24);
        $paymentIntent->amount = 250000;
        $paymentIntent->currency = 'cop';
        $paymentIntent->status = 'succeeded';
        
        return $paymentIntent;
    }
    
    private function testPagoExitoso($usuario, $pedido, $paymentIntent)
    {
        $this->info("   ✅ Enviando email de pago exitoso...");
        
        Mail::to($usuario->correo_electronico)
            ->send(new StripePagoExitoso($usuario, $pedido, $paymentIntent));
        
        $this->info("      ✅ Email de pago exitoso enviado");
    }
    
    private function testPagoFallido($usuario, $pedido, $paymentIntent)
    {
        $this->info("   ❌ Enviando email de pago fallido...");
        
        Mail::to($usuario->correo_electronico)
            ->send(new StripePagoFallido($usuario, $pedido, $paymentIntent));
        
        $this->info("      ✅ Email de pago fallido enviado");
    }
}
