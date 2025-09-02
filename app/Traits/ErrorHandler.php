<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

trait ErrorHandler
{
    /**
     * Maneja errores de base de datos de manera específica
     */
    protected function handleDatabaseError(QueryException $e, $context = []): JsonResponse|RedirectResponse
    {
        $errorDetails = $this->analyzeDatabaseError($e);
        
        // Log del error con contexto
        Log::error('Error de base de datos en controlador', array_merge([
            'controller' => get_class($this),
            'method' => debug_backtrace()[1]['function'] ?? 'unknown',
            'error_code' => $e->getCode(),
            'error_message' => $e->getMessage(),
            'sql' => $e->getSql(),
            'bindings' => $e->getBindings()
        ], $context));

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => false,
                'error_type' => $errorDetails['type'],
                'message' => $errorDetails['user_message'],
                'technical_details' => config('app.debug') ? $errorDetails['technical_details'] : null,
                'error_code' => $e->getCode()
            ], 500);
        }

        return redirect()->back()->withErrors([
            'database_error' => $errorDetails['user_message']
        ])->withInput();
    }

    /**
     * Maneja errores de validación de manera específica
     */
    protected function handleValidationError(ValidationException $e, $context = []): JsonResponse|RedirectResponse
    {
        // Log del error con contexto
        Log::warning('Error de validación en controlador', array_merge([
            'controller' => get_class($this),
            'method' => debug_backtrace()[1]['function'] ?? 'unknown',
            'errors' => $e->errors(),
            'input' => request()->except(['password', 'password_confirmation'])
        ], $context));

        if (request()->ajax() || request()->wantsJson()) {
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
     * Maneja errores de modelo no encontrado
     */
    protected function handleModelNotFoundError(ModelNotFoundException $e, $context = []): JsonResponse|RedirectResponse
    {
        $model = class_basename($e->getModel());
        
        // Log del error con contexto
        Log::warning('Modelo no encontrado en controlador', array_merge([
            'controller' => get_class($this),
            'method' => debug_backtrace()[1]['function'] ?? 'unknown',
            'model' => $model,
            'id' => $e->getIds()
        ], $context));

        if (request()->ajax() || request()->wantsJson()) {
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
     * Maneja errores genéricos
     */
    protected function handleGenericError(\Exception $e, $context = []): JsonResponse|RedirectResponse
    {
        // Log del error con contexto
        Log::error('Error genérico en controlador', array_merge([
            'controller' => get_class($this),
            'method' => debug_backtrace()[1]['function'] ?? 'unknown',
            'error_code' => $e->getCode(),
            'error_message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ], $context));

        if (request()->ajax() || request()->wantsJson()) {
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

        return redirect()->back()->with('error', 'Ha ocurrido un error inesperado');
    }

    /**
     * Analiza errores de base de datos y proporciona información específica
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
                    'suggestion' => 'Verificar que la migración se haya ejecutado correctamente',
                    'action' => 'Ejecutar: php artisan migrate'
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
                    'suggestion' => 'Ejecutar las migraciones de la base de datos',
                    'action' => 'Ejecutar: php artisan migrate'
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
                    'suggestion' => 'Verificar que los datos referenciados existan',
                    'action' => 'Revisar las relaciones entre tablas'
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
                    'suggestion' => 'Revisar la consulta SQL generada',
                    'action' => 'Verificar el código del controlador'
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
                    'suggestion' => 'Verificar la conexión a la base de datos',
                    'action' => 'Revisar configuración de .env'
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
                'suggestion' => 'Revisar los logs del sistema para más detalles',
                'action' => 'Contactar al administrador del sistema'
            ]
        ];
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
            } elseif (str_contains($field, 'min')) {
                $suggestions[$field] = 'Este campo debe tener al menos el número mínimo de caracteres especificado';
            } elseif (str_contains($field, 'max')) {
                $suggestions[$field] = 'Este campo no puede exceder el número máximo de caracteres especificado';
            } elseif (str_contains($field, 'unique')) {
                $suggestions[$field] = 'Este valor ya existe en el sistema';
            } elseif (str_contains($field, 'numeric')) {
                $suggestions[$field] = 'Este campo debe ser un número';
            } elseif (str_contains($field, 'date')) {
                $suggestions[$field] = 'Este campo debe ser una fecha válida';
            }
        }
        
        return $suggestions;
    }
}
