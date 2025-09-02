<?php

namespace App\Services\Base;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;

abstract class BaseService
{
    /**
     * Ejecuta una operación en transacción con manejo de errores
     */
    protected function executeInTransaction(callable $callback, string $operation = 'operación')
    {
        try {
            DB::beginTransaction();
            $result = $callback();
            DB::commit();
            
            Log::info("{$operation} completada exitosamente");
            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Error en {$operation}: " . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Valida que un modelo exista
     */
    protected function validateModelExists($model, $id, string $modelName = 'Modelo'): void
    {
        if (!$model) {
            throw new Exception("{$modelName} con ID {$id} no encontrado");
        }
    }

    /**
     * Valida permisos de usuario
     */
    protected function validateUserPermission($user, string $requiredRole, string $operation = 'operación'): void
    {
        if (!$user || $user->rol !== $requiredRole) {
            throw new Exception("No tienes permisos para realizar {$operation}");
        }
    }

    /**
     * Valida stock disponible
     */
    protected function validateStock($stock, $cantidad, string $productoNombre = 'Producto'): void
    {
        if ($stock < $cantidad) {
            throw new Exception("Stock insuficiente para {$productoNombre}. Disponible: {$stock}, Solicitado: {$cantidad}");
        }
    }

    /**
     * Registra operaciones importantes
     */
    protected function logOperation(string $operation, array $data = [], string $level = 'info'): void
    {
        $logData = array_merge(['operation' => $operation], $data);
        
        switch ($level) {
            case 'error':
                Log::error($operation, $logData);
                break;
            case 'warning':
                Log::warning($operation, $logData);
                break;
            default:
                Log::info($operation, $logData);
        }
    }

    /**
     * Maneja errores de validación
     */
    protected function handleValidationError(array $errors, string $message = 'Error de validación'): array
    {
        $this->logOperation('validation_error', ['errors' => $errors], 'warning');
        
        return [
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ];
    }

    /**
     * Formatea respuesta exitosa
     */
    protected function formatSuccessResponse($data = null, string $message = 'Operación exitosa'): array
    {
        return [
            'success' => true,
            'message' => $message,
            'data' => $data,
            'timestamp' => now()->toISOString()
        ];
    }

    /**
     * Formatea respuesta de error
     */
    protected function formatErrorResponse(string $message = 'Error en la operación', $errors = null): array
    {
        $response = [
            'success' => false,
            'message' => $message,
            'timestamp' => now()->toISOString()
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        return $response;
    }
}
