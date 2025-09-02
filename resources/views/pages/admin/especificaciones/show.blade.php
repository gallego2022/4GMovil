@extends('layouts.app-new')

@section('title', 'Detalles de Especificación - 4GMovil')

@push('css')
<style>
    .info-section {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    
    .dark .info-section {
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        border-color: #475569;
    }
    
    .field-preview {
        background: #f1f5f9;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 1.5rem;
        margin-top: 1rem;
    }
    
    .dark .field-preview {
        background: #334155;
        border-color: #475569;
    }
    
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    
    .status-active {
        background-color: #dcfce7;
        color: #166534;
    }
    
    .dark .status-active {
        background-color: #14532d;
        color: #bbf7d0;
    }
    
    .status-inactive {
        background-color: #fef2f2;
        color: #991b1b;
    }
    
    .dark .status-inactive {
        background-color: #7f1d1d;
        color: #fecaca;
    }
    
    .required-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    
    .required-yes {
        background-color: #dcfce7;
        color: #166534;
    }
    
    .dark .required-yes {
        background-color: #14532d;
        color: #bbf7d0;
    }
    
    .required-no {
        background-color: #f3f4f6;
        color: #374151;
    }
    
    .dark .required-no {
        background-color: #374151;
        color: #d1d5db;
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Encabezado -->
    <div class="bg-white dark:bg-gray-900 p-6 rounded-xl shadow-lg border border-gray-100 dark:border-gray-800">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Detalles de Especificación
                </h1>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Información completa de la especificación técnica
                </p>
            </div>
            
            <!-- Botones de Acción -->
            <div class="flex items-center space-x-3">
                <!-- Botón Volver -->
                <a href="{{ route('admin.especificaciones.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Volver al Listado
                </a>
                
                <!-- Botón Editar -->
                <a href="{{ route('admin.especificaciones.edit', $especificacion->especificacion_id) }}" 
                   class="inline-flex items-center px-4 py-2 border border-brand-300 dark:border-brand-600 rounded-md shadow-sm text-sm font-medium text-brand-700 dark:text-brand-300 bg-brand-50 dark:bg-brand-900/20 hover:bg-brand-100 dark:hover:bg-brand-900/30 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 13l6-6m-3-3L6 18H3v-3L15.232 5.232z" />
                    </svg>
                    Editar
                </a>
            </div>
        </div>
    </div>

    <!-- Información Principal -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Columna Principal -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Información Básica -->
            <div class="info-section">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-brand-600 dark:text-brand-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Información Básica
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Etiqueta
                        </label>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ $especificacion->etiqueta }}
                        </p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Nombre del Campo
                        </label>
                        <p class="text-lg font-mono text-gray-900 dark:text-white bg-gray-100 dark:bg-gray-800 px-3 py-2 rounded">
                            {{ $especificacion->nombre_campo }}
                        </p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Categoría
                        </label>
                        <p class="text-lg text-gray-900 dark:text-white">
                            {{ $especificacion->categoria->nombre ?? 'Sin categoría' }}
                        </p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Tipo de Campo
                        </label>
                        <p class="text-lg text-gray-900 dark:text-white">
                            {{ ucfirst($especificacion->tipo_campo) }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Configuración del Campo -->
            <div class="info-section">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-brand-600 dark:text-brand-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Configuración del Campo
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Unidad de Medida
                        </label>
                        <p class="text-lg text-gray-900 dark:text-white">
                            {{ $especificacion->unidad ?: 'No especificada' }}
                        </p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Orden de Visualización
                        </label>
                        <p class="text-lg text-gray-900 dark:text-white">
                            {{ $especificacion->orden }}
                        </p>
                    </div>
                </div>

                @if($especificacion->opciones)
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Opciones Disponibles
                    </label>
                    <div class="flex flex-wrap gap-2">
                        @foreach(explode(',', $especificacion->opciones) as $opcion)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-brand-100 text-brand-800 dark:bg-brand-900 dark:text-brand-200">
                                {{ trim($opcion) }}
                            </span>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($especificacion->descripcion)
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Descripción
                    </label>
                    <p class="text-gray-900 dark:text-white">
                        {{ $especificacion->descripcion }}
                    </p>
                </div>
                @endif
            </div>

            <!-- Vista Previa del Campo -->
            <div class="info-section">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-brand-600 dark:text-brand-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    Vista Previa del Campo
                </h3>
                
                <div class="field-preview">
                    @include('components.especificacion-field-preview', ['especificacion' => $especificacion])
                </div>
            </div>
        </div>

        <!-- Barra Lateral -->
        <div class="space-y-6">
            <!-- Estado y Configuración -->
            <div class="info-section">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-brand-600 dark:text-brand-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Estado y Configuración
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Estado
                        </label>
                        <span class="status-badge {{ $especificacion->activo ? 'status-active' : 'status-inactive' }}">
                            {{ $especificacion->activo ? 'Activa' : 'Inactiva' }}
                        </span>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Requerido
                        </label>
                        <span class="required-badge {{ $especificacion->requerido ? 'required-yes' : 'required-no' }}">
                            {{ $especificacion->requerido ? 'Sí' : 'No' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Información del Sistema -->
            <div class="info-section">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-gray-600 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    Información del Sistema
                </h3>
                
                <div class="space-y-3 text-sm">
                    <div>
                        <span class="font-medium text-gray-700 dark:text-gray-300">ID:</span>
                        <span class="ml-2 text-gray-900 dark:text-gray-100 font-mono">{{ $especificacion->especificacion_id }}</span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700 dark:text-gray-300">Creado:</span>
                        <span class="ml-2 text-gray-900 dark:text-gray-100">{{ $especificacion->created_at ? $especificacion->created_at->format('d/m/Y H:i') : 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700 dark:text-gray-300">Actualizado:</span>
                        <span class="ml-2 text-gray-900 dark:text-gray-100">{{ $especificacion->updated_at ? $especificacion->updated_at->format('d/m/Y H:i') : 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <!-- Acciones Rápidas -->
            <div class="info-section">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-brand-600 dark:text-brand-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    Acciones Rápidas
                </h3>
                
                <div class="space-y-3">
                    <a href="{{ route('admin.especificaciones.edit', $especificacion->especificacion_id) }}" 
                       class="w-full inline-flex items-center justify-center px-4 py-2 border border-brand-300 dark:border-brand-600 rounded-md shadow-sm text-sm font-medium text-brand-700 dark:text-brand-300 bg-brand-50 dark:bg-brand-900/20 hover:bg-brand-100 dark:hover:bg-brand-900/30 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500">
                        <svg class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 13l6-6m-3-3L6 18H3v-3L15.232 5.232z" />
                        </svg>
                        Editar Especificación
                    </a>
                    
                    <button onclick="toggleEstado()" 
                            class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500">
                        <svg class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        {{ $especificacion->activo ? 'Desactivar' : 'Activar' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function toggleEstado() {
    const especificacionId = {{ $especificacion->especificacion_id }};
    const currentEstado = {{ $especificacion->activo ? 'true' : 'false' }};
    const newEstado = !currentEstado;
    
    fetch(`/admin/especificaciones/${especificacionId}/toggle-estado`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Estado Actualizado',
                text: data.message,
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'Error al cambiar el estado'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error al cambiar el estado'
        });
    });
}
</script>
@endpush

@endsection
