@extends('layouts.landing')

@section('title', 'Historial de Pedidos - 4GMovil')
@section('meta-description', 'Consulta tu historial de pedidos en 4GMovil')

@section('content')
<div class="bg-gray-100 min-h-screen py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Encabezado -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Historial de Pedidos</h1>
            <p class="mt-2 text-gray-600">Consulta todos tus pedidos realizados</p>
        </div>

        <!-- Lista de pedidos -->
        <div class="bg-white shadow-xl rounded-lg overflow-hidden">
            @if($pedidos->isEmpty())
                <div class="p-6 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                        <i class="fas fa-shopping-bag text-3xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">No hay pedidos realizados</h3>
                    <p class="mt-2 text-gray-500">Cuando realices una compra, aparecerá aquí.</p>
                    <a href="{{ route('landing') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-primary text-white rounded-md hover:bg-blue-700">
                        <i class="fas fa-shopping-cart mr-2"></i>
                        Ir a comprar
                    </a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Pedido #
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Fecha
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Estado
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Método de Pago
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($pedidos as $pedido)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            #{{ $pedido->pedido_id }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            @if($pedido->fecha_pedido instanceof \DateTime)
                                                {{ $pedido->fecha_pedido->format('d/m/Y') }}
                                                <div class="text-xs text-gray-500">
                                                    {{ $pedido->fecha_pedido->format('H:i') }}
                                                </div>
                                            @else
                                                {{ date('d/m/Y', strtotime($pedido->fecha_pedido)) }}
                                                <div class="text-xs text-gray-500">
                                                    {{ date('H:i', strtotime($pedido->fecha_pedido)) }}
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            ${{ number_format($pedido->detalles->sum(function($detalle) { return $detalle->cantidad * $detalle->precio_unitario; }), 0, ',', '.') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @switch($pedido->estado->nombre)
                                                @case('Pendiente')
                                                    bg-yellow-100 text-yellow-800
                                                    @break
                                                @case('Confirmado')
                                                    bg-green-100 text-green-800
                                                    @break
                                                @case('Cancelado')
                                                    bg-red-100 text-red-800
                                                    @break
                                                @default
                                                    bg-gray-100 text-gray-800
                                            @endswitch">
                                            {{ $pedido->estado->nombre }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2">
                                    {{ \App\Helpers\PaymentHelper::getPaymentMethodName($pedido) }}
                                </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('pedidos.detalle', ['pedido' => $pedido->pedido_id]) }}" 
                                           class="text-primary hover:text-blue-900">
                                            Ver detalles
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="px-6 py-4 bg-gray-50">
                    {{ $pedidos->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 