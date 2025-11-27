<?php

namespace App\Http\Controllers\Admin;

use App\Services\User\UsuarioService;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View as ViewView;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Base\WebController;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Redirect;

/**
 * Controlador de administración para gestionar Usuarios.
 *
 * Provee acciones CRUD, cambio de estado y asignación de roles, delegando
 * la lógica de negocio al servicio `UsuarioService`.
 */
class UsuarioController extends WebController
{
    protected UsuarioService $usuarioService;

    public function __construct(UsuarioService $usuarioService)
    {
        $this->usuarioService = $usuarioService;
    }

    /**
     * Listado de usuarios.
     *
     * @return ViewView Vista con el listado de usuarios
     */
    public function index(): ViewView
    {
        $usuarios = $this->usuarioService->getAllUsers();
        return View::make('pages.admin.usuarios.index', compact('usuarios'));
    }

    /**
     * Formulario de creación de usuario.
     *
     * @return ViewView Vista del formulario de creación
     */
    public function create(): ViewView
    {
        return View::make('pages.admin.usuarios.create');
    }

    /**
     * Crear un usuario.
     *
     * @param Request $request Datos del usuario
     * @return RedirectResponse Redirección con mensaje de estado
     */
    public function store(Request $request): RedirectResponse
    {

        $request->validate([
            'nombre_usuario' => 'required|string|max:255',
            'correo_electronico' => 'required|email|unique:usuarios,correo_electronico',
            'contrasena' => ['required', 'string', 'min:8', 'confirmed', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/'],
            'telefono' => ['required', 'digits:10', 'regex:/^[0-9]+$/'],
            'rol' => 'required|in:admin,cliente',
            'estado' => 'required|boolean',
        ]); 
        

        $userData = $request->except('contrasena_confirmation');
        $userData['contrasena'] = Hash::make($request->contrasena);

        $result = $this->usuarioService->createUser($userData);

        if ($result['success']) {
            return Redirect::route('usuarios.index')->with('mensaje', 'Usuario Creado')->with('tipo', 'success');
        }

        return Redirect::back()->withInput()->with('mensaje', $result['message'])->with('tipo', 'error');
    }

    /**
     * Mostrar detalle de un usuario.
     *
     * @param int $id Identificador del usuario
     * @return ViewView Vista de detalle o redirección si no existe
     */
    public function show(int $id): ViewView|RedirectResponse
    {
        $data = $this->usuarioService->getUserById($id);
        
        if (!$data) {
            return Redirect::route('usuarios.index')->with('error', 'Usuario no encontrado.');
        }

        return View::make('pages.admin.usuarios.show', compact('data'));
    }

    /**
     * Formulario de edición de usuario.
     *
     * @param int $id Identificador del usuario
     * @return ViewView Vista de edición o redirección si no existe
     */
    public function edit(int $id): ViewView|RedirectResponse
    {
        $data = $this->usuarioService->getUserById($id);
        
        if (!$data) {
            return Redirect::route('usuarios.index')->with('error', 'Usuario no encontrado.');
        }

        return View::make('pages.admin.usuarios.edit', compact('data'));
    }

    /**
     * Actualizar un usuario.
     *
     * @param Request $request Datos a actualizar
     * @param int $id Identificador del usuario
     * @return RedirectResponse Redirección con mensaje de estado
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'nombre_usuario' => 'required|string|max:255',
            'correo_electronico' => "required|email|unique:usuarios,correo_electronico,{$id},usuario_id",
            'telefono' => ['nullable', 'digits:10', 'regex:/^[0-9]+$/'], 
            'contrasena' => 'nullable|min:8|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/',
            'foto_perfil' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'rol' => 'required',
            'estado' => 'required|boolean',
        ]);

        $result = $this->usuarioService->updateUser(
            $id,
            $request->except(['foto_perfil']),
            $request->file('foto_perfil')
        );

        if ($result['success']) {
            return Redirect::route('usuarios.index')->with('mensaje', 'Usuario Actualizado')->with('tipo', 'success');
        }
        
        return Redirect::route('usuarios.index')->with('mensaje', $result['message'])->with('tipo', 'error');
    }

    /**
     * Eliminar un usuario.
     *
     * @param int $id Identificador del usuario
     * @return RedirectResponse Redirección con mensaje de estado
     */
    public function destroy(int $id): RedirectResponse
    {
        $result = $this->usuarioService->deleteUser($id);

        if ($result['success']) {
            return Redirect::route('usuarios.index')->with('mensaje', 'Usuario Eliminado')->with('tipo', 'success');
        }
        
        return Redirect::route('usuarios.index')->with('mensaje', $result['message'])->with('tipo', 'error');
    }

    /**
     * Cambiar estado activo/inactivo de un usuario.
     *
     * @param int $id Identificador del usuario
     * @return RedirectResponse Redirección con mensaje de estado
     */
    public function cambiarEstado(int $id): RedirectResponse
    {
        $result = $this->usuarioService->toggleUserStatus($id);

        if ($result['success']) {
            return Redirect::route('usuarios.index')->with('mensaje', 'Estado de Usuario Actualizado')->with('tipo', 'success');
        }
        
        return Redirect::route('usuarios.index')->with('mensaje', $result['message'])->with('tipo', 'error');
    }

    /**
     * Asignar rol al usuario.
     *
     * @param Request $request Contiene el rol
     * @param int $id Identificador del usuario
     * @return RedirectResponse Redirección con mensaje de estado
     */
    public function asignarRol(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'rol' => 'required|in:cliente,admin,vendedor'
        ]);

        $result = $this->usuarioService->updateRole($id, $request->rol);

        if ($result['success']) {
            return Redirect::route('usuarios.index')->with('mensaje', 'Rol de Usuario Actualizado')->with('tipo', 'success');
        }
        
        return Redirect::route('usuarios.index')->with('mensaje', $result['message'])->with('tipo', 'error');
    }
}
