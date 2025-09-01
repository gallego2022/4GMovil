<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class ExceptionHandlerMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            return $next($request);
        } catch (Throwable $e) {
            return $this->handleException($e, $request);
        }
    }

    /**
     * Handle different types of exceptions.
     */
    protected function handleException(Throwable $e, Request $request): Response
    {
        // Log de debug
        Log::info('ExceptionHandlerMiddleware: Capturando excepción', [
            'exception_class' => get_class($e),
            'message' => $e->getMessage(),
            'request_url' => $request->url(),
            'expects_json' => $request->expectsJson()
        ]);

        // Log del error
        if (app()->environment('local')) {
            Log::error('Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        // Manejar diferentes tipos de excepciones
        if ($e instanceof NotFoundHttpException) {
            return $this->handleNotFound($request);
        }

        if ($e instanceof AccessDeniedHttpException) {
            return $this->handleAccessDenied($request);
        }

        if ($e instanceof TokenMismatchException) {
            return $this->handleTokenMismatch($request);
        }

        if ($e instanceof ThrottleRequestsException) {
            return $this->handleTooManyRequests($request);
        }

        if ($e instanceof ModelNotFoundException) {
            return $this->handleModelNotFound($request);
        }

        if ($e instanceof QueryException) {
            return $this->handleQueryException($request);
        }

        if ($e instanceof ValidationException) {
            return $this->handleValidationException($e, $request);
        }

        if ($e instanceof AuthenticationException) {
            return $this->handleAuthenticationException($request);
        }

        if ($e instanceof HttpException) {
            return $this->handleHttpException($e, $request);
        }

        // Error genérico
        return $this->handleGenericException($e, $request);
    }

    /**
     * Handle 404 Not Found.
     */
    protected function handleNotFound(Request $request): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Recurso no encontrado',
                'message' => 'La página o recurso que buscas no existe'
            ], 404);
        }

        return response()->view('errors.404', [], 404);
    }

    /**
     * Handle 403 Access Denied.
     */
    protected function handleAccessDenied(Request $request): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Acceso denegado',
                'message' => 'No tienes permisos para acceder a este recurso'
            ], 403);
        }

        return response()->view('errors.403', [], 403);
    }

    /**
     * Handle 419 Token Mismatch.
     */
    protected function handleTokenMismatch(Request $request): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Token CSRF expirado',
                'message' => 'Tu sesión ha expirado, por favor recarga la página'
            ], 419);
        }

        return response()->view('errors.419', [], 419);
    }

    /**
     * Handle 429 Too Many Requests.
     */
    protected function handleTooManyRequests(Request $request): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Demasiadas solicitudes',
                'message' => 'Has realizado demasiadas solicitudes, espera un momento'
            ], 429);
        }

        return response()->view('errors.429', [], 429);
    }

    /**
     * Handle Model Not Found.
     */
    protected function handleModelNotFound(Request $request): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Recurso no encontrado',
                'message' => 'El elemento que buscas no existe en la base de datos'
            ], 404);
        }

        return response()->view('errors.404', [], 404);
    }

    /**
     * Handle Query Exception.
     */
    protected function handleQueryException(Request $request): Response
    {
        // Log de debug
        Log::info('ExceptionHandlerMiddleware: Manejando QueryException', [
            'request_url' => $request->url(),
            'expects_json' => $request->expectsJson()
        ]);
        
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Error de base de datos',
                'message' => 'Ha ocurrido un error al procesar tu solicitud'
            ], 500);
        }

        // Verificar si la vista existe
        if (!view()->exists('errors.500')) {
            Log::error('Vista errors.500 no encontrada');
            return response()->view('errors.generic', ['exception' => new \Exception('Error interno del servidor')], 500);
        }

        Log::info('ExceptionHandlerMiddleware: Retornando vista errors.500');
        return response()->view('errors.500', [], 500);
    }

    /**
     * Handle Validation Exception.
     */
    protected function handleValidationException(ValidationException $e, Request $request): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Error de validación',
                'message' => 'Los datos proporcionados no son válidos',
                'errors' => $e->errors()
            ], 422);
        }

        // Para formularios web, redirigir con errores
        return redirect()->back()->withErrors($e->errors())->withInput();
    }

    /**
     * Handle Authentication Exception.
     */
    protected function handleAuthenticationException(Request $request): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'No autenticado',
                'message' => 'Debes iniciar sesión para acceder a este recurso'
            ], 401);
        }

        // Redirigir al login
        return redirect()->guest(route('login'));
    }

    /**
     * Handle HTTP Exception.
     */
    protected function handleHttpException(HttpException $e, Request $request): Response
    {
        $statusCode = $e->getStatusCode();
        
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Error HTTP ' . $statusCode,
                'message' => $e->getMessage() ?: 'Ha ocurrido un error'
            ], $statusCode);
        }

        // Mapear códigos de estado a vistas específicas
        $viewMap = [
            400 => 'errors.400',
            401 => 'errors.401',
            403 => 'errors.403',
            404 => 'errors.404',
            419 => 'errors.419',
            422 => 'errors.422',
            429 => 'errors.429',
            500 => 'errors.500',
            502 => 'errors.502',
            503 => 'errors.503',
            504 => 'errors.504'
        ];

        $viewName = $viewMap[$statusCode] ?? 'errors.generic';
        
        if (view()->exists($viewName)) {
            return response()->view($viewName, ['exception' => $e], $statusCode);
        }

        return response()->view('errors.generic', ['exception' => $e], $statusCode);
    }

    /**
     * Handle Generic Exception.
     */
    protected function handleGenericException(Throwable $e, Request $request): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Error interno del servidor',
                'message' => app()->environment('local') ? $e->getMessage() : 'Ha ocurrido un error inesperado'
            ], 500);
        }

        // En desarrollo, mostrar errores detallados
        if (app()->environment('local')) {
            throw $e;
        }

        // En producción, mostrar página de error genérica
        return response()->view('errors.generic', ['exception' => $e], 500);
    }
}