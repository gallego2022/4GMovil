@extends('layouts.app-new')

@section('title', 'Detalle de Pedido #' . $pedido->pedido_id . ' - 4GMovil')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-900">
                Detalle del Pedido #{{ $pedido->pedido_id }}
            </h1>
            <a href="{{ route('admin.pedidos.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <i class="fas fa-arrow-left mr-2"></i>
                Volver a la lista
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4" role="alert">
            <p class="font-bold">¡Éxito!</p>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
            <p class="font-bold">Error</p>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <!-- Información del Cliente -->
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Información del Cliente</h3>
        </div>
        <div class="border-t border-gray-200">
            <dl>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Nombre</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $pedido->usuario->nombre_usuario }}
                    </dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $pedido->usuario->correo_electronico }}
                    </dd>
                </div>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Teléfono</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $pedido->direccion->telefono }}
                    </dd>
                </div>
            </dl>
        </div>

        <!-- Estado del Pedido -->
        <div class="px-4 py-5 sm:px-6 border-t border-gray-200">
            <div class="flex justify-between items-start">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Estado del Pedido</h3>
                <form action="{{ route('admin.pedidos.updateEstado', $pedido->pedido_id) }}" method="POST" class="flex items-center space-x-4">
                    @csrf
                    @method('PUT')
                    <select name="estado_id" class="rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        @foreach(\App\Models\EstadoPedido::all() as $estado)
                            <option value="{{ $estado->estado_id }}" 
                                {{ $pedido->estado_id == $estado->estado_id ? 'selected' : '' }}>
                                {{ $estado->nombre_estado }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                        Actualizar Estado
                    </button>
                </form>
            </div>
        </div>

        <!-- Detalles del Pedido -->
        <div class="px-4 py-5 sm:px-6 border-t border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Detalles del Pedido</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Producto
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Cantidad
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Precio Unitario
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Subtotal
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($pedido->detalles as $detalle)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $detalle->producto->nombre_producto }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $detalle->cantidad }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">${{ number_format($detalle->precio_unitario, 0, ',', '.') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">${{ number_format($detalle->cantidad * $detalle->precio_unitario, 0, ',', '.') }}</div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-right font-medium">Total:</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-lg font-bold text-primary">${{ number_format($pedido->total, 0, ',', '.') }}</div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Información de Envío -->
        <div class="px-4 py-5 sm:px-6 border-t border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Información de Envío</h3>
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Tipo de dirección</p>
                        <p class="mt-1 text-sm text-gray-900">{{ ucfirst($pedido->direccion->tipo_direccion) }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Dirección completa</p>
                        <p class="mt-1 text-sm text-gray-900">{{ $pedido->direccion->direccion }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Barrio</p>
                        <p class="mt-1 text-sm text-gray-900">{{ $pedido->direccion->barrio }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Ciudad</p>
                        <p class="mt-1 text-sm text-gray-900">{{ $pedido->direccion->ciudad }}, {{ $pedido->direccion->departamento }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Código Postal</p>
                        <p class="mt-1 text-sm text-gray-900">{{ $pedido->direccion->codigo_postal }}</p>
                    </div>
                    @if($pedido->direccion->instrucciones)
                        <div class="col-span-2">
                            <p class="text-sm font-medium text-gray-500">Instrucciones adicionales</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $pedido->direccion->instrucciones }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Información de Pago -->
        <div class="px-4 py-5 sm:px-6 border-t border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Información de Pago</h3>
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Método de pago</p>
                        <p class="mt-1 text-sm text-gray-900">{{ $pedido->pago->metodoPago->nombre_metodo }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Estado del pago</p>
                        <p class="mt-1 text-sm text-gray-900">{{ ucfirst($pedido->pago->estado ?? 'pendiente') }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Fecha del pago</p>
                        <p class="mt-1 text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($pedido->pago->fecha_pago)->format('d/m/Y H:i') }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Monto</p>
                        <p class="mt-1 text-sm text-gray-900">${{ number_format($pedido->pago->monto, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 