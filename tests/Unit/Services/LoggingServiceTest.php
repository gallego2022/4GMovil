<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\LoggingService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;


class LoggingServiceTest extends TestCase
{

    protected LoggingService $loggingService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loggingService = new LoggingService();
    }

    /** @test */
    public function it_can_log_info_messages()
    {
        Log::shouldReceive('info')->once()->with('Test info message', ['key' => 'value']);
        
        $this->loggingService->info('Test info message', ['key' => 'value']);
    }

    /** @test */
    public function it_can_log_error_messages()
    {
        Log::shouldReceive('error')->once()->with('Test error message', ['key' => 'value']);
        
        $this->loggingService->error('Test error message', ['key' => 'value']);
    }

    /** @test */
    public function it_can_log_warning_messages()
    {
        Log::shouldReceive('warning')->once()->with('Test warning message', ['key' => 'value']);
        
        $this->loggingService->warning('Test warning message', ['key' => 'value']);
    }

    /** @test */
    public function it_can_log_debug_messages()
    {
        Log::shouldReceive('debug')->once()->with('Test debug message', ['key' => 'value']);
        
        $this->loggingService->debug('Test debug message', ['key' => 'value']);
    }

    /** @test */
    public function it_can_log_crud_operations()
    {
        Log::shouldReceive('info')->once()->with('Usuario realizó acción: Operación CRUD: CREATE en User', [
            'action' => 'Operación CRUD: CREATE en User',
            'operation' => 'CREATE',
            'model' => 'User',
            'model_id' => 123,
            'user_id' => null,
            'ip_address' => null,
            'user_agent' => null
        ]);
        
        $this->loggingService->crudOperation('CREATE', 'User', 123);
    }

    /** @test */
    public function it_can_log_user_actions()
    {
        Log::shouldReceive('info')->once()->with('User action: test_action', [
            'action' => 'test_action',
            'user_id' => 123,
            'ip' => '127.0.0.1',
            'user_agent' => 'Test Browser'
        ]);
        
        $this->loggingService->userAction('test_action', [
            'user_id' => 123,
            'ip' => '127.0.0.1',
            'user_agent' => 'Test Browser'
        ]);
    }

    /** @test */
    public function it_can_log_database_errors()
    {
        $exception = new \Exception('Database connection failed', 500);
        
        Log::shouldReceive('error')->once()->with('Error de base de datos en: SELECT', [
            'operation' => 'SELECT',
            'error_message' => 'Database connection failed',
            'error_code' => 500,
            'file' => $exception->getFile(),
            'line' => $exception->getLine()
        ]);
        
        $this->loggingService->databaseError('SELECT', $exception);
    }

    /** @test */
    public function it_can_log_payment_operations()
    {
        Log::shouldReceive('info')->once()->with('Usuario realizó acción: Operación de pago: charge via Stripe', [
            'action' => 'Operación de pago: charge via Stripe',
            'payment_operation' => 'charge',
            'payment_gateway' => 'Stripe',
            'user_id' => null,
            'ip_address' => null,
            'user_agent' => null
        ]);
        
        $this->loggingService->paymentOperation('charge', 'Stripe');
    }

    /** @test */
    public function it_can_log_validation_errors()
    {
        $errors = ['email' => 'El email es requerido'];
        
        Log::shouldReceive('warning')->once()->with('Error de validación', [
            'validation_errors' => $errors
        ]);
        
        $this->loggingService->validationError($errors);
    }

    /** @test */
    public function it_can_log_with_context()
    {
        $context = ['user_id' => 123, 'action' => 'test'];
        
        Log::shouldReceive('info')->once()->with('Test message', $context);
        
        $this->loggingService->info('Test message', $context);
    }

    /** @test */
    public function it_can_log_without_context()
    {
        Log::shouldReceive('info')->once()->with('Test message', []);
        
        $this->loggingService->info('Test message');
    }
}
