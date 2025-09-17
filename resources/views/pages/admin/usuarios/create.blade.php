@extends('layouts.app-new')

@section('title', __('admin.actions.create') . ' Usuario - 4GMovil')

@section('content')
<div class="space-y-6">
    <!-- Encabezado -->
    <div>
        <h2 class="text-2xl font-bold leading-7 text-gray-900 dark:text-gray-100 sm:truncate sm:text-3xl sm:tracking-tight">{{ __('admin.actions.create') }} {{ __('admin.products.new_status') }} Usuario</h2>
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
            <!--{{ __('admin.messages.info') }}Personal -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                   {{ __('admin.messages.info') }}Personal
                </h3>
                
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <!--{{ __('admin.fields.name') }}bre del usuario -->
                    <div>
                        <label for="nombre_usuario" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                           {{ __('admin.fields.name') }}bre Completo <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="nombre_usuario" 
                               id="nombre_usuario"
                               class="block w-full px-4 py-3 rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm text-gray-900 dark:bg-gray-700 dark:text-gray-100 transition-colors duration-200 @error('nombre_usuario') border-red-300 dark:border-red-600 text-red-900 dark:text-red-200 placeholder-red-300 dark:placeholder-red-500 focus:border-red-500 focus:ring-red-500 @enderror" 
                               value="{{ old('nombre_usuario') }}" 
                               placeholder="Ej: Juan Pérez"
                               required>
                        @error('nombre_usuario')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!--{{ __('admin.fields.phone') }}-->
                    <div>
                        <label for="telefono" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                           {{ __('admin.fields.phone') }}<span class="text-red-500">*</span>
                        </label>
                        <input type="tel" 
                               name="telefono" 
                               id="telefono"
                               class="block w-full px-4 py-3 rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm text-gray-900 dark:bg-gray-700 dark:text-gray-100 transition-colors duration-200 @error('telefono') border-red-300 dark:border-red-600 text-red-900 dark:text-red-200 placeholder-red-300 dark:placeholder-red-500 focus:border-red-500 focus:ring-red-500 @enderror" 
                               value="{{ old('telefono') }}" 
                               placeholder="Ej: +57 300 123 4567"
                               required>
                        @error('telefono')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!--{{ __('admin.messages.info') }}de Acceso -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                   {{ __('admin.messages.info') }}de Acceso
                </h3>
                
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <!-- Correo electrónico -->
                    <div>
                        <label for="correo_electronico" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Correo Electrónico <span class="text-red-500">*</span>
                        </label>
                        <input type="email" 
                               name="correo_electronico" 
                               id="correo_electronico"
                               class="block w-full px-4 py-3 rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm text-gray-900 dark:bg-gray-700 dark:text-gray-100 transition-colors duration-200 @error('correo_electronico') border-red-300 dark:border-red-600 text-red-900 dark:text-red-200 placeholder-red-300 dark:placeholder-red-500 focus:border-red-500 focus:ring-red-500 @enderror" 
                               value="{{ old('correo_electronico') }}" 
                               placeholder="usuario@ejemplo.com"
                               required>
                        @error('correo_electronico')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Contraseña -->
                    <div>
                        <label for="contrasena" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Contraseña <span class="text-red-500">*</span>
                        </label>
                        <input type="password" 
                               name="contrasena" 
                               id="contrasena"
                               class="block w-full px-4 py-3 rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm text-gray-900 dark:bg-gray-700 dark:text-gray-100 transition-colors duration-200 @error('contrasena') border-red-300 dark:border-red-600 text-red-900 dark:text-red-200 placeholder-red-300 dark:placeholder-red-500 focus:border-red-500 focus:ring-red-500 @enderror" 
                               placeholder="Mínimo 8 caracteres"
                               required>
                        @error('contrasena')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Confirmar contraseña -->
                <div class="mt-6">
                    <label for="contrasena_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Confirmar Contraseña <span class="text-red-500">*</span>
                    </label>
                    <input type="password" 
                           name="contrasena_confirmation" 
                           id="password_confirmation"
                           class="block w-full px-4 py-3 rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm text-gray-900 dark:bg-gray-700 dark:text-gray-100 transition-colors duration-200" 
                           placeholder="Repite la contraseña"
                           required>
                    @error('contrasena_confirmation')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Permisos y{{ __('admin.fields.status') }}-->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    Permisos y{{ __('admin.fields.status') }}                </h3>
                
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
                           {{ __('admin.fields.status') }}del Usuario
                        </label>
                        <select name="estado" 
                                id="estado"
                                class="block w-full px-4 py-3 rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm text-gray-900 dark:bg-gray-700 dark:text-gray-100 transition-colors duration-200">
                            <option value="1" selected{{ >{{ __('admin.status.active') }}< }}/option>
                            <option value="0"{{ >{{ __('admin.status.inactive') }}< }}/option>
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
               {{ __('admin.actions.create') }}Usuario
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