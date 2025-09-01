<?php

namespace App\Http\Controllers;

use App\Services\UsuarioService;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    protected $usuarioService;

    public function __construct(UsuarioService $usuarioService)
    {
        $this->usuarioService = $usuarioService;
    }

    public function index()
    {
        $usuarios = $this->usuarioService->getAllUsers();
        return view('pages.admin.usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        return view('pages.admin.usuarios.create');
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
            return redirect()
                ->route('usuarios.index')
                ->with('success', 'Usuario creado exitosamente');
        }

        return back()
            ->withInput()
            ->with('error', $result['message']);
    }

    public function show($id)
    {
        $data = $this->usuarioService->getUserById($id);
        
        if (!$data) {
            return redirect()->route('usuarios.index')->with('error', 'Usuario no encontrado.');
        }

        return view('pages.admin.usuarios.show', $data);
    }

    public function edit($id)
    {
        $data = $this->usuarioService->getUserById($id);
        
        if (!$data) {
            return redirect()->route('usuarios.index')->with('error', 'Usuario no encontrado.');
        }

        return view('pages.admin.usuarios.edit', $data);
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

        return redirect()
            ->route('usuarios.index')
            ->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    public function destroy($id)
    {
        $result = $this->usuarioService->deleteUser($id);

        return redirect()
            ->route('usuarios.index')
            ->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    public function cambiarEstado($id)
    {
        $result = $this->usuarioService->toggleUserStatus($id);

        return redirect()
            ->route('usuarios.index')
            ->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    public function asignarRol(Request $request, $id)
    {
        $request->validate([
            'rol' => 'required|in:cliente,admin,vendedor'
        ]);

        $result = $this->usuarioService->updateRole($id, $request->rol);

        return redirect()
            ->route('usuarios.index')
            ->with($result['success'] ? 'success' : 'error', $result['message']);
    }
}
