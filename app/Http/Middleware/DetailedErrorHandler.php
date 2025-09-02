<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class DetailedErrorHandler
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            return $next($request);
        } catch (ValidationException $e) {
            return $this->handleValidationException($e, $request);
        } catch (AuthenticationException $e) {
            return $this->handleAuthenticationException($e, $request);
        } catch (ModelNotFoundException $e) {
            return $this->handleModelNotFoundException($e, $request);
        } catch (NotFoundHttpException $e) {
            return $this->handleNotFoundException($e, $request);
        } catch (MethodNotAllowedHttpException $e) {
            return $this->handleMethodNotAllowedException($e, $request);
        } catch (\Exception $e) {
            return $this->handleGenericException($e, $request);
        }
    }

    /**
     * Maneja errores de validación
     */
    private function handleValidationException(ValidationException $e, Request $request)
    {
        Log::warning('Error de validación', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'user_id' => Auth::check() ? Auth::id() : 'guest',
            'errors' => $e->errors(),
            'input' => $request->except(['password', 'password_confirmation'])
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'error_type' => 'validation_error',
                'message' => 'Los datos proporcionados no son válidos',
                'errors' => $e->errors(),
                'suggestions' => $this->getValidationSuggestions($e->errors())
            ], 422);
        }

        return redirect()->back()->withErrors($e->errors())->withInput();
    }

    /**
     * Maneja errores de autenticación
     */
    private function handleAuthenticationException(AuthenticationException $e, Request $request)
    {
        Log::warning('Error de autenticación', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'error_type' => 'authentication_error',
                'message' => 'Debes iniciar sesión para acceder a este recurso',
                'redirect_url' => route('login')
            ], 401);
        }

        return redirect()->route('login')->with('error', 'Debes iniciar sesión para acceder a este recurso');
    }

    /**
     * Maneja errores de modelo no encontrado
     */
    private function handleModelNotFoundException(ModelNotFoundException $e, Request $request)
    {
        $model = class_basename($e->getModel());
        
        Log::warning('Modelo no encontrado', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'model' => $model,
            'id' => $e->getIds()
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'error_type' => 'model_not_found',
                'message' => "El {$model} solicitado no fue encontrado",
                'model' => $model,
                'suggestion' => 'Verificar que el ID proporcionado sea correcto'
            ], 404);
        }

        return redirect()->back()->with('error', "El {$model} solicitado no fue encontrado");
    }

    /**
     * Maneja errores de ruta no encontrada
     */
    private function handleNotFoundException(NotFoundHttpException $e, Request $request)
    {
        Log::warning('Ruta no encontrada', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'error_type' => 'route_not_found',
                'message' => 'La página solicitada no fue encontrada',
                'suggestion' => 'Verificar que la URL sea correcta'
            ], 404);
        }

        return response()->view('errors.404', [], 404);
    }

    /**
     * Maneja errores de método HTTP no permitido
     */
    private function handleMethodNotAllowedException(MethodNotAllowedHttpException $e, Request $request)
    {
        Log::warning('Método HTTP no permitido', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'allowed_methods' => $e->getHeaders()['Allow'] ?? []
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'error_type' => 'method_not_allowed',
                'message' => 'El método HTTP utilizado no está permitido para esta ruta',
                'allowed_methods' => $e->getHeaders()['Allow'] ?? [],
                'suggestion' => 'Usar uno de los métodos HTTP permitidos'
            ], 405);
        }

        return response()->view('errors.405', [
            'allowed_methods' => $e->getHeaders()['Allow'] ?? []
        ], 405);
    }

    /**
     * Maneja errores genéricos
     */
    private function handleGenericException(\Exception $e, Request $request)
    {
        Log::error('Error genérico', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'user_id' => Auth::check() ? Auth::id() : 'guest',
            'error_code' => $e->getCode(),
            'error_message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'error_type' => 'general_error',
                'message' => 'Ha ocurrido un error inesperado',
                'technical_details' => config('app.debug') ? [
                    'error_code' => $e->getCode(),
                    'error_message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ] : null
            ], 500);
        }

        return response()->view('errors.500', [], 500);
    }

    /**
     * Proporciona sugerencias para errores de validación
     */
    private function getValidationSuggestions(array $errors): array
    {
        $suggestions = [];
        
        foreach ($errors as $field => $fieldErrors) {
            if (str_contains($field, 'email')) {
                $suggestions[$field] = 'Asegúrate de que el formato del correo electrónico sea válido (ejemplo@dominio.com)';
            } elseif (str_contains($field, 'password')) {
                $suggestions[$field] = 'La contraseña debe tener al menos 8 caracteres';
            } elseif (str_contains($field, 'phone') || str_contains($field, 'telefono')) {
                $suggestions[$field] = 'Ingresa un número de teléfono válido';
            } elseif (str_contains($field, 'required')) {
                $suggestions[$field] = 'Este campo es obligatorio';
            }
        }
        
        return $suggestions;
    }
}
