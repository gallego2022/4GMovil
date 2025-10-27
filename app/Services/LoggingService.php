<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Auth;

class LoggingService
{
    /**
     * Log de información general
     */
    public function info(string $message, array $context = []): void
    {
        $this->log('info', $message, $context);
    }

    /**
     * Log de advertencias
     */
    public function warning(string $message, array $context = []): void
    {
        $this->log('warning', $message, $context);
    }

    /**
     * Log de errores
     */
    public function error(string $message, array $context = []): void
    {
        $this->log('error', $message, $context);
    }

    /**
     * Log de debug
     */
    public function debug(string $message, array $context = []): void
    {
        $this->log('debug', $message, $context);
    }

    /**
     * Log de acciones del usuario
     */
    public function userAction(string $action, array $context = []): void
    {
        $context['action'] = $action;
        $context['user_id'] = Auth::id();
        $context['ip_address'] = Request::ip();
        $context['user_agent'] = Request::userAgent();
        
        $this->info("Usuario realizó acción: {$action}", $context);
    }

    /**
     * Log de operaciones CRUD
     */
    public function crudOperation(string $operation, string $model, $id = null, array $context = []): void
    {
        $context['operation'] = $operation;
        $context['model'] = $model;
        $context['model_id'] = $id;
        
        $this->info("Operación CRUD: {$operation} en {$model}", $context);
    }

    /**
     * Log de errores de validación
     */
    public function validationError(array $errors, array $context = []): void
    {
        $context['validation_errors'] = $errors;
        $this->warning('Error de validación', $context);
    }

    /**
     * Log de errores de base de datos
     */
    public function databaseError(string $operation, \Exception $exception, array $context = []): void
    {
        $context['operation'] = $operation;
        $context['error_message'] = $exception->getMessage();
        $context['error_code'] = $exception->getCode();
        $context['file'] = $exception->getFile();
        $context['line'] = $exception->getLine();
        
        $this->error("Error de base de datos en: {$operation}", $context);
    }

    /**
     * Log de operaciones de pago
     */
    public function paymentOperation(string $operation, string $gateway, array $context = []): void
    {
        $context['payment_operation'] = $operation;
        $context['payment_gateway'] = $gateway;
        
        $this->info("Operación de pago: {$operation} via {$gateway}", $context);
    }

    /**
     * Log de operaciones de inventario
     */
    public function inventoryOperation(string $operation, string $product, int $quantity, array $context = []): void
    {
        $context['inventory_operation'] = $operation;
        $context['product'] = $product;
        $context['quantity'] = $quantity;
        
        $this->info("Operación de inventario: {$operation} - {$product} x{$quantity}", $context);
    }

    /**
     * Log de performance
     */
    public function performance(string $operation, float $executionTime, array $context = []): void
    {
        $context['execution_time'] = $executionTime;
        $context['operation'] = $operation;
        
        if ($executionTime > 1.0) {
            $this->warning("Operación lenta: {$operation} - {$executionTime}s", $context);
        } else {
            $this->debug("Performance: {$operation} - {$executionTime}s", $context);
        }
    }

    /**
     * Log de seguridad
     */
    public function security(string $event, array $context = []): void
    {
        $context['security_event'] = $event;
        $context['ip_address'] = Request::ip();
        $context['user_agent'] = Request::userAgent();
        
        $this->warning("Evento de seguridad: {$event}", $context);
    }

    /**
     * Log de API
     */
    public function apiRequest(string $endpoint, string $method, array $context = []): void
    {
        $context['api_endpoint'] = $endpoint;
        $context['api_method'] = $method;
        $context['response_time'] = microtime(true) - (defined('LARAVEL_START') ? LARAVEL_START : microtime(true));
        
        $this->info("API Request: {$method} {$endpoint}", $context);
    }

    /**
     * Método principal de logging
     */
    private function log(string $level, string $message, array $context = []): void
    {
        // Agregar contexto común
        $context = array_merge($context, [
            'timestamp' => now()->toISOString(),
            'request_id' => uniqid(),
            'url' => Request::fullUrl(),
            'method' => Request::method(),
            'user_id' => Auth::id(),
            'session_id' => session()->getId(),
        ]);

        // Filtrar datos sensibles
        $context = $this->filterSensitiveData($context);

        // Log según el nivel
        switch ($level) {
            case 'info':
                Log::info($message, $context);
                break;
            case 'warning':
                Log::warning($message, $context);
                break;
            case 'error':
                Log::error($message, $context);
                break;
            case 'debug':
                Log::debug($message, $context);
                break;
            default:
                Log::info($message, $context);
        }
    }

    /**
     * Filtrar datos sensibles del contexto
     */
    private function filterSensitiveData(array $context): array
    {
        $sensitiveFields = [
            'password', 'password_confirmation', 'token', 'api_key',
            'secret', 'credit_card', 'cvv', 'ssn', 'dni'
        ];

        foreach ($sensitiveFields as $field) {
            if (isset($context[$field])) {
                $context[$field] = '***HIDDEN***';
            }
        }

        return $context;
    }

    /**
     * Crear contexto de transacción
     */
    public function createTransactionContext(string $transactionId): array
    {
        return [
            'transaction_id' => $transactionId,
            'user_id' => Auth::id(),
            'ip_address' => Request::ip(),
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Log de transacción completa
     */
    public function transactionLog(string $transactionId, string $status, array $context = []): void
    {
        $context['transaction_id'] = $transactionId;
        $context['transaction_status'] = $status;
        
        $this->info("Transacción {$status}: {$transactionId}", $context);
    }
}
