<?php

namespace App\Http\Controllers\Publico;

use App\Http\Controllers\Base\WebController;
use App\Services\ContactoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ContactoController extends WebController
{
    protected $contactoService;

    public function __construct(ContactoService $contactoService)
    {
        $this->contactoService = $contactoService;
    }
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
            $result = $this->contactoService->enviarFormulario($request);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message']
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 500);

        } catch (\Exception $e) {
            Log::error('Error al enviar formulario de contacto: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Hubo un error al enviar el mensaje. Por favor, inténtalo de nuevo más tarde.'
            ], 500);
        }
    }

    /**
     * Enviar formulario de servicio técnico
     */
    public function enviarServicioTecnico(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'device' => 'required|string|in:celular,tablet,computadora,laptop',
            'model' => 'nullable|string|max:100',
            'problem' => 'required|string|max:1000'
        ]);

        try {
            $result = $this->contactoService->enviarServicioTecnico($request);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message']
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 500);

        } catch (\Exception $e) {
            Log::error('Error en formulario de servicio técnico: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Hubo un error al enviar la solicitud. Por favor, inténtalo de nuevo más tarde.'
            ], 500);
        }
    }
}
