<?php

namespace App\Http\Controllers;

use App\Services\UsuarioService;
use App\Models\Usuario;
use Illuminate\Http\Request;
use App\Traits\AdminCheck;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    use AdminCheck;

    protected $usuarioService;

    public function __construct(UsuarioService $usuarioService)
    {
        $this->usuarioService = $usuarioService;
    }

    public function index()
    {
        if ($respuesta = $this->verificarAdmin()) {
            return $respuesta;
        }
        
        $usuarios = $this->usuarioService->getAllUsers();
        return view('pages.admin.usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        if ($respuesta = $this->verificarAdmin()) {
            return $respuesta;
        }

        return view('pages.admin.usuarios.create');
    }

    public function store(Request $request)
    {
        if ($respuesta = $this->verificarAdmin()) {
            return $respuesta;
        }

        $request->validate([
            'nombre_usuario' => 'required|string|max:255',
            'correo_electronico' => 'required|email|unique:usuarios,correo_electronico',
            'password' => 'required|min:6|confirmed',
            'rol' => 'required|in:admin,cliente,invitado',
            'estado' => 'required|boolean',
        ]);

        $userData = $request->except('password', 'password_confirmation');
        $userData['contrasena'] = Hash::make($request->password);

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
            'contrasena' => 'nullable|min:6|confirmed',
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
