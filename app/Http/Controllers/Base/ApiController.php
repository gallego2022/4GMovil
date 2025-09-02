<?php

namespace App\Http\Controllers\Base;

use App\Http\Controllers\Base\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

abstract class ApiController extends BaseController
{
    /**
     * Valida la request y retorna los datos validados
     */
    protected function validateRequest(Request $request, array $rules, array $messages = []): array
    {
        $validator = Validator::make($request->all(), $rules, $messages);
        
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
        
        return $validator->validated();
    }

    /**
     * Respuesta de éxito con datos
     */
    protected function success($data = null, string $message = 'Operación exitosa', int $code = 200): JsonResponse
    {
        return $this->successResponse($data, $message, $code);
    }

    /**
     * Respuesta de error
     */
    protected function error(string $message = 'Error en la operación', int $code = 400, $errors = null): JsonResponse
    {
        return $this->errorResponse($message, $code, $errors);
    }

    /**
     * Respuesta de validación fallida
     */
    protected function validationError($errors, string $message = 'Error de validación'): JsonResponse
    {
        return $this->validationErrorResponse($errors, $message);
    }

    /**
     * Respuesta de recurso no encontrado
     */
    protected function notFound(string $message = 'Recurso no encontrado'): JsonResponse
    {
        return $this->notFoundResponse($message);
    }

    /**
     * Respuesta de acceso denegado
     */
    protected function forbidden(string $message = 'Acceso denegado'): JsonResponse
    {
        return $this->forbiddenResponse($message);
    }

    /**
     * Respuesta de servidor interno
     */
    protected function serverError(string $message = 'Error interno del servidor'): JsonResponse
    {
        return $this->serverErrorResponse($message);
    }

    /**
     * Respuesta de creación exitosa
     */
    protected function created($data = null, string $message = 'Recurso creado exitosamente'): JsonResponse
    {
        return $this->createdResponse($data, $message);
    }

    /**
     * Respuesta de actualización exitosa
     */
    protected function updated($data = null, string $message = 'Recurso actualizado exitosamente'): JsonResponse
    {
        return $this->updatedResponse($data, $message);
    }

    /**
     * Respuesta de eliminación exitosa
     */
    protected function deleted(string $message = 'Recurso eliminado exitosamente'): JsonResponse
    {
        return $this->deletedResponse($message);
    }

    /**
     * Respuesta de lista paginada
     */
    protected function paginated($data, string $message = 'Lista obtenida exitosamente'): JsonResponse
    {
        return $this->paginatedResponse($data, $message);
    }

    /**
     * Maneja excepciones de validación
     */
    protected function handleValidationException(ValidationException $e): JsonResponse
    {
        return $this->validationError($e->errors(), 'Los datos proporcionados no son válidos');
    }

    /**
     * Maneja excepciones generales
     */
    protected function handleException(\Exception $e): JsonResponse
    {
        $message = config('app.debug') ? $e->getMessage() : 'Ha ocurrido un error inesperado';
        $code = $e->getCode() ?: 500;
        
        if ($code < 100 || $code > 599) {
            $code = 500;
        }
        
        return $this->error($message, $code);
    }

    /**
     * Verifica si el usuario está autenticado
     */
    protected function requireAuth(): void
    {
        if (!\Illuminate\Support\Facades\Auth::check()) {
            abort(401, 'Usuario no autenticado');
        }
    }

    /**
     * Verifica si el usuario tiene un rol específico
     */
    protected function requireRole(string $role): void
    {
        $this->requireAuth();
        
        if (\Illuminate\Support\Facades\Auth::user()->rol !== $role) {
            abort(403, 'No tienes permisos para realizar esta acción');
        }
    }

    /**
     * Verifica si el usuario tiene al menos uno de los roles especificados
     */
    protected function requireAnyRole(array $roles): void
    {
        $this->requireAuth();
        
        if (!in_array(\Illuminate\Support\Facades\Auth::user()->rol, $roles)) {
            abort(403, 'No tienes permisos para realizar esta acción');
        }
    }

    /**
     * Obtiene el usuario autenticado o null
     */
    protected function getAuthUser()
    {
        return \Illuminate\Support\Facades\Auth::user();
    }

    /**
     * Obtiene el ID del usuario autenticado
     */
    protected function getAuthUserId(): ?int
    {
        return \Illuminate\Support\Facades\Auth::id();
    }

    /**
     * Verifica si la request es de tipo AJAX
     */
    protected function isAjax(Request $request): bool
    {
        return $request->ajax() || $request->wantsJson();
    }

    /**
     * Obtiene parámetros de paginación de la request
     */
    protected function getPaginationParams(Request $request): array
    {
        return [
            'per_page' => (int) $request->get('per_page', 15),
            'page' => (int) $request->get('page', 1),
            'sort_by' => $request->get('sort_by', 'id'),
            'sort_direction' => $request->get('sort_direction', 'desc')
        ];
    }

    /**
     * Obtiene parámetros de filtrado de la request
     */
    protected function getFilterParams(Request $request): array
    {
        $filters = $request->only(['search', 'category', 'brand', 'status', 'date_from', 'date_to']);
        
        // Eliminar filtros vacíos
        return array_filter($filters, function ($value) {
            return $value !== null && $value !== '';
        });
    }
}
