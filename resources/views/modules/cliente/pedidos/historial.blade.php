@extends('layouts.landing')

@section('title', 'Historial de Pedidos - 4GMovil')
@section('meta-description', 'Consulta tu historial de pedidos en 4GMovil')

@section('content')
<div class="bg-gray-100 dark:bg-gray-800 min-h-screen py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Encabezado -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Historial de Pedidos</h1>
            <p class="mt-2 text-gray-600 dark:text-gray-300">Consulta todos tus pedidos realizados</p>
        </div>

        <!-- Lista de pedidos -->
        <div class="bg-white dark:bg-gray-900 shadow-xl rounded-lg overflow-hidden">
            @if($pedidos->isEmpty())
                <div class="p-6 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full mb-4">
                        <i class="fas fa-shopping-bag text-3xl text-gray-400 dark:text-gray-500"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">No hay pedidos realizados</h3>
                    <p class="mt-2 text-gray-500 dark:text-gray-400">Cuando realices una compra, aparecerá aquí.</p>
                    <a href="{{ route('landing') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-primary text-white rounded-md hover:bg-blue-700 transition-colors duration-200">
                        <i class="fas fa-shopping-cart mr-2"></i>
                        Ir a comprar
                    </a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Pedido #
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Fecha
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Total
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Estado
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Método de Pago
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($pedidos as $pedido)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            #{{ $pedido->pedido_id }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-white">
                                            @if($pedido->fecha_pedido instanceof \DateTime)
                                                {{ $pedido->fecha_pedido->format('d/m/Y') }}
                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $pedido->fecha_pedido->format('H:i') }}
                                                </div>
                                            @else
                                                {{ date('d/m/Y', strtotime($pedido->fecha_pedido)) }}
                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ date('H:i', strtotime($pedido->fecha_pedido)) }}
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            ${{ number_format($pedido->detalles->sum(function($detalle) { return $detalle->cantidad * $detalle->precio_unitario; }), 0, ',', '.') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @switch($pedido->estado->nombre)
                                                @case('Pendiente')
                                                    bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200
                                                    @break
                                                @case('Confirmado')
                                                    bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200
                                                    @break
                                                @case('Cancelado')
                                                    bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200
                                                    @break
                                                @default
                                                    bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200
                                            @endswitch">
                                            {{ $pedido->estado->nombre }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-white">
                                            {{ \App\Helpers\PaymentHelper::getPaymentMethodName($pedido) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('pedidos.detalle', ['pedido' => $pedido->pedido_id]) }}" 
                                           class="text-primary hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 transition-colors duration-200">
                                            Ver detalles
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800">
                    {{ $pedidos->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    /* Estilos adicionales para modo oscuro en la paginación */
    .dark .pagination .page-link {
        background-color: #1e293b;
        border-color: #475569;
        color: #e2e8f0;
    }
    
    .dark .pagination .page-link:hover {
        background-color: #334155;
        border-color: #64748b;
        color: #ffffff;
    }
    
    .dark .pagination .page-item.active .page-link {
        background-color: #3b82f6;
        border-color: #3b82f6;
        color: #ffffff;
    }
    
    .dark .pagination .page-item.disabled .page-link {
        background-color: #334155;
        border-color: #475569;
        color: #64748b;
    }
    
    /* Estilos para el botón de "Ir a comprar" */
    .dark .bg-primary {
        background-color: #3b82f6;
    }
    
    .dark .bg-primary:hover {
        background-color: #2563eb;
    }
    
    /* Transiciones suaves para todos los elementos */
    .dark * {
        transition: background-color 0.2s ease-in-out, color 0.2s ease-in-out, border-color 0.2s ease-in-out;
    }
</style>
@endsection 