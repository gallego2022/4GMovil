@extends('layouts.landing')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Encabezado -->
        <div class="mb-6">
            <div class="flex justify-between items-center mb-4">
                <h1 class="text-2xl font-bold text-gray-800">Mis Direcciones</h1>
                <a href="{{ route('direcciones.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Agregar Nueva Dirección
                </a>
            </div>
            <p class="text-gray-600">Gestiona tus direcciones de envío para recibir tus pedidos de manera segura y puntual.</p>
        </div>

        @if(session('mensaje'))
            <div class="mb-4 {{ session('tipo') === 'error' ? 'bg-red-50 border-red-500' : 'bg-green-50 border-green-500' }} border-l-4 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        @if(session('tipo') === 'error')
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        @else
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        @endif
                    </div>
                    <div class="ml-3">
                        <p class="text-sm {{ session('tipo') === 'error' ? 'text-red-700' : 'text-green-700' }}">{{ session('mensaje') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if($direcciones->isEmpty())
            <div class="bg-white rounded-lg shadow-md p-6 text-center">
                <div class="flex flex-col items-center justify-center py-12">
                    <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No tienes direcciones guardadas</h3>
                    <p class="text-gray-600 mb-6">Agrega una dirección para recibir tus pedidos</p>
                    <a href="{{ route('direcciones.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Agregar mi primera dirección
                    </a>
                </div>
            </div>
        @else
            <div class="mb-4">
                <p class="text-sm text-gray-600">Tienes <span class="font-medium text-gray-900">{{ $direcciones->count() }}</span> dirección{{ $direcciones->count() !== 1 ? 'es' : '' }} guardada{{ $direcciones->count() !== 1 ? 's' : '' }}</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($direcciones as $direccion)
                    <div class="bg-white rounded-lg shadow-md p-6 relative">
                        <!-- Encabezado de la dirección -->
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center space-x-2">
                                @if($direccion->predeterminada)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                        Principal
                                    </span>
                                @endif
                                @if($direccion->activo)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Activa
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Inactiva
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Información del destinatario -->
                        <div class="mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">{{ $direccion->nombre_destinatario }}</h3>
                            <p class="text-gray-600">{{ $direccion->telefono }}</p>
                        </div>

                        <!-- Información de la dirección -->
                        <div class="space-y-2">
                            <p class="text-gray-900 font-medium">
                                {{ $direccion->calle }} {{ $direccion->numero }}
                                @if($direccion->piso)
                                    , Piso {{ $direccion->piso }}
                                @endif
                                @if($direccion->departamento)
                                    , Depto {{ $direccion->departamento }}
                                @endif
                            </p>
                            <p class="text-gray-600">{{ $direccion->ciudad }}, {{ $direccion->provincia }}</p>
                            <p class="text-gray-600">{{ $direccion->pais }}</p>
                            <p class="text-gray-600">Código Postal: {{ $direccion->codigo_postal }}</p>
                            @if($direccion->referencias)
                                <div class="mt-3 p-3 bg-gray-50 rounded-md">
                                    <p class="text-sm text-gray-700">
                                        <span class="font-medium">Referencias:</span><br>
                                        {{ $direccion->referencias }}
                                    </p>
                                </div>
                            @endif
                        </div>

                        <!-- Acciones -->
                        <div class="mt-4 flex justify-end space-x-2">
                            <a href="{{ route('direcciones.edit', $direccion->direccion_id) }}" class="inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Editar
                            </a>
                            <form action="{{ route('direcciones.destroy', $direccion->direccion_id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('¿Estás seguro de que deseas eliminar la dirección de {{ $direccion->nombre_destinatario }}? Esta acción no se puede deshacer.')" class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection 