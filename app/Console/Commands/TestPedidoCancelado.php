<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Usuario;
use App\Models\Pedido;
use App\Mail\PedidoCancelado;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class TestPedidoCancelado extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:pedido-cancelado {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Probar el Mailable de pedido cancelado';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info("🚫 Probando Mailable de pedido cancelado...");
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
        
        $this->info("\n📧 Enviando email de pedido cancelado...");
        
        try {
            $this->testPedidoCancelado($usuario, $pedido);
            
            $this->info("\n✅ Email de pedido cancelado enviado exitosamente!");
            $this->info("📬 Revisa tu bandeja de entrada (y carpeta de spam)");
            
        } catch (\Exception $e) {
            $this->error("❌ Error al enviar email: " . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
    
    private function createTestPedido($usuario)
    {
        // Crear un pedido de prueba
        $pedido = new Pedido();
        $pedido->pedido_id = 'CANCEL-' . Str::random(8);
        $pedido->usuario_id = $usuario->usuario_id;
        $pedido->total = 180000;
        $pedido->fecha_pedido = now();
        $pedido->estado = 'pendiente';
        
        return $pedido;
    }
    
    private function testPedidoCancelado($usuario, $pedido)
    {
        $this->info("   🚫 Enviando email de pedido cancelado...");
        
        $motivo = 'Producto agotado - No se pudo procesar el pedido';
        
        Mail::to($usuario->correo_electronico)
            ->send(new PedidoCancelado($usuario, $pedido, $motivo));
        
        $this->info("      ✅ Email de pedido cancelado enviado");
    }
}
