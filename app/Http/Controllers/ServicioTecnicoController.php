<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Mail\ServicioTecnicoFormulario;
use App\Mail\ServicioTecnicoConfirmacion;

class ServicioTecnicoController extends Controller
{
    public function enviarFormulario(Request $request)
    {
        // Validar los datos del formulario
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'device' => 'required|string|in:celular,tablet,computadora,laptop',
            'model' => 'nullable|string|max:100',
            'problem' => 'required|string|max:1000'
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'name.max' => 'El nombre no puede tener más de 100 caracteres.',
            'phone.required' => 'El teléfono es obligatorio.',
            'phone.max' => 'El teléfono no puede tener más de 20 caracteres.',
            'email.email' => 'El email debe tener un formato válido.',
            'email.max' => 'El email no puede tener más de 255 caracteres.',
            'device.required' => 'Debes seleccionar un tipo de dispositivo.',
            'device.in' => 'El tipo de dispositivo seleccionado no es válido.',
            'model.max' => 'El modelo no puede tener más de 100 caracteres.',
            'problem.required' => 'La descripción del problema es obligatoria.',
            'problem.max' => 'La descripción no puede tener más de 1000 caracteres.'
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
                'nombre' => $request->name,
                'telefono' => $request->phone,
                'dispositivo' => $request->device,
                'modelo' => $request->model,
                'problema' => $request->problem,
                'fecha' => now()->format('d/m/Y H:i:s'),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ];

            // Enviar email al administrador
            Mail::to('osmandavidgallego@gmail.com')->send(new ServicioTecnicoFormulario($datosFormulario));

            // Enviar email de confirmación al usuario (si proporciona email)
            if ($request->email && filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
                Mail::to($request->email)->send(new ServicioTecnicoConfirmacion($datosFormulario));
            }

            $mensaje = '¡Solicitud enviada exitosamente! Te contactaremos en las próximas 2 horas para agendar tu cita de diagnóstico.';
            
            if ($request->email && filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
                $mensaje .= ' También hemos enviado una confirmación a tu email.';
            }

            return response()->json([
                'success' => true,
                'message' => $mensaje
            ]);

        } catch (\Exception $e) {
            Log::error('Error al enviar formulario de servicio técnico: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Hubo un error al enviar la solicitud. Por favor, inténtalo de nuevo más tarde.'
            ], 500);
        }
    }
}
