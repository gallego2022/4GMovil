<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Usuario;
use App\Models\Pedido;
use App\Models\Direccion;
use App\Models\EstadoPedido;
use App\Mail\ConfirmacionPedido;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class TestConfirmacionPedido extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:confirmacion-pedido {email} {--simulate-admin : Simular confirmación desde admin} {--cancel : Simular cancelación desde admin}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Probar el Mailable de confirmación de pedido';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $simulateAdmin = $this->option('simulate-admin');
        $cancel = $this->option('cancel');
        
        $this->info("📦 Probando Mailable de confirmación de pedido...");
        $this->info("📧 Email: {$email}");
        
        if ($simulateAdmin) {
            $this->info("👨‍💼 Modo: Simulando confirmación desde ADMIN");
        } elseif ($cancel) {
            $this->info("🚫 Modo: Simulando cancelación desde ADMIN");
        }
        
        // Verificar si el usuario existe
        $usuario = Usuario::where('correo_electronico', $email)->first();
        
        if (!$usuario) {
            $this->error("❌ Usuario no encontrado con el email: {$email}");
            return 1;
        }
        
        $this->info("✅ Usuario encontrado: {$usuario->nombre_usuario}");
        
        if ($simulateAdmin) {
            // Simular el proceso completo de confirmación desde admin
            $this->simularConfirmacionAdmin($usuario);
        } elseif ($cancel) {
            // Simular el proceso completo de cancelación desde admin
            $this->simularCancelacionAdmin($usuario);
        } else {
            // Crear un pedido de prueba más realista
            $pedido = $this->createTestPedido($usuario);
            
            $this->info("\n📧 Enviando email de confirmación de pedido...");
            
            try {
                $this->testConfirmacionPedido($usuario, $pedido);
                
                $this->info("\n✅ Email de confirmación de pedido enviado exitosamente!");
                $this->info("📬 Revisa tu bandeja de entrada (y carpeta de spam)");
                $this->info("🔗 URL del pedido: " . route('pedidos.show', $pedido->pedido_id));
                
            } catch (\Exception $e) {
                $this->error("❌ Error al enviar email: " . $e->getMessage());
                $this->error("📋 Stack trace: " . $e->getTraceAsString());
                return 1;
            }
        }
        
        return 0;
    }
    
    /**
     * Simular el proceso completo de confirmación desde admin
     */
    private function simularConfirmacionAdmin($usuario)
    {
        $this->info("\n👨‍💼 Simulando proceso de confirmación desde ADMIN...");
        
        // 1. Crear pedido en estado PENDIENTE
        $pedido = $this->createPedidoPendiente($usuario);
        $this->info("   📦 Pedido creado en estado PENDIENTE: #{$pedido->pedido_id}");
        
        // 2. Simular cambio de estado a CONFIRMADO (como lo haría el admin)
        $estadoAnterior = $pedido->estado_id; // 1 = Pendiente
        $nuevoEstado = 2; // 2 = Confirmado
        
        $this->info("   🔄 Cambiando estado: {$estadoAnterior} (Pendiente) → {$nuevoEstado} (Confirmado)");
        
        // 3. Verificar si debe enviar correo (lógica del admin)
        if ($this->debeEnviarCorreoConfirmacion($estadoAnterior, $nuevoEstado)) {
            $this->info("   ✅ Debe enviar correo de confirmación");
            
            // 4. Enviar correo de confirmación
            $this->enviarCorreoConfirmacion($pedido);
            
            // 5. Actualizar estado del pedido
            $pedido->estado_id = $nuevoEstado;
            $pedido->save();
            
            $this->info("   📧 Correo de confirmación enviado exitosamente!");
            $this->info("   🔗 URL del pedido: " . route('pedidos.show', $pedido->pedido_id));
            
        } else {
            $this->warn("   ⚠️ No debe enviar correo de confirmación");
        }
        
        $this->info("\n🎉 Simulación de confirmación desde ADMIN completada!");
    }
    
    /**
     * Simular el proceso completo de cancelación desde admin
     */
    private function simularCancelacionAdmin($usuario)
    {
        $this->info("\n🚫 Simulando proceso de cancelación desde ADMIN...");
        
                 // 1. Encontrar un pedido existente en estado CONFIRMADO
         $pedido = Pedido::where('estado_id', 2)->where('usuario_id', $usuario->usuario_id)->first();
         
         if (!$pedido) {
             $this->error("   ❌ No se encontró un pedido en estado CONFIRMADO para cancelar.");
             return;
         }
         
         $this->info("   📦 Pedido encontrado para cancelar: #{$pedido->pedido_id}");
         
         // 2. Simular cambio de estado a CANCELADO (como lo haría el admin)
         $estadoAnterior = $pedido->estado_id; // 2 = Confirmado
         $nuevoEstado = 3; // 3 = Cancelado
         
         $this->info("   🔄 Cambiando estado: {$estadoAnterior} (Confirmado) → {$nuevoEstado} (Cancelado)");
        
        // 3. Verificar si debe enviar correo de cancelación (lógica del admin)
        if ($this->debeEnviarCorreoCancelacion($estadoAnterior, $nuevoEstado)) {
            $this->info("   ✅ Debe enviar correo de cancelación");
            
            // 4. Enviar correo de cancelación
            $this->enviarCorreoCancelacion($pedido);
            
            // 5. Actualizar estado del pedido
            $pedido->estado_id = $nuevoEstado;
            $pedido->save();
            
            $this->info("   📧 Correo de cancelación enviado exitosamente!");
            $this->info("   🔗 URL del pedido: " . route('pedidos.show', $pedido->pedido_id));
            
        } else {
            $this->warn("   ⚠️ No debe enviar correo de cancelación");
        }
        
        $this->info("\n🎉 Simulación de cancelación desde ADMIN completada!");
    }
    
    /**
     * Crear pedido en estado PENDIENTE (como cuando se crea inicialmente)
     */
    private function createPedidoPendiente($usuario)
    {
        // Buscar o crear una dirección de prueba
        $direccion = Direccion::where('usuario_id', $usuario->usuario_id)->first();
        
        if (!$direccion) {
            $direccion = Direccion::create([
                'usuario_id' => $usuario->usuario_id,
                'calle' => 'Calle de Prueba',
                'numero' => '123',
                'ciudad' => 'Ciudad de Prueba',
                'estado' => 'Estado de Prueba',
                'codigo_postal' => '12345',
                'pais' => 'Colombia'
            ]);
        }
        
        // Crear pedido en estado PENDIENTE (estado_id = 1)
        $pedido = Pedido::create([
            'usuario_id' => $usuario->usuario_id,
            'direccion_id' => $direccion->direccion_id,
            'fecha_pedido' => now(),
            'estado_id' => 1, // PENDIENTE
            'total' => 150000
        ]);
        
        $this->info("   📦 Pedido de prueba creado: #{$pedido->pedido_id}");
        $this->info("   💰 Total: $" . number_format($pedido->total, 0, ',', '.'));
        $this->info("   📍 Dirección: {$direccion->calle} {$direccion->numero}, {$direccion->ciudad}");
        
        return $pedido;
    }
    
    /**
     * Determinar si se debe enviar correo de confirmación (misma lógica del admin)
     */
    private function debeEnviarCorreoConfirmacion(int $estadoAnterior, int $nuevoEstado): bool
    {
        // Estados pendientes
        $estadosPendientes = [1]; // Pendiente
        
        // Estados que confirman la venta
        $estadosConfirmados = [2, 3, 4]; // Confirmado, Enviado, Entregado
        
        // Solo enviar correo si pasa de pendiente a confirmado/enviado/entregado
        return in_array($estadoAnterior, $estadosPendientes) && in_array($nuevoEstado, $estadosConfirmados);
    }
    
    /**
     * Enviar correo de confirmación del pedido (misma lógica del admin)
     */
    private function enviarCorreoConfirmacion(Pedido $pedido): void
    {
        try {
            // Generar URL del pedido
            $pedidoUrl = route('pedidos.show', $pedido->pedido_id);
            
            // Enviar correo de confirmación
            Mail::to($pedido->usuario->correo_electronico)
                ->send(new ConfirmacionPedido($pedido->usuario, $pedido, $pedidoUrl));
            
            $this->info("      ✅ Correo enviado a: {$pedido->usuario->correo_electronico}");
            
        } catch (\Exception $e) {
            $this->error("      ❌ Error enviando correo: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Determinar si se debe enviar correo de cancelación (misma lógica del admin)
     */
    private function debeEnviarCorreoCancelacion(int $estadoAnterior, int $nuevoEstado): bool
    {
        // Estados que confirman la venta
        $estadosConfirmados = [2]; // Confirmado
        
        // Solo enviar correo si pasa de confirmado a cancelado
        return in_array($estadoAnterior, $estadosConfirmados) && $nuevoEstado == 3; // Cancelado
    }
    
    /**
     * Enviar correo de cancelación del pedido (misma lógica del admin)
     */
    private function enviarCorreoCancelacion(Pedido $pedido): void
    {
        try {
            // Generar URL del pedido
            $pedidoUrl = route('pedidos.show', $pedido->pedido_id);
            
            // Enviar correo de cancelación usando el Mailable correcto
            Mail::to($pedido->usuario->correo_electronico)
                ->send(new \App\Mail\PedidoCancelado($pedido->usuario, $pedido, $pedidoUrl));
            
            $this->info("      ✅ Correo de cancelación enviado a: {$pedido->usuario->correo_electronico}");
            
        } catch (\Exception $e) {
            $this->error("      ❌ Error enviando correo de cancelación: " . $e->getMessage());
            throw $e;
        }
    }
    
    private function createTestPedido($usuario)
    {
        // Buscar o crear una dirección de prueba
        $direccion = Direccion::where('usuario_id', $usuario->usuario_id)->first();
        
        if (!$direccion) {
            $direccion = Direccion::create([
                'usuario_id' => $usuario->usuario_id,
                'calle' => 'Calle de Prueba',
                'numero' => '123',
                'ciudad' => 'Ciudad de Prueba',
                'estado' => 'Estado de Prueba',
                'codigo_postal' => '12345',
                'pais' => 'Colombia'
            ]);
        }
        
        // Crear un pedido de prueba más realista
        $pedido = Pedido::create([
            'usuario_id' => $usuario->usuario_id,
            'direccion_id' => $direccion->direccion_id,
            'fecha_pedido' => now(),
            'estado_id' => 2, // Confirmado (estado_id = 2)
            'total' => 150000
        ]);
        
        $this->info("   📦 Pedido de prueba creado: #{$pedido->pedido_id}");
        $this->info("   💰 Total: $" . number_format($pedido->total, 0, ',', '.'));
        $this->info("   📍 Dirección: {$direccion->calle} {$direccion->numero}, {$direccion->ciudad}");
        
        return $pedido;
    }
    
    private function testConfirmacionPedido($usuario, $pedido)
    {
        $this->info("   📦 Enviando confirmación de pedido...");
        
        $pedidoUrl = route('pedidos.show', $pedido->pedido_id);
        
        Mail::to($usuario->correo_electronico)->send(new ConfirmacionPedido($usuario, $pedido, $pedidoUrl));
        
        $this->info("      ✅ Confirmación de pedido enviado");
    }
}
