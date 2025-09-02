<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class DatabaseErrorHandler
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
        } catch (QueryException $e) {
            // Log del error con contexto detallado
            Log::error('Error de base de datos detectado', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'user_id' => Auth::check() ? Auth::id() : 'guest',
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'error_code' => $e->getCode(),
                'error_message' => $e->getMessage(),
                'sql' => $e->getSql(),
                'bindings' => $e->getBindings(),
                'trace' => $e->getTraceAsString()
            ]);

            // Determinar el tipo de error específico
            $errorDetails = $this->analyzeDatabaseError($e);
            
            // Si es una petición AJAX, devolver JSON con error específico
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error_type' => $errorDetails['type'],
                    'message' => $errorDetails['user_message'],
                    'technical_details' => config('app.debug') ? $errorDetails['technical_details'] : null,
                    'error_code' => $e->getCode()
                ], 500);
            }

            // Para peticiones normales, redirigir con mensaje de error
            return redirect()->back()->withErrors([
                'database_error' => $errorDetails['user_message']
            ])->withInput();
        }
    }

    /**
     * Analiza el error de base de datos y proporciona información específica
     */
    private function analyzeDatabaseError(QueryException $e): array
    {
        $errorCode = $e->getCode();
        $errorMessage = $e->getMessage();

        // Error de columna no encontrada
        if (str_contains($errorMessage, "Column not found")) {
            return [
                'type' => 'missing_column',
                'user_message' => 'Error de configuración de la base de datos: Columna no encontrada',
                'technical_details' => [
                    'error_code' => $errorCode,
                    'sql_error' => $errorMessage,
                    'suggestion' => 'Verificar que la migración se haya ejecutado correctamente'
                ]
            ];
        }

        // Error de tabla no encontrada
        if (str_contains($errorMessage, "Table") && str_contains($errorMessage, "doesn't exist")) {
            return [
                'type' => 'missing_table',
                'user_message' => 'Error de configuración de la base de datos: Tabla no encontrada',
                'technical_details' => [
                    'error_code' => $errorCode,
                    'sql_error' => $errorMessage,
                    'suggestion' => 'Ejecutar las migraciones de la base de datos'
                ]
            ];
        }

        // Error de clave foránea
        if (str_contains($errorMessage, "foreign key constraint fails")) {
            return [
                'type' => 'foreign_key_constraint',
                'user_message' => 'Error de integridad de datos: Referencia no válida',
                'technical_details' => [
                    'error_code' => $errorCode,
                    'sql_error' => $errorMessage,
                    'suggestion' => 'Verificar que los datos referenciados existan'
                ]
            ];
        }

        // Error de sintaxis SQL
        if (str_contains($errorMessage, "syntax error")) {
            return [
                'type' => 'sql_syntax',
                'user_message' => 'Error de sintaxis en la consulta de base de datos',
                'technical_details' => [
                    'error_code' => $errorCode,
                    'sql_error' => $errorMessage,
                    'suggestion' => 'Revisar la consulta SQL generada'
                ]
            ];
        }

        // Error de conexión
        if (str_contains($errorMessage, "Connection refused") || str_contains($errorMessage, "Connection timed out")) {
            return [
                'type' => 'connection_error',
                'user_message' => 'Error de conexión con la base de datos',
                'technical_details' => [
                    'error_code' => $errorCode,
                    'sql_error' => $errorMessage,
                    'suggestion' => 'Verificar la conexión a la base de datos'
                ]
            ];
        }

        // Error genérico de base de datos
        return [
            'type' => 'database_error',
            'user_message' => 'Error en la base de datos',
            'technical_details' => [
                'error_code' => $errorCode,
                'sql_error' => $errorMessage,
                'suggestion' => 'Revisar los logs del sistema para más detalles'
            ]
        ];
    }
}
