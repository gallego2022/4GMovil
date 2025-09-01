<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Models\Usuario;
use App\Notifications\StripePaymentSucceeded;
use App\Models\Pedido;

class TestEmail extends Command
{
    protected $signature = 'email:test {--to=} {--type=success}';
    protected $description = 'Probar la configuración de email enviando una notificación de prueba';

    public function handle()
    {
        $this->info('🧪 Probando configuración de email...');

        $emailTo = $this->option('to');
        $type = $this->option('type');

        if (!$emailTo) {
            // Buscar un usuario existente
            $usuario = Usuario::first();
            if (!$usuario) {
                $this->error('❌ No hay usuarios en el sistema');
                return 1;
            }
            $emailTo = $usuario->correo_electronico;
        }

        $this->info("📧 Enviando email de prueba a: {$emailTo}");

        try {
            // Crear un pedido de prueba
            $pedido = $this->createTestOrder();

            // Buscar o crear usuario para la prueba
            $usuario = Usuario::where('correo_electronico', $emailTo)->first();
            if (!$usuario) {
                $usuario = Usuario::create([
                    'nombre_usuario' => 'Usuario de Prueba',
                    'correo_electronico' => $emailTo,
                    'contrasena' => bcrypt('password'),
                    'estado' => 'activo'
                ]);
            }

            // Enviar notificación según el tipo
            if ($type === 'success') {
                $usuario->notify(new StripePaymentSucceeded($pedido));
                $this->info('✅ Email de pago exitoso enviado');
            } else {
                // Aquí podrías agregar más tipos de notificaciones
                $this->error('❌ Tipo de notificación no soportado');
                return 1;
            }

            $this->info('🎉 Email enviado exitosamente!');
            $this->info('📧 Revisa la bandeja de entrada de: ' . $emailTo);

        } catch (\Exception $e) {
            $this->error('❌ Error enviando email: ' . $e->getMessage());
            $this->error('💡 Verifica la configuración en tu archivo .env');
            return 1;
        }

        return 0;
    }

    private function createTestOrder()
    {
        // Buscar un usuario existente
        $usuario = Usuario::first();
        if (!$usuario) {
            throw new \Exception('No hay usuarios en el sistema');
        }

        // Buscar una dirección existente o crear una de prueba
        $direccion = \App\Models\Direccion::first();
        if (!$direccion) {
            $direccion = \App\Models\Direccion::create([
                'usuario_id' => $usuario->usuario_id,
                'calle' => 'Calle de Prueba',
                'numero' => '123',
                'ciudad' => 'Ciudad de Prueba',
                'estado' => 'Estado de Prueba',
                'codigo_postal' => '12345',
                'pais' => 'Colombia'
            ]);
        }

        // Crear un pedido de prueba
        return Pedido::create([
            'usuario_id' => $usuario->usuario_id,
            'direccion_id' => $direccion->direccion_id,
            'fecha_pedido' => now(),
            'estado_id' => 2, // Confirmado
            'total' => 70000
        ]);
    }
}
