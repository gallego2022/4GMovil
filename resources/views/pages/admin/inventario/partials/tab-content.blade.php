@php
    $productosCriticos = $alertas['datos_paginados'] && $alertas['tipo_actual'] === 'criticos' ? $alertas['datos_paginados'] : collect();
    $productosStockBajo = $alertas['datos_paginados'] && $alertas['tipo_actual'] === 'bajo' ? $alertas['datos_paginados'] : collect();
    $variantesAgotadas = $alertas['datos_paginados'] && $alertas['tipo_actual'] === 'agotadas' ? $alertas['datos_paginados'] : collect();
@endphp

@if($tipo === 'criticos')
    <!-- Tab Productos Críticos -->
    <div class="mb-4">
        <h3 class="text-lg font-semibold text-red-600 dark:text-red-400 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
            </svg>
            Productos con Stock Crítico
        </h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Productos que requieren atención inmediata</p>
    </div>
    
    @if($productosCriticos->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-red-50 dark:bg-red-900/20">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Producto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Stock Actual</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Variantes Problemáticas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($productosCriticos as $alerta)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if($alerta['producto']->imagenes->isNotEmpty())
                                        <img src="{{ asset('storage/' . $alerta['producto']->imagenes[0]->ruta_imagen) }}" 
                                             class="h-12 w-12 rounded-lg object-cover mr-3" 
                                             alt="{{ $alerta['producto']->nombre_producto }}">
                                    @else
                                        <div class="h-12 w-12 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center mr-3">
                                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $alerta['producto']->nombre_producto }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $alerta['producto']->categoria->nombre ?? 'Sin categoría' }} | 
                                            {{ $alerta['producto']->marca->nombre ?? 'Sin marca' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                    {{ number_format($alerta['stock_actual']) }}
                                </span>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $alerta['porcentaje'] }}% del stock inicial</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($alerta['total_variantes_problematicas'] > 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                        {{ $alerta['total_variantes_problematicas'] }} variantes
                                    </span>
                                @else
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Sin variantes</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center gap-2">
                                    <button onclick="abrirModalReponer({{ $alerta['producto']->producto_id }})" 
                                            class="inline-flex items-center px-3 py-1.5 border border-green-300 dark:border-green-600 shadow-sm text-sm font-medium rounded-md text-green-700 dark:text-green-300 bg-green-50 dark:bg-green-900/20 hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        Reponer
                                    </button>
                                    @if($alerta['total_variantes_problematicas'] > 0)
                                        <button onclick="mostrarVariantes({{ $alerta['producto']->producto_id }})" 
                                                class="inline-flex items-center px-3 py-1.5 border border-blue-300 dark:border-blue-600 shadow-sm text-sm font-medium rounded-md text-blue-700 dark:text-blue-300 bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                                            </svg>
                                            Variantes
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Paginación -->
        @if($alertas['datos_paginados'] && $alertas['tipo_actual'] === 'criticos')
            <div class="mt-6">
                {{ $alertas['datos_paginados']->appends(['tipo' => 'criticos'])->links() }}
            </div>
        @endif
    @else
        <div class="text-center py-12">
            <svg class="w-16 h-16 text-green-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">¡Excelente!</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">No hay productos con stock crítico</p>
        </div>
    @endif

@elseif($tipo === 'bajo')
    <!-- Tab Stock Bajo -->
    <div class="mb-4">
        <h3 class="text-lg font-semibold text-yellow-600 dark:text-yellow-400 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Productos con Stock Bajo
        </h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Productos que requieren monitoreo y posible reposición</p>
    </div>
    
    @if($productosStockBajo->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-yellow-50 dark:bg-yellow-900/20">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Producto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Stock Actual</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Variantes Problemáticas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($productosStockBajo as $alerta)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if($alerta['producto']->imagenes->isNotEmpty())
                                        <img src="{{ asset('storage/' . $alerta['producto']->imagenes[0]->ruta_imagen) }}" 
                                             class="h-12 w-12 rounded-lg object-cover mr-3" 
                                             alt="{{ $alerta['producto']->nombre_producto }}">
                                    @else
                                        <div class="h-12 w-12 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center mr-3">
                                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $alerta['producto']->nombre_producto }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $alerta['producto']->categoria->nombre ?? 'Sin categoría' }} | 
                                            {{ $alerta['producto']->marca->nombre ?? 'Sin marca' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                    {{ number_format($alerta['stock_actual']) }}
                                </span>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $alerta['porcentaje'] }}% del stock inicial</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($alerta['total_variantes_problematicas'] > 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                        {{ $alerta['total_variantes_problematicas'] }} variantes
                                    </span>
                                @else
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Sin variantes</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center gap-2">
                                    <button onclick="abrirModalReponer({{ $alerta['producto']->producto_id }})" 
                                            class="inline-flex items-center px-3 py-1.5 border border-green-300 dark:border-green-600 shadow-sm text-sm font-medium rounded-md text-green-700 dark:text-green-300 bg-green-50 dark:bg-green-900/20 hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        Reponer
                                    </button>
                                    @if($alerta['total_variantes_problematicas'] > 0)
                                        <button onclick="mostrarVariantes({{ $alerta['producto']->producto_id }})" 
                                                class="inline-flex items-center px-3 py-1.5 border border-blue-300 dark:border-blue-600 shadow-sm text-sm font-medium rounded-md text-blue-700 dark:text-blue-300 bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                                            </svg>
                                            Variantes
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Paginación -->
        @if($alertas['datos_paginados'] && $alertas['tipo_actual'] === 'bajo')
            <div class="mt-6">
                {{ $alertas['datos_paginados']->appends(['tipo' => 'bajo'])->links() }}
            </div>
        @endif
    @else
        <div class="text-center py-12">
            <svg class="w-16 h-16 text-green-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">¡Perfecto!</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">No hay productos con stock bajo</p>
        </div>
    @endif

@elseif($tipo === 'agotadas')
    <!-- Tab Variantes Agotadas -->
    <div class="mb-4">
        <h3 class="text-lg font-semibold text-blue-600 dark:text-blue-400 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Variantes Completamente Agotadas
        </h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Variantes que requieren reposición inmediata</p>
    </div>
    
    @if($variantesAgotadas->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-blue-50 dark:bg-blue-900/20">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Producto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Variante</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($variantesAgotadas as $alerta)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if($alerta['producto']->imagenes->isNotEmpty())
                                        <img src="{{ asset('storage/' . $alerta['producto']->imagenes[0]->ruta_imagen) }}" 
                                             class="h-12 w-12 rounded-lg object-cover mr-3" 
                                             alt="{{ $alerta['producto']->nombre_producto }}">
                                    @else
                                        <div class="h-12 w-12 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center mr-3">
                                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $alerta['producto']->nombre_producto }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $alerta['producto']->categoria->nombre ?? 'Sin categoría' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-5 w-5 rounded border border-gray-300 dark:border-gray-600 mr-2" 
                                         style="background-color: {{ $alerta['variante']->codigo_color ?? '#CCCCCC' }};"></div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $alerta['variante']->nombre }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            +${{ number_format($alerta['variante']->precio_adicional, 0, ',', '.') }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                    Agotado
                                </span>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">0 unidades disponibles</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center gap-2">
                                    <button onclick="abrirModalReponer({{ $alerta['producto']->producto_id }}, {{ $alerta['variante']->variante_id }})" 
                                            class="inline-flex items-center px-3 py-1.5 border border-green-300 dark:border-green-600 shadow-sm text-sm font-medium rounded-md text-green-700 dark:text-green-300 bg-green-50 dark:bg-green-900/20 hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        Reponer
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Paginación -->
        @if($alertas['datos_paginados'] && $alertas['tipo_actual'] === 'agotadas')
            <div class="mt-6">
                {{ $alertas['datos_paginados']->appends(['tipo' => 'agotadas'])->links() }}
            </div>
        @endif
    @else
        <div class="text-center py-12">
            <svg class="w-16 h-16 text-green-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">¡Excelente!</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">No hay variantes agotadas</p>
        </div>
    @endif
@endif

