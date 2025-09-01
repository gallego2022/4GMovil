<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

trait ApiResponse
{
    /**
     * Respuesta exitosa estándar
     */
    protected function successResponse($data = null, string $message = 'Operación exitosa', int $code = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message,
            'data' => $data,
            'timestamp' => now()->toISOString(),
            'request_id' => uniqid(),
        ];

        return response()->json($response, $code);
    }

    /**
     * Respuesta de error estándar
     */
    protected function errorResponse(string $message = 'Error en la operación', int $code = 400, $errors = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
            'timestamp' => now()->toISOString(),
            'request_id' => uniqid(),
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        // Log del error
        Log::error("API Error: {$message}", [
            'code' => $code,
            'errors' => $errors,
            'request_id' => $response['request_id'],
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'user_id' => auth()->id(),
        ]);

        return response()->json($response, $code);
    }

    /**
     * Respuesta de validación fallida
     */
    protected function validationErrorResponse($errors, string $message = 'Error de validación'): JsonResponse
    {
        return $this->errorResponse($message, 422, $errors);
    }

    /**
     * Respuesta de recurso no encontrado
     */
    protected function notFoundResponse(string $message = 'Recurso no encontrado'): JsonResponse
    {
        return $this->errorResponse($message, 404);
    }

    /**
     * Respuesta de acceso denegado
     */
    protected function forbiddenResponse(string $message = 'Acceso denegado'): JsonResponse
    {
        return $this->errorResponse($message, 403);
    }

    /**
     * Respuesta de servidor interno
     */
    protected function serverErrorResponse(string $message = 'Error interno del servidor'): JsonResponse
    {
        return $this->errorResponse($message, 500);
    }

    /**
     * Respuesta de creación exitosa
     */
    protected function createdResponse($data = null, string $message = 'Recurso creado exitosamente'): JsonResponse
    {
        return $this->successResponse($data, $message, 201);
    }

    /**
     * Respuesta de actualización exitosa
     */
    protected function updatedResponse($data = null, string $message = 'Recurso actualizado exitosamente'): JsonResponse
    {
        return $this->successResponse($data, $message, 200);
    }

    /**
     * Respuesta de eliminación exitosa
     */
    protected function deletedResponse(string $message = 'Recurso eliminado exitosamente'): JsonResponse
    {
        return $this->successResponse(null, $message, 200);
    }

    /**
     * Respuesta de lista paginada
     */
    protected function paginatedResponse($data, string $message = 'Lista obtenida exitosamente'): JsonResponse
    {
        return $this->successResponse($data, $message, 200);
    }

    /**
     * Respuesta de operación en proceso
     */
    protected function processingResponse(string $message = 'Operación en proceso'): JsonResponse
    {
        return $this->successResponse(null, $message, 202);
    }

    /**
     * Respuesta de conflicto
     */
    protected function conflictResponse(string $message = 'Conflicto en la operación', $errors = null): JsonResponse
    {
        return $this->errorResponse($message, 409, $errors);
    }

    /**
     * Respuesta de demasiadas solicitudes
     */
    protected function tooManyRequestsResponse(string $message = 'Demasiadas solicitudes', int $retryAfter = 60): JsonResponse
    {
        $response = $this->errorResponse($message, 429);
        $response->header('Retry-After', $retryAfter);
        return $response;
    }

    /**
     * Respuesta de servicio no disponible
     */
    protected function serviceUnavailableResponse(string $message = 'Servicio temporalmente no disponible'): JsonResponse
    {
        return $this->errorResponse($message, 503);
    }

    /**
     * Respuesta de gateway timeout
     */
    protected function gatewayTimeoutResponse(string $message = 'Tiempo de espera agotado'): JsonResponse
    {
        return $this->errorResponse($message, 504);
    }

    /**
     * Respuesta de datos parciales
     */
    protected function partialContentResponse($data, string $message = 'Contenido parcial'): JsonResponse
    {
        return $this->successResponse($data, $message, 206);
    }

    /**
     * Respuesta de redirección
     */
    protected function redirectResponse(string $url, string $message = 'Redirección'): JsonResponse
    {
        $response = $this->successResponse(['redirect_url' => $url], $message, 302);
        $response->header('Location', $url);
        return $response;
    }

    /**
     * Respuesta de archivo
     */
    protected function fileResponse($file, string $filename, string $mimeType): Response
    {
        return response($file, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Respuesta de imagen
     */
    protected function imageResponse($imageData, string $mimeType): Response
    {
        return response($imageData, 200, [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'public, max-age=31536000',
        ]);
    }

    /**
     * Respuesta de PDF
     */
    protected function pdfResponse($pdfData, string $filename): Response
    {
        return response($pdfData, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "inline; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Respuesta de CSV
     */
    protected function csvResponse($csvData, string $filename): Response
    {
        return response($csvData, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Respuesta de Excel
     */
    protected function excelResponse($excelData, string $filename): Response
    {
        return response($excelData, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Respuesta de JSON con headers personalizados
     */
    protected function jsonResponse($data, int $status = 200, array $headers = []): JsonResponse
    {
        $response = response()->json($data, $status);
        
        foreach ($headers as $key => $value) {
            $response->header($key, $value);
        }
        
        return $response;
    }

    /**
     * Respuesta de error con código de error personalizado
     */
    protected function errorWithCode(string $message, string $errorCode, int $httpCode = 400, $errors = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
            'error_code' => $errorCode,
            'timestamp' => now()->toISOString(),
            'request_id' => uniqid(),
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $httpCode);
    }

    /**
     * Respuesta de éxito con metadata
     */
    protected function successWithMetadata($data, array $metadata, string $message = 'Operación exitosa'): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message,
            'data' => $data,
            'metadata' => $metadata,
            'timestamp' => now()->toISOString(),
            'request_id' => uniqid(),
        ];

        return response()->json($response, 200);
    }

    /**
     * Respuesta de lista con información adicional
     */
    protected function listResponse($data, array $pagination = [], array $filters = [], string $message = 'Lista obtenida exitosamente'): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message,
            'data' => $data,
            'pagination' => $pagination,
            'filters' => $filters,
            'timestamp' => now()->toISOString(),
            'request_id' => uniqid(),
        ];

        return response()->json($response, 200);
    }
}
