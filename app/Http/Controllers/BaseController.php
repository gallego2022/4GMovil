<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

abstract class BaseController extends \Illuminate\Routing\Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Respuesta exitosa estándar para API
     */
    protected function successResponse($data = null, string $message = 'Operación exitosa', int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'timestamp' => now()->toISOString()
        ], $code);
    }

    /**
     * Respuesta de error estándar para API
     */
    protected function errorResponse(string $message = 'Error en la operación', int $code = 400, $errors = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
            'timestamp' => now()->toISOString()
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

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
}
