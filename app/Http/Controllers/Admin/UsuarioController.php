<?php

namespace App\Http\Controllers\Admin;

use App\Services\UsuarioService;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Base\WebController;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Redirect;

class UsuarioController extends WebController
{
    protected $usuarioService;

    public function __construct(UsuarioService $usuarioService)
    {
        $this->usuarioService = $usuarioService;
    }

    public function index()
    {
        $usuarios = $this->usuarioService->getAllUsers();
        return View::make('pages.admin.usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        return View::make('pages.admin.usuarios.create');
    }

    public function store(Request $request)
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

    public function show($id)
    {
        $data = $this->usuarioService->getUserById($id);
        
        if (!$data) {
            return Redirect::route('usuarios.index')->with('error', 'Usuario no encontrado.');
        }

        return View::make('pages.admin.usuarios.show', compact('data'));
    }

    public function edit($id)
    {
        $data = $this->usuarioService->getUserById($id);
        
        if (!$data) {
            return Redirect::route('usuarios.index')->with('error', 'Usuario no encontrado.');
        }

        return View::make('pages.admin.usuarios.edit', compact('data'));
    }

    public function update(Request $request, $id)
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

    public function destroy($id)
    {
        $result = $this->usuarioService->deleteUser($id);

        if ($result['success']) {
            return Redirect::route('usuarios.index')->with('mensaje', 'Usuario Eliminado')->with('tipo', 'success');
        }
        
        return Redirect::route('usuarios.index')->with('mensaje', $result['message'])->with('tipo', 'error');
    }

    public function cambiarEstado($id)
    {
        $result = $this->usuarioService->toggleUserStatus($id);

        if ($result['success']) {
            return Redirect::route('usuarios.index')->with('mensaje', 'Estado de Usuario Actualizado')->with('tipo', 'success');
        }
        
        return Redirect::route('usuarios.index')->with('mensaje', $result['message'])->with('tipo', 'error');
    }

    public function asignarRol(Request $request, $id)
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
