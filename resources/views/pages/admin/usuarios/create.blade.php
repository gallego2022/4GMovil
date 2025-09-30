@extends('layouts.app-new')

@section('title', __('admin.actions.create') . ' Usuario - 4GMovil')

@section('content')
<div class="space-y-6">
    <!-- Encabezado -->
    <div>
        <h2 class="text-2xl font-bold leading-7 text-gray-900 dark:text-gray-100 sm:truncate sm:text-3xl sm:tracking-tight">Crear Usuario</h2>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Ingresa los detalles del nuevo usuario</p>
    </div>

    @if ($errors->any())
        <div class="rounded-md bg-red-50 dark:bg-red-900/50 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400 dark:text-red-300" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800 dark:text-red-200">Se encontraron los siguientes errores:</h3>
                    <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                        <ul class="list-disc space-y-1 pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Formulario -->
    <form action="{{ route('usuarios.store') }}" method="POST" class="mt-6">
        @csrf

        <div class="space-y-8">
            <!-- Información Personal -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                   Información Personal
                </h3>
                
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <!-- Nombre del usuario -->
                    <x-validation-field 
                        name="nombre_usuario"
                        label="Nombre Completo"
                        type="text"
                        placeholder="Ej: Juan Pérez"
                        :required="true"
                        :rules="['required', 'minLength:2', 'maxLength:100', 'nameFormat', 'noNumbers']"
                        :messages="[
                            'required' => 'El nombre completo es requerido',
                            'minLength' => 'Mínimo 2 caracteres',
                            'maxLength' => 'Máximo 100 caracteres',
                            'nameFormat' => 'Debe contener al menos nombre y apellido',
                            'noNumbers' => 'No debe contener números'
                        ]"
                        help-text="Nombre completo del usuario (nombre y apellido)"
                    />

                    <!-- Teléfono -->
                    <x-validation-field 
                        name="telefono"
                        label="Teléfono"
                        type="tel"
                        placeholder="Ej: +57 300 123 4567"
                        :required="true"
                        :rules="['required', 'phone', 'phoneLength']"
                        :messages="[
                            'required' => 'El teléfono es requerido',
                            'phone' => 'Ingresa un número de teléfono válido',
                            'phoneLength' => 'El teléfono debe tener entre 7 y 15 dígitos'
                        ]"
                        help-text="Número de teléfono del usuario (con código de país)"
                    />
                </div>
            </div>

            <!-- Información de Acceso -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                   Información de Acceso
                </h3>
                
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <!-- Correo electrónico -->
                    <x-validation-field 
                        name="correo_electronico"
                        label="Correo Electrónico"
                        type="email"
                        placeholder="usuario@ejemplo.com"
                        :required="true"
                        :rules="['required', 'email', 'emailDomain']"
                        :messages="[
                            'required' => 'El correo electrónico es requerido',
                            'email' => 'Ingresa un correo electrónico válido',
                            'emailDomain' => 'El dominio del correo debe ser válido'
                        ]"
                        help-text="Correo electrónico del usuario"
                    />

                    <!-- Contraseña -->
                    <x-validation-field 
                        name="contrasena"
                        label="Contraseña"
                        type="password"
                        placeholder="Mínimo 8 caracteres"
                        :required="true"
                        :rules="['required', 'minLength:8', 'passwordStrength']"
                        :messages="[
                            'required' => 'La contraseña es requerida',
                            'minLength' => 'Mínimo 8 caracteres',
                            'passwordStrength' => 'Debe contener al menos una mayúscula, una minúscula y un número'
                        ]"
                        help-text="Contraseña segura del usuario"
                    />
                </div>

                <!-- Confirmar contraseña -->
                <div class="mt-6">
                    <x-validation-field 
                        name="contrasena_confirmation"
                        label="Confirmar Contraseña"
                        type="password"
                        placeholder="Repite la contraseña"
                        :required="true"
                        :rules="['required', 'matchPassword', 'passwordStrength']"
                        :messages="[
                            'required' => 'La confirmación de contraseña es requerida',
                            'matchPassword' => 'Las contraseñas no coinciden',
                            'passwordStrength' => 'Debe contener al menos una mayúscula, una minúscula y un número'
                        ]"
                        help-text="Repite la contraseña para confirmar"
                    />
                </div>
            </div>

            <!-- Permisos y Estado -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    Permisos y Estado                </h3>
                
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label for="rol" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Rol de Usuario
                        </label>
                        <select name="rol" 
                                id="rol"
                                class="block w-full px-4 py-3 rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm text-gray-900 dark:bg-gray-700 dark:text-gray-100 transition-colors duration-200">
                            <option value="admin">Administrador</option>
                            <option value="cliente" selected>Cliente</option>
                            <option value="invitado">Invitado</option>
                        </select>
                        @error('rol')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="estado" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                           Estado del Usuario
                        </label>
                        <select name="estado" 
                                id="estado"
                                class="block w-full px-4 py-3 rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm text-gray-900 dark:bg-gray-700 dark:text-gray-100 transition-colors duration-200">
                            <option value="1" selected>Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                        @error('estado')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 flex items-center justify-end gap-x-6">
            <a href="{{ route('usuarios.index') }}" 
               class="text-sm font-semibold leading-6 text-gray-900 dark:text-gray-300 hover:text-gray-700 dark:hover:text-gray-100 transition-colors duration-200">
               {{ __('admin.actions.cancel') }}            </a>
            <button type="submit" 
            class="inline-flex justify-center rounded-lg bg-gradient-to-r from-emerald-500 to-teal-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg hover:from-emerald-600 hover:to-teal-700 transform hover:scale-105 transition-all duration-200 ease-in-out focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600 hover:shadow-xl">
            <svg class="w-5 h-5 mr-2 transition-transform duration-200 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
               {{ __('admin.actions.create') }} Usuario
            </button>
        </div>
    </form>
</div>

@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: '¡Éxito!',
        text: "{{ session('success') }}",
        timer: 3000,
        timerProgressBar: true,
        showConfirmButton: false,
        toast: true,
        position: 'top-end',
        background: document.documentElement.classList.contains('dark') ? '#374151' : '#fff',
        color: document.documentElement.classList.contains('dark') ? '#fff' : '#000'
    });
</script>
@endif

@if(session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: '¡Error!',
        text: "{{ session('error') }}",
        timer: 3000,
        timerProgressBar: true,
        showConfirmButton: false,
        toast: true,
        position: 'top-end',
        background: document.documentElement.classList.contains('dark') ? '#374151' : '#fff',
        color: document.documentElement.classList.contains('dark') ? '#fff' : '#000'
    });
</script>
@endif

@endsection 