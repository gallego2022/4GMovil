<?php

namespace App\Http\Controllers\Base;

use App\Http\Controllers\Base\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

abstract class WebController extends BaseController
{
    // Nota: No definimos __construct para evitar "Cannot call constructor" cuando la clase padre no tiene constructor.

    /**
     * Aplicar localización en cada método
     */
    protected function applyLocalization()
    {
        \App\Helpers\ViewHelper::applyLocalization();
    }

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
     * Redirecciona con mensaje de éxito
     */
    protected function redirectSuccess(string $route, string $message = 'Operación completada exitosamente', array $parameters = []): RedirectResponse
    {
        return Redirect::route($route, $parameters)->with('mensaje', $message)->with('tipo', 'success');
    }

    /**
     * Redirecciona con mensaje de error
     */
    protected function redirectError(string $route, string $message = 'Ha ocurrido un error', array $parameters = []): RedirectResponse
    {
        return Redirect::route($route, $parameters)->with('mensaje', $message)->with('tipo', 'error');
    }

    /**
     * Redirecciona con mensaje de advertencia
     */
    protected function redirectWarning(string $route, string $message = 'Advertencia', array $parameters = []): RedirectResponse
    {
        return Redirect::route($route, $parameters)->with('mensaje', $message)->with('tipo', 'warning');
    }

    /**
     * Redirecciona con mensaje de información
     */
    protected function redirectInfo(string $route, string $message = 'Información', array $parameters = []): RedirectResponse
    {
        return Redirect::route($route, $parameters)->with('mensaje', $message)->with('tipo', 'info');
    }

    /**
     * Redirecciona de vuelta con mensaje de éxito
     */
    protected function backSuccess(string $message = 'Operación completada exitosamente'): RedirectResponse
    {
        return Redirect::back()->with('mensaje', $message)->with('tipo', 'success');
    }

    /**
     * Redirecciona de vuelta con mensaje de error
     */
    protected function backError(string $message = 'Ha ocurrido un error'): RedirectResponse
    {
        return Redirect::back()->with('mensaje', $message)->with('tipo', 'error');
    }

    /**
     * Redirecciona de vuelta con mensaje de advertencia
     */
    protected function backWarning(string $message = 'Advertencia'): RedirectResponse
    {
        return Redirect::back()->with('mensaje', $message)->with('tipo', 'warning');
    }

    /**
     * Redirecciona de vuelta con mensaje de información
     */
    protected function backInfo(string $message = 'Información'): RedirectResponse
    {
        return Redirect::back()->with('mensaje', $message)->with('tipo', 'info');
    }

    /**
     * Redirecciona con datos de entrada para mantener formularios
     */
    protected function redirectWithInput(string $route, string $message = 'Por favor, corrige los errores', array $parameters = []): RedirectResponse
    {
        return Redirect::route($route, $parameters)
            ->withInput()
            ->with('mensaje', $message)
            ->with('tipo', 'error');
    }

    /**
     * Redirecciona de vuelta con datos de entrada
     */
    protected function backWithInput(string $message = 'Por favor, corrige los errores'): RedirectResponse
    {
        return Redirect::back()
            ->withInput()
            ->with('mensaje', $message)
            ->with('tipo', 'error');
    }

    /**
     * Maneja excepciones de validación para web
     */
    protected function handleValidationException(ValidationException $e, string $redirectRoute = null, array $parameters = []): RedirectResponse
    {
        $message = 'Por favor, corrige los errores en el formulario';
        
        if ($redirectRoute) {
            return $this->redirectWithInput($redirectRoute, $message, $parameters);
        }
        
        return $this->backWithInput($message);
    }

    /**
     * Maneja excepciones generales para web
     */
    protected function handleException(\Exception $e, string $redirectRoute = null, array $parameters = []): RedirectResponse
    {
        $message = Config::get('app.debug') ? $e->getMessage() : 'Ha ocurrido un error inesperado';
        
        if ($redirectRoute) {
            return $this->redirectError($redirectRoute, $message, $parameters);
        }
        
        return $this->backError($message);
    }

    /**
     * Verifica si el usuario está autenticado
     */
    protected function requireAuth(): void
    {
        if (!Auth::check()) {
            abort(401, 'Usuario no autenticado');
        }
    }

    /**
     * Verifica si el usuario tiene un rol específico
     */
    protected function requireRole(string $role): void
    {
        $this->requireAuth();
        
        if (Auth::user()->rol !== $role) {
            abort(403, 'No tienes permisos para realizar esta acción');
        }
    }

    /**
     * Verifica si el usuario tiene al menos uno de los roles especificados
     */
    protected function requireAnyRole(array $roles): void
    {
        $this->requireAuth();
        
        if (!in_array(Auth::user()->rol, $roles)) {
            abort(403, 'No tienes permisos para realizar esta acción');
        }
    }

    /**
     * Obtiene el usuario autenticado o null
     */
    protected function getAuthUser()
    {
        return Auth::user();
    }

    /**
     * Obtiene el ID del usuario autenticado
     */
    protected function getAuthUserId(): ?int
    {
        return Auth::id();
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

    /**
     * Agrega mensaje flash a la sesión
     */
    protected function addFlashMessage(string $message, string $type = 'info'): void
    {
        Session::flash('mensaje', $message);
        Session::flash('tipo', $type);
    }

    /**
     * Agrega múltiples mensajes flash
     */
    protected function addFlashMessages(array $messages): void
    {
        foreach ($messages as $type => $message) {
            $this->addFlashMessage($message, $type);
        }
    }

    /**
     * Verifica si hay mensajes flash en la sesión
     */
    protected function hasFlashMessages(): bool
    {
        return Session::has('mensaje') || Session::has('tipo');
    }

    /**
     * Obtiene los mensajes flash de la sesión
     */
    protected function getFlashMessages(): array
    {
        return [
            'mensaje' => Session::get('mensaje'),
            'tipo' => Session::get('tipo')
        ];
    }
}
