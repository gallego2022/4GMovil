<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Usuario;
use App\Models\Pedido;
use App\Models\Direccion;
use App\Models\MetodoPago;
use App\Services\AdminNotificationService;
use Illuminate\Support\Facades\Log;

class TestAdminNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:admin-notifications {email} {--metodo= : Método de pago específico a probar}';

    /**
     * The console console description.
     *
     * @var string
     */
    protected $description = 'Probar las notificaciones a administradores sobre pedidos nuevos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $metodoEspecifico = $this->option('metodo');

        $this->info("🧪 Probando sistema de notificaciones a administradores");
        $this->info("📧 Email de destino: {$email}");

        try {
            // Buscar o crear usuario
            $usuario = Usuario::where('correo_electronico', $email)->first();
            
            if (!$usuario) {
                $this->error("❌ Usuario con email {$email} no encontrado");
                return 1;
            }

            $this->info("✅ Usuario encontrado: {$usuario->nombre_usuario}");

            // Obtener métodos de pago disponibles
            $metodosPago = MetodoPago::where('estado', 1)->get();
            
            if ($metodosPago->isEmpty()) {
                $this->error("❌ No hay métodos de pago activos");
                return 1;
            }

            $this->info("💳 Métodos de pago disponibles:");
            foreach ($metodosPago as $metodo) {
                $this->info("   - {$metodo->metodo_id}: {$metodo->nombre}");
            }

            // Si se especifica un método, probar solo ese
            if ($metodoEspecifico) {
                $metodo = $metodosPago->where('metodo_id', $metodoEspecifico)->first();
                if (!$metodo) {
                    $this->error("❌ Método de pago #{$metodoEspecifico} no encontrado");
                    return 1;
                }
                $this->probarMetodoPago($usuario, $metodo);
            } else {
                // Probar todos los métodos
                foreach ($metodosPago as $metodo) {
                    $this->probarMetodoPago($usuario, $metodo);
                    $this->info(""); // Línea en blanco entre métodos
                }
            }

            $this->info("\n🎉 Pruebas completadas exitosamente!");
            $this->info("📧 Revisa tu bandeja de entrada (y carpeta de spam)");

        } catch (\Exception $e) {
            $this->error("❌ Error durante la prueba: " . $e->getMessage());
            Log::error('Error en TestAdminNotifications: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }

    /**
     * Probar un método de pago específico
     */
    private function probarMetodoPago(Usuario $usuario, MetodoPago $metodo): void
    {
        $this->info("🔄 Probando método: {$metodo->nombre}");

        try {
            // Crear pedido de prueba
            $pedido = $this->createTestPedido($usuario, $metodo);
            
            $this->info("   📦 Pedido creado: #{$pedido->pedido_id}");
            $this->info("   💰 Total: $" . number_format($pedido->total, 0, ',', '.'));
            $this->info("   💳 Método: {$metodo->nombre}");

            // Simular notificación a administradores
            $this->info("   📧 Enviando notificación a administradores...");
            
            $adminService = new AdminNotificationService();
            $enviado = $adminService->notificarPedidoNuevo($pedido, $metodo->nombre);

            if ($enviado) {
                $this->info("   ✅ Notificación a administradores enviada exitosamente!");
                $this->info("   🔗 URL del pedido: " . route('admin.pedidos.show', $pedido->pedido_id));
            } else {
                $this->warn("   ⚠️ No se pudo enviar la notificación a administradores");
            }

            // Mostrar estadísticas
            $stats = $adminService->obtenerEstadisticas();
            $this->info("   📊 Estadísticas de administradores:");
            $this->info("      - Total admins: {$stats['total_admins']}");
            $this->info("      - Con email: {$stats['admins_con_email']}");
            $this->info("      - Sin email: {$stats['admins_sin_email']}");

        } catch (\Exception $e) {
            $this->error("   ❌ Error probando método {$metodo->nombre}: " . $e->getMessage());
        }
    }

    /**
     * Crear pedido de prueba
     */
    private function createTestPedido(Usuario $usuario, MetodoPago $metodo): Pedido
    {
        // Buscar o crear una dirección de prueba
        $direccion = Direccion::where('usuario_id', $usuario->usuario_id)->first();
        
        if (!$direccion) {
            $direccion = Direccion::create([
                'usuario_id' => $usuario->usuario_id,
                'calle' => 'Calle de Prueba ' . $metodo->nombre,
                'numero' => '789',
                'ciudad' => 'Ciudad de Prueba ' . $metodo->nombre,
                'estado' => 'Estado de Prueba ' . $metodo->nombre,
                'codigo_postal' => '98765',
                'pais' => 'Colombia'
            ]);
        }

        // Crear pedido en estado CONFIRMADO (estado_id = 2)
        $pedido = Pedido::create([
            'usuario_id' => $usuario->usuario_id,
            'direccion_id' => $direccion->direccion_id,
            'fecha_pedido' => now(),
            'estado_id' => 2, // CONFIRMADO
            'total' => 300000 + (rand(1, 10) * 10000) // Precio aleatorio entre 300k y 400k
        ]);

        // Crear pago asociado
        $estadoPago = $metodo->nombre === 'Stripe' ? 'completed' : 'pendiente';
        
        \App\Models\Pago::create([
            'pedido_id' => $pedido->pedido_id,
            'metodo_id' => $metodo->metodo_id,
            'monto' => $pedido->total,
            'estado' => $estadoPago,
            'fecha_pago' => now()
        ]);

        return $pedido;
    }
}
