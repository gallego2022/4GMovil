<?php

namespace App\Http\Controllers\Servicios;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Illuminate\Session\TokenMismatchException;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;

class TestErrorController extends Controller
{
    /**
     * Probar diferentes tipos de errores
     */
    public function testErrors(Request $request)
    {
        $errorType = $request->query('error', '404');
        
        switch ($errorType) {
            case '404':
                throw new NotFoundHttpException('Página de prueba no encontrada');
                
            case '403':
                throw new AccessDeniedHttpException('Acceso denegado de prueba');
                
            case '500':
                throw new \Exception('Error interno de prueba');
                
            case '419':
                throw new TokenMismatchException('Token CSRF expirado de prueba');
                
            case '429':
                throw new TooManyRequestsHttpException('Demasiadas solicitudes de prueba');
                
            case 'validation':
                throw ValidationException::withMessages([
                    'email' => ['El correo electrónico es obligatorio'],
                    'password' => ['La contraseña debe tener al menos 8 caracteres']
                ]);
                
            case 'model':
                throw new ModelNotFoundException('Usuario de prueba no encontrado');
                
            case 'auth':
                throw new AuthenticationException('Usuario no autenticado de prueba');
                
            default:
                return Response::json([
                    'message' => 'Middleware funcionando correctamente',
                    'available_errors' => [
                        '404', '403', '500', '419', '429', 
                        'validation', 'model', 'auth'
                    ],
                    'usage' => 'Agrega ?error=TIPO_DE_ERROR a la URL'
                ]);
        }
    }
}