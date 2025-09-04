@extends('layouts.landing')

@section('title', 'Mi Perfil - 4GMovil')
@section('meta-description', 'Gestiona tu perfil de usuario en 4GMovil')

@section('content')
<div class="bg-gray-100 dark:bg-gray-800 min-h-screen py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Mensaje de éxito/error -->
        @if(session('mensaje'))
            <div class="mb-4 rounded-md p-4 {{ session('tipo', 'success') === 'success' ? 'bg-green-50 dark:bg-green-900 text-green-800 dark:text-green-200' : 'bg-red-50 dark:bg-red-900 text-red-800 dark:text-red-200' }}">
                <div class="flex">
                    <div class="flex-shrink-0">
                        @if(session('tipo', 'success') === 'success')
                            <svg class="h-5 w-5 text-green-400 dark:text-green-300" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        @else
                            <svg class="h-5 w-5 text-red-400 dark:text-red-300" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        @endif
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium">{{ session('mensaje') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-white dark:bg-gray-900 shadow-xl rounded-lg overflow-hidden">
            <!-- Encabezado del perfil -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-800 px-6 py-8">
                <div class="flex items-center">
                    <div class="relative">
                        @if($usuario->foto_perfil)
                            @php
                                $photoUrl = \App\Helpers\PhotoHelper::getPhotoUrl($usuario->foto_perfil);
                            @endphp
                            <img src="{{ $photoUrl }}" 
                                 alt="Foto de perfil" 
                                 class="w-24 h-24 rounded-full border-4 border-white object-cover">
                            <button onclick="confirmarEliminarFoto()" 
                                    class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 transition-colors duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        @else
                            <div class="w-24 h-24 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center border-4 border-white">
                                <svg class="w-12 h-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div class="ml-6">
                        <h1 class="text-2xl font-bold text-white">{{ $usuario->nombre_usuario }}</h1>
                        <p class="text-blue-200">{{ $usuario->correo_electronico }}</p>
                        <p class="text-blue-200 mt-1">
                            <i class="fas fa-phone-alt mr-2"></i>{{ $usuario->telefono ?? 'No especificado' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Contenido del perfil -->
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Formulario de actualización -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
                        <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white">Actualizar Información</h2>
                        
                        @if($errors->any())
                            <div class="mb-4 bg-red-50 dark:bg-red-900 text-red-800 dark:text-red-200 p-4 rounded-md">
                                <ul class="list-disc list-inside text-sm">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('perfil.actualizar') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="space-y-4">
                                <div>
                                    <label for="nombre_usuario" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ __('profile.nombre_usuario') }}
                                    </label>
                                    <input type="text" 
                                           id="nombre_usuario" 
                                           name="nombre_usuario" 
                                           value="{{ old('nombre_usuario', $usuario->nombre_usuario) }}"
                                           class="mt-1 block w-full px-4 py-3 rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-gray-900 dark:text-white bg-white dark:bg-gray-700 placeholder-gray-500 dark:placeholder-gray-400">
                                </div>

                                <div>
                                    <label for="correo_electronico" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ __('profile.correo_electronico') }}
                                    </label>
                                    <input type="email" 
                                           id="correo_electronico" 
                                           name="correo_electronico" 
                                           value="{{ old('correo_electronico', $usuario->correo_electronico) }}"
                                           class="mt-1 block w-full px-4 py-3 rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-gray-900 dark:text-white bg-white dark:bg-gray-700 placeholder-gray-500 dark:placeholder-gray-400">
                                </div>

                                <div>
                                    <label for="telefono" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ __('profile.telefono') }}
                                    </label>
                                    <input type="tel" 
                                           id="telefono" 
                                           name="telefono" 
                                           value="{{ old('telefono', $usuario->telefono) }}"
                                           class="mt-1 block w-full px-4 py-3 rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-gray-900 dark:text-white bg-white dark:bg-gray-700 placeholder-gray-500 dark:placeholder-gray-400">
                                </div>

                                <div>
                                    <label for="foto_perfil" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ __('profile.foto_perfil') }}
                                    </label>
                                    <input type="file" 
                                           id="foto_perfil" 
                                           name="foto_perfil" 
                                           accept="image/*"
                                           class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 dark:file:bg-blue-900 file:text-blue-700 dark:file:text-blue-300 hover:file:bg-blue-100 dark:hover:file:bg-blue-800">
                                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                        {{ __('profile.photo_requirements') }}
                                    </p>
                                </div>

                                <div class="flex justify-end">
                                    <button type="submit" 
                                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        {{ __('profile.save_changes') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Acciones adicionales -->
                    <div class="space-y-6">
                        <!-- Cambiar Contraseña -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
                            <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white">Seguridad</h2>
                            <button type="button" 
                                   onclick="showChangePasswordModal()"
                                   class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-700 text-white rounded-md hover:bg-gray-700 dark:hover:bg-gray-600 transition-colors duration-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                </svg>
                                {{ __('profile.change_password') }}
                            </button>
                        </div>

                        <!-- Historial de Pedidos -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
                            <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white">Mis Pedidos</h2>
                            <a href="{{ route('pedidos.historial') }}" class="inline-flex items-center text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors duration-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                </svg>
                                {{ __('profile.view_order_history') }}
                            </a>
                        </div>

                        <!-- Direcciones -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
                            <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white">{{ __('profile.my_addresses') }}</h2>
                            <div class="space-y-4">
                                @if($usuario->direcciones->isEmpty())
                                    <p class="text-gray-500 dark:text-gray-400">{{ __('profile.no_addresses_saved') }}</p>
                                @else
                                    @foreach($usuario->direcciones->take(2) as $direccion)
                                        <div class="border-b border-gray-200 dark:border-gray-700 pb-3 last:border-b-0 last:pb-0">
                                            <div class="flex items-center justify-between mb-2">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $direccion->tipo_direccion === 'casa' ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200' : ($direccion->tipo_direccion === 'apartamento' ? 'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200' : 'bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200') }}">
                                                    {{ ucfirst($direccion->tipo_direccion) }}
                                                </span>
                                                @if($direccion->predeterminada)
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                        </svg>
                                                        Principal
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="text-gray-900 dark:text-white font-medium">{{ $direccion->nombre_destinatario }}</p>
                                            <p class="text-gray-900 dark:text-white">{{ $direccion->direccion_completa }}</p>
                                            <p class="text-gray-600 dark:text-gray-400 text-sm">{{ $direccion->ciudad }}, {{ $direccion->provincia }}</p>
                                            <p class="text-gray-500 dark:text-gray-500 text-xs">{{ $direccion->codigo_postal }}</p>
                                        </div>
                                    @endforeach
                                @endif
                                <div class="mt-4">
                                    <a href="{{ route('direcciones.index') }}" class="inline-flex items-center text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors duration-200">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        {{ $usuario->direcciones->isEmpty() ? 'Agregar dirección' : 'Gestionar direcciones' }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('components.change-password-modal', [
    'title' => __('profile.change_password'),
    'currentPasswordLabel' => __('profile.current_password'),
    'newPasswordLabel' => __('profile.new_password'),
    'confirmPasswordLabel' => 'Confirmar Nueva Contraseña',
    'newPasswordPlaceholder' => 'Mínimo 8 caracteres',
    'confirmPasswordPlaceholder' => 'Repite tu nueva contraseña',
    'passwordRequirements' => __('profile.password_requirements'),
    'cancelButtonText' => 'Cancelar',
    'submitButtonText' => 'Cambiar Contraseña'
])

<style>
/* Estilos adicionales para modo oscuro */
.dark .bg-primary {
    background-color: #3b82f6;
}

.dark .bg-primary:hover {
    background-color: #2563eb;
}

.dark .text-primary {
    color: #3b82f6;
}

/* Transiciones suaves para todos los elementos */
.dark * {
    transition: background-color 0.2s ease-in-out, color 0.2s ease-in-out, border-color 0.2s ease-in-out;
}

/* Estilos para el modal en modo oscuro */
.dark .modal-backdrop {
    background-color: rgba(0, 0, 0, 0.7);
}

/* Estilos para los mensajes de error/éxito en modo oscuro */
.dark .bg-green-50 {
    background-color: #064e3b;
}

.dark .bg-red-50 {
    background-color: #450a0a;
}

/* Estilos para los inputs de archivo en modo oscuro */
.dark input[type="file"]::file-selector-button {
    background-color: #1e40af;
    color: #ffffff;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 0.375rem;
    cursor: pointer;
    transition: background-color 0.2s ease-in-out;
}

.dark input[type="file"]::file-selector-button:hover {
    background-color: #1d4ed8;
}
</style>

<script>
function confirmarEliminarFoto() {
    // Verificar que el token CSRF existe
    const token = document.querySelector('meta[name="csrf-token"]');
    if (!token) {
        console.error('No se encontró el token CSRF');
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error de seguridad: Token CSRF no encontrado'
        });
        return;
    }
    console.log('Token CSRF encontrado:', token.getAttribute('content'));

    Swal.fire({
        title: '¿Estás seguro?',
        text: "¿Deseas eliminar tu foto de perfil?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            console.log('Iniciando petición para eliminar foto...');
            
            // Mostrar loading
            Swal.fire({
                title: 'Eliminando...',
                text: 'Por favor espera',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            fetch("{{ route('perfil.eliminarFoto') }}", {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': token.getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => {
                console.log('Respuesta recibida:', response.status);
                return response.json().then(data => ({
                    status: response.status,
                    data: data
                }));
            })
            .then(({status, data}) => {
                console.log('Datos recibidos:', data);
                
                if (status === 200 && data.tipo === 'success') {
                    Swal.fire({
                        title: '¡Eliminada!',
                        text: data.mensaje,
                        icon: 'success'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    throw new Error(data.mensaje || 'Error al eliminar la foto');
                }
            })
            .catch(error => {
                console.error('Error detallado:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'No se pudo eliminar la foto de perfil'
                });
            });
        }
            });
    }

    // Funciones para el modal de cambio de contraseña
    // Todas las funciones están incluidas en el componente reutilizable
</script>

@endsection 