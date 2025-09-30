<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Usuario;
use App\Models\Pedido;
use App\Http\Controllers\Cliente\CheckoutController;
use Illuminate\Support\Facades\Auth;

class ProbarPaginaConfirmacion extends Command
{
    protected $signature = 'checkout:probar-confirmacion {--usuario-id=2}';
    protected $description = 'Prueba específicamente la página de confirmación de checkout';

    public function handle(): int
    {
        $this->info('📄 Iniciando prueba específica de la página de confirmación...');
        
        try {
            // Obtener parámetros
            $usuarioId = $this->option('usuario-id');
            
            // Verificar que el usuario existe
            $usuario = Usuario::find($usuarioId);
            if (!$usuario) {
                $this->error("❌ Usuario con ID {$usuarioId} no encontrado");
                return 1;
            }
            
            $this->info("✅ Usuario: {$usuario->nombre} (ID: {$usuario->id})");
            
            // Autenticar como el usuario
            Auth::login($usuario);
            $this->info("✅ Usuario autenticado correctamente");
            
            // Buscar un pedido del usuario
            $pedido = Pedido::where('usuario_id', $usuarioId)
                ->where('estado_id', 1) // pendiente
                ->latest()
                ->first();
            
            if (!$pedido) {
                $this->error("❌ No hay pedidos pendientes para el usuario {$usuarioId}");
                return 1;
            }
            
            $this->info("✅ Pedido encontrado: {$pedido->pedido_id}");
            $this->info("✅ Estado: {$pedido->estado_id} (1=pendiente)");
            $this->info("✅ Total: {$pedido->total}");
            
            // Probar el método showConfirm del controlador
            $this->info("\n📄 Probando método showConfirm...");
            
            try {
                $controller = app(CheckoutController::class);
                $response = $controller->showConfirm($pedido->pedido_id);
                
                $this->info("✅ showConfirm exitoso:");
                $this->info("   - Tipo: " . get_class($response));
                
                // Verificar que es una vista
                if ($response instanceof \Illuminate\View\View) {
                    $this->info("✅ CORRECTO: Es una vista");
                    
                    // Renderizar la vista para verificar contenido
                    $content = $response->render();
                    
                    if (strpos($content, 'Confirmar Pago') !== false) {
                        $this->info("✅ Contiene título 'Confirmar Pago'");
                    } else {
                        $this->warn("⚠️ No contiene título 'Confirmar Pago'");
                    }
                    
                    if (strpos($content, 'sweetalert2') !== false) {
                        $this->info("✅ Incluye SweetAlert2");
                    } else {
                        $this->warn("⚠️ No incluye SweetAlert2");
                    }
                    
                    if (strpos($content, 'form') !== false) {
                        $this->info("✅ Contiene formulario");
                    } else {
                        $this->warn("⚠️ No contiene formulario");
                    }
                    
                    if (strpos($content, 'csrf-token') !== false) {
                        $this->info("✅ Incluye token CSRF");
                    } else {
                        $this->warn("⚠️ No incluye token CSRF");
                    }
                    
                } else {
                    $this->error("❌ ERROR: No es una vista");
                    $this->error("   - Tipo: " . get_class($response));
                    return 1;
                }
                
            } catch (\Exception $e) {
                $this->error("❌ Error en showConfirm:");
                $this->error("   - Mensaje: " . $e->getMessage());
                $this->error("   - Archivo: " . $e->getFile() . ":" . $e->getLine());
                $this->error("   - Stack trace:");
                $this->error($e->getTraceAsString());
                return 1;
            }
            
            // Probar el método confirmarPedido
            $this->info("\n✅ Probando método confirmarPedido...");
            
            try {
                $controller = app(CheckoutController::class);
                $request = new \Illuminate\Http\Request([
                    'confirmar_pago' => true,
                    'acepto_terminos' => true
                ]);
                
                $response = $controller->confirmarPedido($request, $pedido->pedido_id);
                
                $this->info("✅ confirmarPedido exitoso:");
                $this->info("   - Status: " . $response->getStatusCode());
                $this->info("   - Content: " . $response->getContent());
                
            } catch (\Exception $e) {
                $this->error("❌ Error en confirmarPedido:");
                $this->error("   - Mensaje: " . $e->getMessage());
                $this->error("   - Archivo: " . $e->getFile() . ":" . $e->getLine());
                return 1;
            }
            
            $this->info("\n✅ Prueba de página de confirmación completada exitosamente");
            $this->info("🎯 La página de confirmación debería funcionar correctamente en el navegador");
            $this->info("🔗 URL de confirmación: http://localhost:8000/checkout/confirm/{$pedido->pedido_id}");
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error("❌ Error durante la prueba: " . $e->getMessage());
            $this->error("Stack trace: " . $e->getTraceAsString());
            return 1;
        }
    }
}