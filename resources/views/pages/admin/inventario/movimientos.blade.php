@extends('layouts.app-new')

@section('title', 'Movimientos de Inventario - 4GMovil')

@section('content')
<div class="space-y-6">
    <!-- Encabezado -->
    <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Movimientos de Inventario</h1>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-300">Historial completo de entradas, salidas y ajustes de stock por variantes</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.inventario.dashboard') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                   Volver a Dashboard                </a>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Filtros</h3>
        <form method="GET" action="{{ route('admin.inventario.movimientos') }}" id="filtrosForm" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <label for="producto_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('admin.products.product') }}</label>
                <select id="producto_id" name="producto_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Todos los productos</option>
                    @foreach($productos as $producto)
                        <option value="{{ $producto->producto_id }}" {{ $productoId == $producto->producto_id ? 'selected' : '' }}>
                            {{ $producto->nombre_producto }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label for="tipo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('admin.fields.type') }} de Movimiento</label>
                <select id="tipo" name="tipo" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">{{ __('admin.webhooks.all_types') }}</option>
                    <option value="entrada" {{ $tipo == 'entrada' ? 'selected' : '' }}>Entrada</option>
                    <option value="salida" {{ $tipo == 'salida' ? 'selected' : '' }}>Salida</option>
                    <option value="ajuste" {{ $tipo == 'ajuste' ? 'selected' : '' }}>Ajuste</option>
                    <option value="devolucion" {{ $tipo == 'devolucion' ? 'selected' : '' }}>Devolución</option>
                </select>
            </div>
            
            <div>
                <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('admin.webhooks.date') }} Inicio</label>
                <input type="date" id="fecha_inicio" name="fecha_inicio" 
                       value="{{ $fechaInicio ? $fechaInicio->format('Y-m-d') : '' }}"
                       max="{{ now()->format('Y-m-d') }}"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            
            <div>
                <label for="fecha_fin" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('admin.webhooks.date') }} Fin</label>
                <input type="date" id="fecha_fin" name="fecha_fin" 
                       value="{{ $fechaFin ? $fechaFin->format('Y-m-d') : '' }}"
                       max="{{ now()->format('Y-m-d') }}"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            
            <div class="flex items-end space-x-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                   {{ __('admin.actions.filter') }}                </button>
                <button type="button" id="limpiarFiltros" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </form>
        
        <!-- Filtros rápidos -->
        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">{{ __('admin.webhooks.filters') }} Rápidos</h4>
            <div class="flex flex-wrap gap-2">
                <button type="button" class="filtro-rapido px-3 py-1 text-xs bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors" data-dias="7">
                   {{ __('admin.actions.last') }}s 7 días
                </button>
                <button type="button" class="filtro-rapido px-3 py-1 text-xs bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors" data-dias="30">
                   {{ __('admin.actions.last') }}s 30 días
                </button>
                <button type="button" class="filtro-rapido px-3 py-1 text-xs bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors" data-dias="90">
                   {{ __('admin.actions.last') }}s 90 días
                </button>
                <button type="button" class="filtro-rapido px-3 py-1 text-xs bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors" data-tipo="entrada">
                    Solo Entradas
                </button>
                <button type="button" class="filtro-rapido px-3 py-1 text-xs bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors" data-tipo="salida">
                    Solo Salidas
                </button>
            </div>
        </div>
    </div>

    <!-- Resumen de movimientos -->
    @if(isset($resumen))
    <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Resumen del Período</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 dark:bg-green-800 rounded-full">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Entradas</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $resumen['total_entradas'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-red-50 dark:bg-red-900/20 p-4 rounded-lg">
                <div class="flex items-center">
                    <div class="p-2 bg-red-100 dark:bg-red-800 rounded-full">
                        <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Salidas</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $resumen['total_salidas'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 dark:bg-yellow-800 rounded-full">
                        <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Ajustes</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $resumen['total_ajustes'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 dark:bg-blue-800 rounded-full">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Movimientos</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $resumen['movimientos_totales'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Tabla de movimientos -->
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Movimientos de Variantes</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $movimientos->count() }} movimientos encontrados</p>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('admin.webhooks.date') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('admin.products.product') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Variante</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('admin.fields.type') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Cantidad</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Motivo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Usuario</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($movimientos as $movimiento)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            {{ $movimiento->fecha_movimiento ? $movimiento->fecha_movimiento->format('d/m/Y H:i') : 'Sin fecha' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                @if($movimiento->variante->producto->imagenes->isNotEmpty())
                                    <img src="{{ asset('storage/' . $movimiento->variante->producto->imagenes[0]->ruta_imagen) }}" 
                                         class="w-8 h-8 rounded-md object-cover" 
                                         alt="{{ $movimiento->variante->producto->nombre_producto }}">
                                @else
                                    <div class="w-8 h-8 bg-gray-200 dark:bg-gray-700 rounded-md flex items-center justify-center">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $movimiento->variante->producto->nombre_producto }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">ID: {{ $movimiento->variante->producto->producto_id }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center space-x-2">
                                @if($movimiento->variante->codigo_color)
                                    <div class="w-4 h-4 rounded-full border border-gray-300" style="background-color: {{ $movimiento->variante->codigo_color }}"></div>
                                @endif
                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $movimiento->variante->nombre }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $tipoClasses = match($movimiento->tipo) {
                                    'entrada' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                    'salida' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                    'ajuste' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                    'devolucion' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                    default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200'
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $tipoClasses }}">
                                {{ ucfirst($movimiento->tipo) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            {{ $movimiento->cantidad }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            {{ $movimiento->motivo }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            {{ $movimiento->usuario->name ?? 'Sistema' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <p class="text-gray-500 dark:text-gray-400">No se encontraron movimientos para los filtros seleccionados</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('filtrosForm');
    const fechaInicio = document.getElementById('fecha_inicio');
    const fechaFin = document.getElementById('fecha_fin');
    const limpiarBtn = document.getElementById('limpiarFiltros');
    const filtrosRapidos = document.querySelectorAll('.filtro-rapido');

    // Validación de fechas en tiempo real
    function validarFechas() {
        const inicio = new Date(fechaInicio.value);
        const fin = new Date(fechaFin.value);
        
        if (fechaInicio.value && fechaFin.value) {
            if (inicio > fin) {
                fechaFin.setCustomValidity('La fecha de fin debe ser posterior a la fecha de inicio');
                fechaFin.reportValidity();
                return false;
            }
            
            // Validar que el rango no sea mayor a 1 año
            const diffTime = Math.abs(fin - inicio);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            
            if (diffDays > 365) {
                fechaFin.setCustomValidity('El rango de fechas no puede ser mayor a 1 año');
                fechaFin.reportValidity();
                return false;
            }
        }
        
        fechaFin.setCustomValidity('');
        return true;
    }

    // Event listeners para validación
    fechaInicio.addEventListener('change', validarFechas);
    fechaFin.addEventListener('change', validarFechas);

    // Validación antes de enviar el formulario
    form.addEventListener('submit', function(e) {
        if (!validarFechas()) {
            e.preventDefault();
            return false;
        }
    });

    // Limpiar filtros
    limpiarBtn.addEventListener('click', function() {
        form.reset();
        fechaInicio.value = '';
        fechaFin.value = '';
        form.submit();
    });

    // Filtros rápidos
    filtrosRapidos.forEach(btn => {
        btn.addEventListener('click', function() {
            const dias = this.dataset.dias;
            const tipo = this.dataset.tipo;
            
            if (dias) {
                const fechaFin = new Date();
                const fechaInicio = new Date();
                fechaInicio.setDate(fechaInicio.getDate() - parseInt(dias));
                
                document.getElementById('fecha_inicio').value = fechaInicio.toISOString().split('T')[0];
                document.getElementById('fecha_fin').value = fechaFin.toISOString().split('T')[0];
                document.getElementById('tipo').value = '';
            }
            
            if (tipo) {
                document.getElementById('tipo').value = tipo;
                // Limpiar fechas para filtro de tipo
                document.getElementById('fecha_inicio').value = '';
                document.getElementById('fecha_fin').value = '';
            }
            
            form.submit();
        });
    });

    // Auto-submit cuando cambian los filtros (opcional)
    const selects = document.querySelectorAll('select[name="producto_id"], select[name="tipo"]');
    selects.forEach(select => {
        select.addEventListener('change', function() {
            // Solo auto-submit si hay fechas válidas o si es un filtro de tipo
            if (this.name === 'tipo' || (fechaInicio.value && fechaFin.value)) {
                form.submit();
            }
        });
    });

    // Mostrar mensaje de validación personalizado
    function mostrarMensaje(mensaje, tipo = 'error') {
        const alertDiv = document.createElement('div');
        alertDiv.className = `fixed top-4 right-4 p-4 rounded-md shadow-lg z-50 ${
            tipo === 'error' ? 'bg-red-100 text-red-700 border border-red-200' : 
            'bg-green-100 text-green-700 border border-green-200'
        }`;
        alertDiv.innerHTML = `
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    ${tipo === 'error' ? 
                        '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>' :
                        '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>'
                    }
                </svg>
                ${mensaje}
            </div>
        `;
        
        document.body.appendChild(alertDiv);
        
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }

    // Validación adicional para fechas futuras
    const hoy = new Date().toISOString().split('T')[0];
    fechaInicio.setAttribute('max', hoy);
    fechaFin.setAttribute('max', hoy);
});
</script>
@endsection