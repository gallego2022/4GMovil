@extends('layouts.landing')

@section('title', 'Checkout Cancelado')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-lg p-8 text-center">
            <!-- Icono de cancelación -->
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-6">
                <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </div>

            <!-- Título -->
            <h1 class="text-3xl font-bold text-gray-900 mb-4">
                Checkout Cancelado
            </h1>

            <!-- Mensaje -->
            <p class="text-lg text-gray-600 mb-8">
                El proceso de checkout ha sido cancelado. Tu pedido no se ha procesado.
            </p>

            <!-- Información del pedido -->
            @if(isset($pedido))
            <div class="bg-gray-50 rounded-lg p-6 mb-8 text-left">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Detalles del Pedido Cancelado</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Número de Pedido:</p>
                        <p class="font-medium text-gray-900">#{{ $pedido->pedido_id }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Fecha:</p>
                        <p class="font-medium text-gray-900">{{ $pedido->fecha_pedido->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total:</p>
                        <p class="font-medium text-gray-900">${{ number_format($pedido->total, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Estado:</p>
                        <p class="font-medium text-red-600">Cancelado</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Acciones -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('landing') }}" 
                   class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    Volver al Inicio
                </a>
                
                <a href="{{ route('checkout.index') }}" 
                   class="inline-flex items-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                    Intentar Nuevamente
                </a>
            </div>

            <!-- Información adicional -->
            <div class="mt-8 text-sm text-gray-500">
                <p>Si tienes alguna pregunta sobre tu pedido cancelado, no dudes en contactarnos.</p>
                <p class="mt-2">
                    <a href="{{ route('contacto.enviar') }}" class="text-blue-600 hover:text-blue-800 underline">
                        Contactar Soporte
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
