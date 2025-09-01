@extends('correo.layouts.base')

@section('title', 'Alerta de Stock - Variante')

@section('content')
    <!-- Icono y título de alerta -->
    <div class="text-center mb-4">
        <div class="text-2xl mb-2">
            @if($tipoAlerta === 'agotado')
                🚨
            @elseif($tipoAlerta === 'critico')
                ⚠️
            @else
                📉
            @endif
        </div>
        <h1 class="text-xl font-bold text-gray-700">
            @if($tipoAlerta === 'agotado')
                Stock Agotado
            @elseif($tipoAlerta === 'critico')
                Stock Crítico
            @else
                Stock Bajo
            @endif
        </h1>
        <p class="text-gray-500">Alerta de inventario - Variante de producto</p>
    </div>

    <!-- Alerta principal -->
    <div class="alert @if($tipoAlerta === 'agotado') alert-danger @elseif($tipoAlerta === 'critico') alert-warning @else alert-info @endif">
        <h2 class="font-bold mb-2">
            @if($tipoAlerta === 'agotado')
                🚨 ACCIÓN INMEDIATA REQUERIDA
            @elseif($tipoAlerta === 'critico')
                ⚠️ ACCIÓN URGENTE
            @else
                📉 MONITOREO REQUERIDO
            @endif
        </h2>
        <p class="mb-0">
            @if($tipoAlerta === 'agotado')
                Esta variante se ha agotado completamente. Se requiere reposición urgente para evitar pérdida de ventas.
            @elseif($tipoAlerta === 'critico')
                El stock está en niveles críticos. Se recomienda realizar un pedido de reposición en las próximas 24-48 horas.
            @else
                El stock está por debajo del mínimo recomendado. Se recomienda planificar una reposición en los próximos días.
            @endif
        </p>
    </div>

    <!-- Información del producto -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ $producto->nombre_producto }}</h3>
        </div>
        
        <div class="grid grid-2">
            <!-- Información de la variante -->
            <div>
                <div class="flex items-center mb-3">
                    <div style="width: 30px; height: 30px; background-color: {{ $variante->codigo_color ?? '#CCCCCC' }}; border-radius: 50%; border: 2px solid #e2e8f0; margin-right: 15px;"></div>
                    <div>
                        <div class="font-bold text-gray-700">{{ $variante->nombre }}</div>
                        <div class="text-sm text-gray-500">
                            Código: {{ $variante->variante_id }} | 
                            +${{ number_format($variante->precio_adicional, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Estado del stock -->
            <div class="text-center">
                <div class="text-2xl font-bold @if($tipoAlerta === 'agotado') text-red-600 @elseif($tipoAlerta === 'critico') text-yellow-600 @else text-blue-600 @endif">
                    {{ number_format($stockActual) }}
                </div>
                <div class="text-sm text-gray-500">Unidades disponibles</div>
                <div class="text-sm text-gray-500">Mínimo: {{ number_format($stockMinimo) }}</div>
            </div>
        </div>

        @if($stockMinimo > 0)
            <!-- Barra de progreso -->
            <div class="mt-3">
                <div style="background-color: #e2e8f0; border-radius: 10px; height: 20px; overflow: hidden;">
                    <div style="height: 100%; border-radius: 10px; background: @if($tipoAlerta === 'agotado') linear-gradient(90deg, #dc3545, #fd7e14) @elseif($tipoAlerta === 'critico') linear-gradient(90deg, #fd7e14, #ffc107) @else linear-gradient(90deg, #3B82F6, #93c5fd) @endif; width: {{ min(100, ($stockActual / $stockMinimo) * 100) }}%;"></div>
                </div>
                <div class="text-center mt-1 font-bold">
                    {{ $porcentajeActual }}% del stock mínimo
                </div>
            </div>
        @endif
    </div>

    <!-- Información adicional -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">📋 Información Adicional</h3>
        </div>
        <div class="grid grid-2">
            <div>
                <div class="text-sm text-gray-500">Producto ID</div>
                <div class="font-bold">{{ $producto->producto_id }}</div>
            </div>
            <div>
                <div class="text-sm text-gray-500">Variante ID</div>
                <div class="font-bold">{{ $variante->variante_id }}</div>
            </div>
            <div>
                <div class="text-sm text-gray-500">Categoría</div>
                <div class="font-bold">{{ $producto->categoria->nombre ?? 'N/A' }}</div>
            </div>
            <div>
                <div class="text-sm text-gray-500">Marca</div>
                <div class="font-bold">{{ $producto->marca->nombre ?? 'N/A' }}</div>
            </div>
            <div>
                <div class="text-sm text-gray-500">Precio base</div>
                <div class="font-bold">${{ number_format($producto->precio, 0, ',', '.') }}</div>
            </div>
            <div>
                <div class="text-sm text-gray-500">Precio total</div>
                <div class="font-bold">${{ number_format($producto->precio + $variante->precio_adicional, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    <!-- Recomendaciones -->
    <div class="alert alert-warning">
        <h4 class="font-bold mb-2">💡 Recomendaciones</h4>
        @if($tipoAlerta === 'agotado')
            <ul class="mb-0" style="padding-left: 20px;">
                <li><strong>Inmediato (0-2 horas):</strong> Contactar al proveedor para solicitar reposición urgente</li>
                <li><strong>Hoy mismo:</strong> Actualizar la página del producto para mostrar "Temporalmente agotado"</li>
                <li><strong>En 24 horas:</strong> Implementar notificación de "Volver a tener stock" para clientes interesados</li>
            </ul>
        @elseif($tipoAlerta === 'critico')
            <ul class="mb-0" style="padding-left: 20px;">
                <li><strong>Acción urgente:</strong> Realizar un pedido de reposición en las próximas 24-48 horas</li>
                <li><strong>Monitoreo:</strong> Revisar el stock mínimo para evitar futuros agotamientos</li>
                <li><strong>Análisis:</strong> Evaluar la demanda para ajustar el inventario</li>
            </ul>
        @else
            <ul class="mb-0" style="padding-left: 20px;">
                <li><strong>Monitoreo:</strong> El stock está por debajo del mínimo recomendado</li>
                <li><strong>Planificación:</strong> Se recomienda planificar una reposición en los próximos días</li>
                <li><strong>Análisis:</strong> Revisar tendencias de venta para optimizar el inventario</li>
            </ul>
        @endif
    </div>

    <!-- Botones de acción -->
    <div class="text-center">
        <a href="{{ $productoUrl }}" class="btn">Ver Producto</a>
        <a href="{{ route('admin.inventario.variantes.reporte') }}" class="btn btn-secondary">Reporte de Inventario</a>
    </div>
@endsection
