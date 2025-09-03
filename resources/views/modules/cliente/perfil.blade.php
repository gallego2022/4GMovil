@extends('layouts.landing')

@section('title', 'Mi Perfil - 4GMovil')
@section('meta-description', 'Gestiona tu perfil de usuario en 4GMovil')

@section('content')
<div class="bg-gray-100 min-h-screen py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Mensaje de éxito/error -->
        @if(session('mensaje'))
            <div class="mb-4 rounded-md p-4 {{ session('tipo', 'success') === 'success' ? 'bg-green-50 text-green-800' : 'bg-red-50 text-red-800' }}">
                <div class="flex">
                    <div class="flex-shrink-0">
                        @if(session('tipo', 'success') === 'success')
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        @else
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
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

        <div class="bg-white shadow-xl rounded-lg overflow-hidden">
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
                                    class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        @else
                            <div class="w-24 h-24 rounded-full bg-gray-200 flex items-center justify-center border-4 border-white">
                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-xl font-semibold mb-4">Actualizar Información</h2>
                        
                        @if($errors->any())
                            <div class="mb-4 bg-red-50 text-red-800 p-4 rounded-md">
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
                                    <label for="nombre_usuario" class="block text-sm font-medium text-gray-700">
                                        {{ __('profile.nombre_usuario') }}
                                    </label>
                                    <input type="text" 
                                           id="nombre_usuario" 
                                           name="nombre_usuario" 
                                           value="{{ old('nombre_usuario', $usuario->nombre_usuario) }}"
                                           class="mt-1 block w-full px-4 py-3 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-gray-900">
                                </div>

                                <div>
                                    <label for="correo_electronico" class="block text-sm font-medium text-gray-700">
                                        {{ __('profile.correo_electronico') }}
                                    </label>
                                    <input type="email" 
                                           id="correo_electronico" 
                                           name="correo_electronico" 
                                           value="{{ old('correo_electronico', $usuario->correo_electronico) }}"
                                           class="mt-1 block w-full px-4 py-3 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-gray-900">
                                </div>

                                <div>
                                    <label for="telefono" class="block text-sm font-medium text-gray-700">
                                        {{ __('profile.telefono') }}
                                    </label>
                                    <input type="tel" 
                                           id="telefono" 
                                           name="telefono" 
                                           value="{{ old('telefono', $usuario->telefono) }}"
                                           class="mt-1 block w-full px-4 py-3 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-gray-900">
                                </div>

                                <div>
                                    <label for="foto_perfil" class="block text-sm font-medium text-gray-700">
                                        {{ __('profile.foto_perfil') }}
                                    </label>
                                    <input type="file" 
                                           id="foto_perfil" 
                                           name="foto_perfil" 
                                           accept="image/*"
                                           class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                    <p class="mt-2 text-sm text-gray-500">
                                        {{ __('profile.photo_requirements') }}
                                    </p>
                                </div>

                                <div class="flex justify-end">
                                    <button type="submit" 
                                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
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
                        <div class="bg-white rounded-lg shadow p-6">
                            <h2 class="text-xl font-semibold mb-4">Seguridad</h2>
                            <button type="button" 
                                   onclick="showChangePasswordModal()"
                                   class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                </svg>
                                {{ __('profile.change_password') }}
                            </button>
                        </div>

                        <!-- Historial de Pedidos -->
                        <div class="bg-white rounded-lg shadow p-6">
                            <h2 class="text-xl font-semibold mb-4">Mis Pedidos</h2>
                            <a href="{{ route('pedidos.historial') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                </svg>
                                {{ __('profile.view_order_history') }}
                            </a>
                        </div>

                        <!-- Direcciones -->
                        <div class="bg-white rounded-lg shadow p-6">
                            <h2 class="text-xl font-semibold mb-4">{{ __('profile.my_addresses') }}</h2>
                            <div class="space-y-4">
                                @if($usuario->direcciones->isEmpty())
                                    <p class="text-gray-500">{{ __('profile.no_addresses_saved') }}</p>
                                @else
                                    @foreach($usuario->direcciones->take(2) as $direccion)
                                        <div class="border-b border-gray-200 pb-3 last:border-b-0 last:pb-0">
                                            <div class="flex items-center justify-between mb-2">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $direccion->tipo_direccion === 'casa' ? 'bg-green-100 text-green-800' : ($direccion->tipo_direccion === 'apartamento' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800') }}">
                                                    {{ ucfirst($direccion->tipo_direccion) }}
                                                </span>
                                                @if($direccion->predeterminada)
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                        </svg>
                                                        Principal
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="text-gray-900 font-medium">{{ $direccion->nombre_destinatario }}</p>
                                            <p class="text-gray-900">{{ $direccion->direccion_completa }}</p>
                                            <p class="text-gray-600 text-sm">{{ $direccion->ciudad }}, {{ $direccion->provincia }}</p>
                                            <p class="text-gray-500 text-xs">{{ $direccion->codigo_postal }}</p>
                                        </div>
                                    @endforeach
                                @endif
                                <div class="mt-4">
                                    <a href="{{ route('direcciones.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800">
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

<!-- Modal para cambiar contraseña -->
<div id="changePasswordModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <!-- Encabezado -->
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('profile.change_password') }}</h3>
                <button type="button" onclick="closeChangePasswordModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <!-- Formulario -->
            <form id="changePasswordForm" class="space-y-4">
                @csrf
                
                <!-- Contraseña Actual -->
                <div>
                    <label for="contrasena_actual" class="block text-sm font-medium text-gray-700 dark:text-gray-300 text-left">
                        {{ __('profile.current_password') }}
                    </label>
                    <input type="password" 
                           id="contrasena_actual" 
                           name="contrasena_actual" 
                           required 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    <div id="error_contrasena_actual" class="mt-1 text-xs text-red-600 dark:text-red-400 hidden"></div>
                </div>
                
                <!-- Nueva Contraseña -->
                <div>
                    <label for="nueva_contrasena" class="block text-sm font-medium text-gray-700 dark:text-gray-300 text-left">
                        {{ __('profile.new_password') }}
                    </label>
                    <input type="password" 
                           id="nueva_contrasena" 
                           name="nueva_contrasena" 
                           required 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                           placeholder="Mínimo 8 caracteres">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 text-left">
                        {{ __('profile.password_requirements') }}
                    </p>
                    <div id="error_nueva_contrasena" class="mt-1 text-xs text-red-600 dark:text-red-400 hidden"></div>
                </div>
                
                <!-- Confirmar Nueva Contraseña -->
                <div>
                    <label for="nueva_contrasena_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 text-left">
                        Confirmar Nueva Contraseña
                    </label>
                    <input type="password" 
                           id="nueva_contrasena_confirmation" 
                           name="nueva_contrasena_confirmation" 
                           required 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                           placeholder="Repite tu nueva contraseña">
                    <div id="error_nueva_contrasena_confirmation" class="mt-1 text-xs text-red-600 dark:text-red-400 hidden"></div>
                </div>
                
                <!-- Botones -->
                <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" 
                            onclick="closeChangePasswordModal()"
                            class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-md hover:bg-gray-400 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        Cancelar
                    </button>
                    <button type="submit" 
                            id="submitChangePassword"
                            class="px-4 py-2 bg-blue-600 dark:bg-blue-500 text-white text-sm font-medium rounded-md hover:bg-blue-700 dark:hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cambiar Contraseña
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

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
    function showChangePasswordModal() {
        document.getElementById('changePasswordModal').classList.remove('hidden');
        // Limpiar formulario y errores
        document.getElementById('changePasswordForm').reset();
        clearAllErrors();
    }

    function closeChangePasswordModal() {
        document.getElementById('changePasswordModal').classList.add('hidden');
        clearAllErrors();
    }

    function clearAllErrors() {
        const errorDivs = document.querySelectorAll('[id^="error_"]');
        errorDivs.forEach(div => {
            div.classList.add('hidden');
            div.textContent = '';
        });
    }

    function showError(fieldId, message) {
        const errorDiv = document.getElementById(`error_${fieldId}`);
        if (errorDiv) {
            errorDiv.textContent = message;
            errorDiv.classList.remove('hidden');
        }
    }

    function clearError(fieldId) {
        const errorDiv = document.getElementById(`error_${fieldId}`);
        if (errorDiv) {
            errorDiv.classList.add('hidden');
            errorDiv.textContent = '';
        }
    }

    // Validación en tiempo real
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('changePasswordForm');
        const submitButton = document.getElementById('submitChangePassword');
        
        // Validar nueva contraseña
        document.getElementById('nueva_contrasena').addEventListener('input', function() {
            const value = this.value;
            clearError('nueva_contrasena');
            
            if (value.length === 0) {
                showError('nueva_contrasena', 'La nueva contraseña es requerida');
                return;
            }
            
            if (value.length < 8) {
                showError('nueva_contrasena', 'La contraseña debe tener al menos 8 caracteres');
                return;
            }
            
            if (!/[A-Z]/.test(value)) {
                showError('nueva_contrasena', 'La contraseña debe contener al menos una mayúscula');
                return;
            }
            
            if (!/[a-z]/.test(value)) {
                showError('nueva_contrasena', 'La contraseña debe contener al menos una minúscula');
                return;
            }
            
            if (!/[0-9]/.test(value)) {
                showError('nueva_contrasena', 'La contraseña debe contener al menos un número');
                return;
            }
            
            if (!/[!@#$%^&*]/.test(value)) {
                showError('nueva_contrasena', 'La contraseña debe contener al menos un carácter especial (!@#$%^&*)');
                return;
            }
        });
        
        // Validar confirmación
        document.getElementById('nueva_contrasena_confirmation').addEventListener('input', function() {
            const value = this.value;
            const newPassword = document.getElementById('nueva_contrasena').value;
            clearError('nueva_contrasena_confirmation');
            
            if (value.length === 0) {
                showError('nueva_contrasena_confirmation', 'La confirmación de contraseña es requerida');
                return;
            }
            
            if (value !== newPassword) {
                showError('nueva_contrasena_confirmation', 'Las contraseñas no coinciden');
                return;
            }
        });
        
        // Validar contraseña actual
        document.getElementById('contrasena_actual').addEventListener('input', function() {
            clearError('contrasena_actual');
        });
        
        // Manejar envío del formulario
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Validar todos los campos
            const contrasenaActual = document.getElementById('contrasena_actual').value;
            const nuevaContrasena = document.getElementById('nueva_contrasena').value;
            const confirmacion = document.getElementById('nueva_contrasena_confirmation').value;
            
            // Limpiar errores previos
            clearAllErrors();
            
            // Validaciones básicas
            let hasErrors = false;
            
            if (!contrasenaActual) {
                showError('contrasena_actual', 'La contraseña actual es requerida');
                hasErrors = true;
            }
            
            if (!nuevaContrasena) {
                showError('nueva_contrasena', 'La nueva contraseña es requerida');
                hasErrors = true;
            } else if (nuevaContrasena.length < 8) {
                showError('nueva_contrasena', 'La contraseña debe tener al menos 8 caracteres');
                hasErrors = true;
            } else if (!/[A-Z]/.test(nuevaContrasena)) {
                showError('nueva_contrasena', 'La contraseña debe contener al menos una mayúscula');
                hasErrors = true;
            } else if (!/[a-z]/.test(nuevaContrasena)) {
                showError('nueva_contrasena', 'La contraseña debe contener al menos una minúscula');
                hasErrors = true;
            } else if (!/[0-9]/.test(nuevaContrasena)) {
                showError('nueva_contrasena', 'La contraseña debe contener al menos un número');
                hasErrors = true;
            } else if (!/[!@#$%^&*]/.test(nuevaContrasena)) {
                showError('nueva_contrasena', 'La contraseña debe contener al menos un carácter especial (!@#$%^&*)');
                hasErrors = true;
            }
            
            if (!confirmacion) {
                showError('nueva_contrasena_confirmation', 'La confirmación de contraseña es requerida');
                hasErrors = true;
            } else if (nuevaContrasena !== confirmacion) {
                showError('nueva_contrasena_confirmation', 'Las contraseñas no coinciden');
                hasErrors = true;
            }
            
            if (hasErrors) {
                return;
            }
            
            // Deshabilitar botón y mostrar loading
            submitButton.disabled = true;
            submitButton.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Procesando...';
            
            try {
                const formData = new FormData(form);
                
                const response = await fetch('{{ route("cambiar.contrasena.post") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (response.ok && data.success) {
                    // Éxito
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: data.message || 'Contraseña cambiada exitosamente',
                        confirmButtonText: 'Continuar'
                    }).then(() => {
                        closeChangePasswordModal();
                        // Recargar la página para mostrar el mensaje de éxito
                        window.location.reload();
                    });
                } else {
                    // Error del servidor
                    if (data.errors) {
                        // Errores de validación
                        Object.keys(data.errors).forEach(field => {
                            const fieldId = field.replace(/\./g, '_');
                            showError(fieldId, data.errors[field][0]);
                        });
                    } else {
                        // Error general
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || 'Error al cambiar la contraseña',
                            confirmButtonText: 'Entendido'
                        });
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error de conexión. Inténtalo de nuevo.',
                    confirmButtonText: 'Entendido'
                });
            } finally {
                // Restaurar botón
                submitButton.disabled = false;
                submitButton.textContent = 'Cambiar Contraseña';
            }
        });
    });
</script>

@endsection 