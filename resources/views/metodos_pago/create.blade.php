@extends('layouts.app-new')

@section('title', 'Crear Método de Pago - 4GMovil')

@section('content')
<div class="space-y-6">
    <!-- Encabezado -->
    <div>
        <h2 class="text-2xl font-bold leading-7 text-gray-900 dark:text-gray-100 sm:truncate sm:text-3xl sm:tracking-tight">Crear Método de Pago</h2>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Ingresa los datos para crear un nuevo método de pago</p>
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
    <form action="{{ route('metodos-pago.store') }}" method="POST" class="mt-6">
        @csrf

        <div class="space-y-8">
            <!-- Información Básica -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                    Información del Método de Pago
                </h3>
                
                <div class="grid grid-cols-1 gap-6">
                    <!-- Nombre del método de pago -->
                    <div>
                        <label for="nombre" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Nombre del Método de Pago <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="nombre" 
                               id="nombre" 
                               value="{{ old('nombre') }}"
                               class="block w-full px-4 py-3 rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm text-gray-900 dark:bg-gray-700 dark:text-gray-100 transition-colors duration-200 @error('nombre') border-red-300 dark:border-red-600 text-red-900 dark:text-red-200 placeholder-red-300 dark:placeholder-red-500 focus:border-red-500 focus:ring-red-500 @enderror"
                               placeholder="Ej: Tarjeta de Crédito, Efectivo, Transferencia Bancaria..."
                               required>
                        @error('nombre')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Descripción -->
                    <div>
                        <label for="descripcion" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Descripción
                        </label>
                        <textarea name="descripcion" 
                                  id="descripcion"
                                  rows="3"
                                  placeholder="Describe brevemente las características de este método de pago..."
                                  class="block w-full px-4 py-3 rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm text-gray-900 dark:bg-gray-700 dark:text-gray-100 transition-colors duration-200 @error('descripcion') border-red-300 dark:border-red-600 text-red-900 dark:text-red-200 placeholder-red-300 dark:placeholder-red-500 focus:border-red-500 focus:ring-red-500 @enderror">{{ old('descripcion') }}</textarea>
                        @error('descripcion')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Estado Activo -->
                    <div class="flex items-center">
                        <input type="hidden" name="estado" value="0">
                        <input type="checkbox" 
                               name="estado"  
                               id="estado"
                               value="1"
                               class="h-4 w-4 text-brand-600 focus:ring-brand-500 border-gray-300 rounded transition-colors duration-200 @error('estado') border-red-300 dark:border-red-600 @enderror"
                               {{ old('estado', true) ? 'checked' : '' }}>
                        <label for="estado" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                            Método de Pago Activo (disponible para transacciones)
                        </label>
                        @error('estado')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 flex items-center justify-end gap-x-6">
            <a href="{{ route('metodos-pago.index') }}" 
               class="text-sm font-semibold leading-6 text-gray-900 dark:text-gray-300 hover:text-gray-700 dark:hover:text-gray-100 transition-colors duration-200">
                Cancelar
            </a>
            <button type="submit" 
            class="inline-flex justify-center rounded-lg bg-gradient-to-r from-emerald-500 to-teal-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg hover:from-emerald-600 hover:to-teal-700 transform hover:scale-105 transition-all duration-200 ease-in-out focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600 hover:shadow-xl">
            <svg class="w-5 h-5 mr-2 transition-transform duration-200 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
                Guardar Método de Pago
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