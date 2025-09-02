<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\ContactoFormulario;
use App\Mail\ContactoConfirmacion;

class ContactoService
{
    /**
     * Enviar formulario de contacto
     */
    public function enviarFormulario(Request $request): array
    {
        try {
            // Preparar los datos del formulario
            $datosFormulario = [
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'email' => $request->email,
                'telefono' => $request->telefono,
                'asunto' => $request->asunto,
                'mensaje' => $request->mensaje,
                'fecha' => now()->format('d/m/Y H:i:s'),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ];

            // Enviar email al administrador
            Mail::to('4gmoviltest@gmail.com')->send(new ContactoFormulario($datosFormulario));

            // Enviar email de confirmación al usuario
            Mail::to($request->email)->send(new ContactoConfirmacion($datosFormulario));

            // Guardar en la base de datos (opcional)
            // $this->guardarContacto($datosFormulario);

            return [
                'success' => true,
                'message' => '¡Mensaje enviado exitosamente! Nos pondremos en contacto contigo pronto.'
            ];

        } catch (\Exception $e) {
            Log::error('Error al enviar formulario de contacto: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Hubo un error al enviar el mensaje. Por favor, inténtalo de nuevo más tarde.'
            ];
        }
    }

    /**
     * Guardar contacto en base de datos (opcional)
     */
    private function guardarContacto($datos): void
    {
        // Aquí puedes guardar el contacto en la base de datos si lo deseas
        // Por ejemplo, crear una tabla 'contactos' y guardar ahí
    }

    /**
     * Enviar formulario de servicio técnico
     */
    public function enviarServicioTecnico(Request $request): array
    {
        try {
            // Preparar los datos del formulario
            $datosFormulario = [
                'nombre' => $request->name,
                'telefono' => $request->phone,
                'email' => $request->email,
                'dispositivo' => $request->device,
                'modelo' => $request->model,
                'problema' => $request->problem,
                'fecha' => now()->format('d/m/Y H:i:s'),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ];

            // Enviar email al administrador
            Mail::to('4gmoviltest@gmail.com')->send(new \App\Mail\ServicioTecnicoFormulario($datosFormulario));

            // Enviar email de confirmación al usuario (si proporciona email)
            if ($request->email && filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
                Mail::to($request->email)->send(new \App\Mail\ServicioTecnicoConfirmacion($datosFormulario));
            }

            $mensaje = '¡Solicitud enviada exitosamente! Te contactaremos en las próximas 2 horas para agendar tu cita de diagnóstico.';
            
            if ($request->email && filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
                $mensaje .= ' También hemos enviado una confirmación a tu email.';
            }

            return [
                'success' => true,
                'message' => $mensaje
            ];

        } catch (\Exception $e) {
            Log::error('Error al enviar formulario de servicio técnico: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Hubo un error al enviar la solicitud. Por favor, inténtalo de nuevo más tarde.'
            ];
        }
    }
}
