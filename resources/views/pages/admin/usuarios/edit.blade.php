@extends('layouts.app-new')

@section('title', __('admin.actions.edit') . ' Usuario - 4GMovil')

@section('content')
<div class="space-y-6">
    <!-- Encabezado -->
    <div>
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">{{ __('admin.actions.edit') . ' Usuario' }}</h2>
        <p class="mt-1 text-sm text-gray-500">Modifica los detalles del usuario</p>
    </div>

    <!-- Formulario -->
    <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl md:col-span-2">
        <form action="{{ route('usuarios.update', $data->usuario_id ?? 0) }}" method="POST" class="px-4 py-6 sm:p-8">
            @csrf
            @method('PUT')

            @if ($errors->any())
                <div class="rounded-md bg-red-50 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Se encontraron los siguientes errores:</h3>
                            <div class="mt-2 text-sm text-red-700">
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

            <div class="space-y-6">
                <!-- Datos del usuario -->
                <div>
                    <label for="nombre_usuario" class="block text-sm font-medium text-gray-700">{{ __('admin.fields.name') }}</label>
                    <div class="mt-1">
                        <input type="text" 
                               name="nombre_usuario" 
                               id="nombre_usuario"
                               class="block w-full px-4 py-3 rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm text-gray-900 dark:bg-gray-700 dark:text-gray-100 transition-colors duration-200 @error('nombre_usuario') border-red-300 dark:border-red-600 text-red-900 dark:text-red-200 placeholder-red-300 dark:placeholder-red-500 focus:border-red-500 focus:ring-red-500 @enderror" 
                               value="{{ old('nombre_usuario', $data->nombre_usuario ?? '') }}" 
                               required>
                    </div>
                    @error('nombre_usuario')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="correo_electronico" class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                    <div class="mt-1">
                        <input type="email" 
                               name="correo_electronico" 
                               id="correo_electronico"
                               class="block w-full px-4 py-3 rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm text-gray-900 dark:bg-gray-700 dark:text-gray-100 transition-colors duration-200 @error('correo_electronico') border-red-300 dark:border-red-600 text-red-900 dark:text-red-200 placeholder-red-300 dark:placeholder-red-500 focus:border-red-500 focus:ring-red-500 @enderror" 
                               value="{{ old('correo_electronico', $data->correo_electronico ?? '') }}" 
                               required>
                    </div>
                    @error('correo_electronico')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="telefono" class="block text-sm font-medium text-gray-700">{{ __('admin.fields.phone') }}</label>
                    <div class="mt-1">
                        <input type="tel" 
                               name="telefono" 
                               id="telefono"
                               class="block w-full px-4 py-3 rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm text-gray-900 dark:bg-gray-700 dark:text-gray-100 transition-colors duration-200 @error('telefono') border-red-300 dark:border-red-600 text-red-900 dark:text-red-200 placeholder-red-300 dark:placeholder-red-500 focus:border-red-500 focus:ring-red-500 @enderror" 
                               value="{{ old('telefono', $data->telefono ?? '') }}">
                    </div>
                    @error('telefono')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Permisos y Estado -->
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-base font-semibold leading-7 text-gray-900">Permisos y Estado</h3>
                    
                    <div class="mt-4 grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label for="rol" class="block text-sm font-medium text-gray-700">Rol</label>
                            <select name="rol" 
                                    id="rol"
                                    class="mt-1 block w-full px-4 py-3 rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm text-gray-900 dark:bg-gray-700 dark:text-gray-100 transition-colors duration-200">
                                <option value="admin" {{ ($data->rol ?? '') == 'admin' ? 'selected' : '' }}>Administrador</option>
                                <option value="cliente" {{ ($data->rol ?? '') == 'cliente' ? 'selected' : '' }}>Cliente</option>
                                <option value="invitado" {{ ($data->rol ?? '') == 'invitado' ? 'selected' : '' }}>Invitado</option>
                            </select>
                        </div>

                        <div>
                            <label for="estado" class="block text-sm font-medium text-gray-700">{{ __('admin.fields.status') }}</label>
                            <select name="estado" 
                                    id="estado"
                                        class="mt-1 block w-full px-4 py-3 rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm text-gray-900 dark:bg-gray-700 dark:text-gray-100 transition-colors duration-200">
                                <option value="1" {{ ($data->estado ?? false) ? 'selected' : '' }}>Activo</option>
                                <option value="0" {{ !($data->estado ?? false) ? 'selected' : '' }}>Inactivo</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-x-6">
                <a href="{{ route('usuarios.index') }}" 
                   class="text-sm font-semibold leading-6 text-gray-900">
                   {{ __('admin.actions.cancel') }}                </a>
                <button type="submit" 
                class="inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-blue-500 to-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg hover:from-blue-600 hover:to-indigo-700 transform hover:scale-105 transition-all duration-200 ease-in-out focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 hover:shadow-xl">
                <svg class="w-5 h-5 mr-2 transition-transform duration-200 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                    Actualizar Usuario
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
