<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Mail\ContactoFormulario;
use App\Mail\ContactoConfirmacion;

class ContactoController extends Controller
{
    public function enviarFormulario(Request $request)
    {
        // Validar los datos del formulario
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'email' => 'required|email|max:255',
            'telefono' => 'required|string|max:20',
            'asunto' => 'required|string|max:100',
            'mensaje' => 'required|string|max:1000',
            'terminos' => 'required|accepted'
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'apellido.required' => 'El apellido es obligatorio.',
            'email.required' => 'El email es obligatorio.',
            'email.email' => 'El email debe tener un formato válido.',
            'telefono.required' => 'El teléfono es obligatorio.',
            'asunto.required' => 'El asunto es obligatorio.',
            'mensaje.required' => 'El mensaje es obligatorio.',
            'mensaje.max' => 'El mensaje no puede tener más de 1000 caracteres.',
            'terminos.required' => 'Debes aceptar los términos y condiciones.',
            'terminos.accepted' => 'Debes aceptar los términos y condiciones.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Por favor, corrige los errores en el formulario.',
                'errors' => $validator->errors()
            ], 422);
        }

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

            return response()->json([
                'success' => true,
                'message' => '¡Mensaje enviado exitosamente! Nos pondremos en contacto contigo pronto.'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al enviar formulario de contacto: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Hubo un error al enviar el mensaje. Por favor, inténtalo de nuevo más tarde.'
            ], 500);
        }
    }

    private function guardarContacto($datos)
    {
        // Aquí puedes guardar el contacto en la base de datos si lo deseas
        // Por ejemplo, crear una tabla 'contactos' y guardar ahí
    }
}
