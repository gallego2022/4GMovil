@extends('layouts.admin')

@section('title', 'Alertas de Stock Optimizadas')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Alertas de Stock Optimizadas</h1>
            <p class="text-muted">Gestión inteligente de alertas agrupadas por producto</p>
        </div>
        <div class="d-flex gap-2">
            <button id="refresh-alerts" class="btn btn-outline-primary">
                <i class="fas fa-sync-alt"></i> Actualizar
            </button>
            <a href="{{ route('admin.inventario.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Volver al Dashboard
            </a>
        </div>
    </div>

    <!-- Estadísticas generales -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Productos Críticos
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="productos-criticos">
                                {{ $alertas['productos_criticos']->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Stock Bajo
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="productos-stock-bajo">
                                {{ $alertas['productos_stock_bajo']->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Variantes Agotadas
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="variantes-agotadas">
                                {{ $alertas['variantes_agotadas']->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Alertas
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-alertas">
                                {{ $alertas['total_alertas'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-bell fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs de navegación -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <ul class="nav nav-tabs card-header-tabs" id="alertTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="criticos-tab" data-bs-toggle="tab" data-bs-target="#criticos" type="button" role="tab">
                        <i class="fas fa-exclamation-triangle text-danger"></i> Críticos
                        <span class="badge bg-danger ms-2">{{ $alertas['productos_criticos']->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="bajo-tab" data-bs-toggle="tab" data-bs-target="#bajo" type="button" role="tab">
                        <i class="fas fa-exclamation-circle text-warning"></i> Stock Bajo
                        <span class="badge bg-warning ms-2">{{ $alertas['productos_stock_bajo']->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="agotadas-tab" data-bs-toggle="tab" data-bs-target="#agotadas" type="button" role="tab">
                        <i class="fas fa-times-circle text-info"></i> Agotadas
                        <span class="badge bg-info ms-2">{{ $alertas['variantes_agotadas']->count() }}</span>
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body">
            <div class="tab-content" id="alertTabsContent">
                <!-- Tab Productos Críticos -->
                <div class="tab-pane fade show active" id="criticos" role="tabpanel">
                    <div class="mb-3">
                        <h5 class="text-danger">
                            <i class="fas fa-exclamation-triangle"></i> Productos con Stock Crítico
                        </h5>
                        <p class="text-muted">Productos que requieren atención inmediata</p>
                    </div>
                    
                    @if($alertas['productos_criticos']->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-danger">
                                    <tr>
                                        <th>Producto</th>
                                        <th>Stock Actual</th>
                                        <th>Variantes Problemáticas</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($alertas['productos_criticos'] as $alerta)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($alerta['producto']->imagenes->isNotEmpty())
                                                        <img src="{{ asset('storage/' . $alerta['producto']->imagenes[0]->ruta_imagen) }}" 
                                                             class="rounded me-3" style="width: 50px; height: 50px; object-fit: cover;" 
                                                             alt="{{ $alerta['producto']->nombre_producto }}">
                                                    @else
                                                        <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" 
                                                             style="width: 50px; height: 50px;">
                                                            <i class="fas fa-image text-muted"></i>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <h6 class="mb-0">{{ $alerta['producto']->nombre_producto }}</h6>
                                                        <small class="text-muted">
                                                            {{ $alerta['producto']->categoria->nombre_categoria ?? 'Sin categoría' }} | 
                                                            {{ $alerta['producto']->marca->nombre_marca ?? 'Sin marca' }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-danger fs-6">{{ number_format($alerta['stock_actual']) }}</span>
                                                <br>
                                                <small class="text-muted">{{ $alerta['porcentaje'] }}% del stock inicial</small>
                                            </td>
                                            <td>
                                                @if($alerta['total_variantes_problematicas'] > 0)
                                                    <span class="badge bg-warning">
                                                        {{ $alerta['total_variantes_problematicas'] }} variantes
                                                    </span>
                                                @else
                                                    <span class="text-muted">Sin variantes</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.productos.show', $alerta['producto']->producto_id) }}" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if($alerta['total_variantes_problematicas'] > 0)
                                                        <button class="btn btn-sm btn-outline-info" 
                                                                onclick="mostrarVariantes({{ $alerta['producto']->producto_id }})">
                                                            <i class="fas fa-list"></i> Ver Variantes
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <h5 class="text-success">¡Excelente!</h5>
                            <p class="text-muted">No hay productos con stock crítico</p>
                        </div>
                    @endif
                </div>

                <!-- Tab Stock Bajo -->
                <div class="tab-pane fade" id="bajo" role="tabpanel">
                    <div class="mb-3">
                        <h5 class="text-warning">
                            <i class="fas fa-exclamation-circle"></i> Productos con Stock Bajo
                        </h5>
                        <p class="text-muted">Productos que requieren monitoreo y posible reposición</p>
                    </div>
                    
                    @if($alertas['productos_stock_bajo']->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-warning">
                                    <tr>
                                        <th>Producto</th>
                                        <th>Stock Actual</th>
                                        <th>Variantes Problemáticas</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($alertas['productos_stock_bajo'] as $alerta)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($alerta['producto']->imagenes->isNotEmpty())
                                                        <img src="{{ asset('storage/' . $alerta['producto']->imagenes[0]->ruta_imagen) }}" 
                                                             class="rounded me-3" style="width: 50px; height: 50px; object-fit: cover;" 
                                                             alt="{{ $alerta['producto']->nombre_producto }}">
                                                    @else
                                                        <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" 
                                                             style="width: 50px; height: 50px;">
                                                            <i class="fas fa-image text-muted"></i>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <h6 class="mb-0">{{ $alerta['producto']->nombre_producto }}</h6>
                                                        <small class="text-muted">
                                                            {{ $alerta['producto']->categoria->nombre_categoria ?? 'Sin categoría' }} | 
                                                            {{ $alerta['producto']->marca->nombre_marca ?? 'Sin marca' }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-warning fs-6">{{ number_format($alerta['stock_actual']) }}</span>
                                                <br>
                                                <small class="text-muted">{{ $alerta['porcentaje'] }}% del stock inicial</small>
                                            </td>
                                            <td>
                                                @if($alerta['total_variantes_problematicas'] > 0)
                                                    <span class="badge bg-info">
                                                        {{ $alerta['total_variantes_problematicas'] }} variantes
                                                    </span>
                                                @else
                                                    <span class="text-muted">Sin variantes</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.productos.show', $alerta['producto']->producto_id) }}" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if($alerta['total_variantes_problematicas'] > 0)
                                                        <button class="btn btn-sm btn-outline-info" 
                                                                onclick="mostrarVariantes({{ $alerta['producto']->producto_id }})">
                                                            <i class="fas fa-list"></i> Ver Variantes
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <h5 class="text-success">¡Perfecto!</h5>
                            <p class="text-muted">No hay productos con stock bajo</p>
                        </div>
                    @endif
                </div>

                <!-- Tab Variantes Agotadas -->
                <div class="tab-pane fade" id="agotadas" role="tabpanel">
                    <div class="mb-3">
                        <h5 class="text-info">
                            <i class="fas fa-times-circle"></i> Variantes Completamente Agotadas
                        </h5>
                        <p class="text-muted">Variantes que requieren reposición inmediata</p>
                    </div>
                    
                    @if($alertas['variantes_agotadas']->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-info">
                                    <tr>
                                        <th>Producto</th>
                                        <th>Variante</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($alertas['variantes_agotadas'] as $alerta)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($alerta['producto']->imagenes->isNotEmpty())
                                                        <img src="{{ asset('storage/' . $alerta['producto']->imagenes[0]->ruta_imagen) }}" 
                                                             class="rounded me-3" style="width: 50px; height: 50px; object-fit: cover;" 
                                                             alt="{{ $alerta['producto']->nombre_producto }}">
                                                    @else
                                                        <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" 
                                                             style="width: 50px; height: 50px;">
                                                            <i class="fas fa-image text-muted"></i>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <h6 class="mb-0">{{ $alerta['producto']->nombre_producto }}</h6>
                                                        <small class="text-muted">
                                                            {{ $alerta['producto']->categoria->nombre_categoria ?? 'Sin categoría' }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="rounded me-2" 
                                                         style="width: 20px; height: 20px; background-color: {{ $alerta['variante']->codigo_color ?? '#CCCCCC' }}; border: 1px solid #dee2e6;"></div>
                                                    <div>
                                                        <strong>{{ $alerta['variante']->nombre }}</strong>
                                                        <br>
                                                        <small class="text-muted">
                                                            +${{ number_format($alerta['variante']->precio_adicional, 0, ',', '.') }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-danger fs-6">Agotado</span>
                                                <br>
                                                <small class="text-muted">0 unidades disponibles</small>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.productos.show', $alerta['producto']->producto_id) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i> Ver Producto
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <h5 class="text-success">¡Excelente!</h5>
                            <p class="text-muted">No hay variantes agotadas</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para mostrar variantes problemáticas -->
<div class="modal fade" id="variantesModal" tabindex="-1" aria-labelledby="variantesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="variantesModalLabel">
                    <i class="fas fa-list"></i> Variantes Problemáticas
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="variantes-content">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="mt-2">Cargando variantes...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Función para mostrar variantes problemáticas
function mostrarVariantes(productoId) {
    $('#variantesModal').modal('show');
    
    // Mostrar loading
    $('#variantes-content').html(`
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-2">Cargando variantes...</p>
        </div>
    `);
    
    // Hacer petición AJAX
    $.ajax({
        url: '{{ route("admin.alertas.variantes") }}',
        method: 'GET',
        data: { producto_id: productoId },
        success: function(response) {
            if (response.success && response.variantes.length > 0) {
                let html = '<div class="table-responsive"><table class="table table-sm">';
                html += '<thead><tr><th>Variante</th><th>Tipo</th><th>Stock</th><th>Mínimo</th><th>%</th></tr></thead><tbody>';
                
                response.variantes.forEach(function(variante) {
                    const badgeClass = variante.tipo_alerta === 'critico' ? 'bg-danger' : 'bg-warning';
                    const iconClass = variante.tipo_alerta === 'critico' ? 'fa-exclamation-triangle' : 'fa-exclamation-circle';
                    
                    html += `
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="rounded me-2" style="width: 15px; height: 15px; background-color: ${variante.codigo_color || '#CCCCCC'}; border: 1px solid #dee2e6;"></div>
                                    <div>
                                        <strong>${variante.nombre}</strong>
                                        <br>
                                        <small class="text-muted">+$${variante.precio_adicional.toLocaleString()}</small>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge ${badgeClass}"><i class="fas ${iconClass}"></i> ${variante.tipo_alerta.toUpperCase()}</span></td>
                            <td><strong>${variante.stock_actual}</strong></td>
                            <td>${variante.stock_minimo}</td>
                            <td>${variante.porcentaje}%</td>
                        </tr>
                    `;
                });
                
                html += '</tbody></table></div>';
                $('#variantes-content').html(html);
            } else {
                $('#variantes-content').html(`
                    <div class="text-center py-4">
                        <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                        <p class="text-muted">No hay variantes problemáticas para este producto</p>
                    </div>
                `);
            }
        },
        error: function() {
            $('#variantes-content').html(`
                <div class="text-center py-4">
                    <i class="fas fa-exclamation-triangle fa-2x text-danger mb-2"></i>
                    <p class="text-danger">Error al cargar las variantes</p>
                </div>
            `);
        }
    });
}

// Función para actualizar estadísticas
function actualizarEstadisticas() {
    $.ajax({
        url: '{{ route("admin.alertas.estadisticas") }}',
        method: 'GET',
        success: function(response) {
            if (response.success) {
                $('#productos-criticos').text(response.estadisticas.productos_criticos);
                $('#productos-stock-bajo').text(response.estadisticas.productos_stock_bajo);
                $('#variantes-agotadas').text(response.estadisticas.variantes_agotadas);
                $('#total-alertas').text(response.estadisticas.total_alertas);
            }
        }
    });
}

// Event listeners
$(document).ready(function() {
    // Botón de actualizar
    $('#refresh-alerts').click(function() {
        location.reload();
    });
    
    // Actualizar estadísticas cada 30 segundos
    setInterval(actualizarEstadisticas, 30000);
});
</script>
@endpush
