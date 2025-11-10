@extends('layouts.app-new')

@section('title', 'Detalle de Reseña - 4GMovil')

@section('content')
<!-- Notificaciones -->
<x-notifications />

<div class="space-y-6">
    <!-- Encabezado -->
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold leading-7 text-content sm:truncate sm:text-3xl sm:tracking-tight">
                Detalle de Reseña
            </h2>
            <p class="mt-1 text-sm text-content-secondary">
                Información completa de la reseña
            </p>
        </div>
        <div class="mt-4 flex sm:ml-4 sm:mt-0">
            <a href="{{ route('admin.resenas.index') }}" 
               class="inline-flex items-center rounded-md bg-white dark:bg-gray-800 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-100 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">
                <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M17 10a.75.75 0 01-.75.75H5.612l7.158 7.158a.75.75 0 11-1.06 1.06l-8.5-8.5a.75.75 0 010-1.06l8.5-8.5a.75.75 0 111.06 1.06L5.612 9.25H16.25A.75.75 0 0117 10z" clip-rule="evenodd" />
                </svg>
                Volver a Reseñas
            </a>
        </div>
    </div>

    <!-- Información de la Reseña -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
        <div class="px-6 py-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Información del Producto -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                        Producto
                    </h3>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">
                                Nombre
                            </label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                {{ $resena->producto->nombre_producto }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">
                                ID del Producto
                            </label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                {{ $resena->producto_id }}
                            </p>
                        </div>
                        <div>
                            <a href="{{ route('productos.show', $resena->producto_id) }}" 
                               class="inline-flex items-center text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                Ver Producto
                                <svg class="ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Información del Cliente -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                        Cliente
                    </h3>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">
                                Nombre
                            </label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                {{ $resena->usuario->nombre_usuario }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">
                                Email
                            </label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                {{ $resena->usuario->correo_electronico }}
                            </p>
                        </div>
                        @if($resena->pedido_id)
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">
                                    Pedido Relacionado
                                </label>
                                <a href="{{ route('admin.pedidos.show', $resena->pedido_id) }}" 
                                   class="mt-1 inline-flex items-center text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                    Pedido #{{ $resena->pedido_id }}
                                    <svg class="ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Calificación -->
            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                    Calificación
                </h3>
                <div class="flex items-center space-x-2">
                    @for($i = 1; $i <= 5; $i++)
                        <svg class="w-8 h-8 {{ $i <= $resena->calificacion ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                    @endfor
                    <span class="ml-4 text-2xl font-semibold text-gray-900 dark:text-white">
                        {{ $resena->calificacion }}/5
                    </span>
                </div>
            </div>

            <!-- Comentario -->
            @if($resena->comentario)
                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                        Comentario
                    </h3>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <p class="text-sm text-gray-900 dark:text-white whitespace-pre-wrap">
                            {{ $resena->comentario }}
                        </p>
                    </div>
                </div>
            @endif

            <!-- Estado y Fechas -->
            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                    Estado y Fechas
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">
                            Estado
                        </label>
                        <div class="mt-2 flex space-x-2">
                            @if($resena->activa)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                    Activa
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200">
                                    Inactiva
                                </span>
                            @endif
                            @if($resena->verificada)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    Verificada
                                </span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">
                            Fecha de Creación
                        </label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">
                            {{ $resena->created_at->format('d/m/Y H:i:s') }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">
                            Última Actualización
                        </label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">
                            {{ $resena->updated_at->format('d/m/Y H:i:s') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Acciones -->
            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700 flex justify-end space-x-3">
                @if($resena->activa)
                    <form action="{{ route('admin.resenas.toggle-activa', $resena->resena_id) }}" method="POST" class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                            Desactivar
                        </button>
                    </form>
                @else
                    <form action="{{ route('admin.resenas.toggle-activa', $resena->resena_id) }}" method="POST" class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            Activar
                        </button>
                    </form>
                @endif
                @if(!$resena->verificada)
                    <form action="{{ route('admin.resenas.verificar', $resena->resena_id) }}" method="POST" class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Verificar
                        </button>
                    </form>
                @endif
                <form action="{{ route('admin.resenas.destroy', $resena->resena_id) }}" 
                      method="POST" 
                      class="inline confirm-action"
                      data-title="¿Eliminar reseña?"
                      data-message="¿Estás seguro de eliminar esta reseña?"
                      data-confirm-text="Sí, eliminar"
                      data-method="DELETE">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

