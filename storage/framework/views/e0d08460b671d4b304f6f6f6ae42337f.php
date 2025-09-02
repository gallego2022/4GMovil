<?php $__env->startSection('title', 'Alerta de Stock - Variante'); ?>

<?php $__env->startSection('content'); ?>
    <!-- Icono y título de alerta -->
    <div class="text-center mb-4">
        <div class="text-2xl mb-2">
            <?php if($tipoAlerta === 'agotado'): ?>
                🚨
            <?php elseif($tipoAlerta === 'critico'): ?>
                ⚠️
            <?php else: ?>
                📉
            <?php endif; ?>
        </div>
        <h1 class="text-xl font-bold text-gray-700">
            <?php if($tipoAlerta === 'agotado'): ?>
                Stock Agotado
            <?php elseif($tipoAlerta === 'critico'): ?>
                Stock Crítico
            <?php else: ?>
                Stock Bajo
            <?php endif; ?>
        </h1>
        <p class="text-gray-500">Alerta de inventario - Variante de producto</p>
    </div>

    <!-- Alerta principal -->
    <div class="alert <?php if($tipoAlerta === 'agotado'): ?> alert-danger <?php elseif($tipoAlerta === 'critico'): ?> alert-warning <?php else: ?> alert-info <?php endif; ?>">
        <h2 class="font-bold mb-2">
            <?php if($tipoAlerta === 'agotado'): ?>
                🚨 ACCIÓN INMEDIATA REQUERIDA
            <?php elseif($tipoAlerta === 'critico'): ?>
                ⚠️ ACCIÓN URGENTE
            <?php else: ?>
                📉 MONITOREO REQUERIDO
            <?php endif; ?>
        </h2>
        <p class="mb-0">
            <?php if($tipoAlerta === 'agotado'): ?>
                Esta variante se ha agotado completamente. Se requiere reposición urgente para evitar pérdida de ventas.
            <?php elseif($tipoAlerta === 'critico'): ?>
                El stock está en niveles críticos. Se recomienda realizar un pedido de reposición en las próximas 24-48 horas.
            <?php else: ?>
                El stock está por debajo del mínimo recomendado. Se recomienda planificar una reposición en los próximos días.
            <?php endif; ?>
        </p>
    </div>

    <!-- Información del producto -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><?php echo e($producto->nombre_producto); ?></h3>
        </div>
        
        <div class="grid grid-2">
            <!-- Información de la variante -->
            <div>
                <div class="flex items-center mb-3">
                    <div style="width: 30px; height: 30px; background-color: <?php echo e($variante->codigo_color ?? '#CCCCCC'); ?>; border-radius: 50%; border: 2px solid #e2e8f0; margin-right: 15px;"></div>
                    <div>
                        <div class="font-bold text-gray-700"><?php echo e($variante->nombre); ?></div>
                        <div class="text-sm text-gray-500">
                            Código: <?php echo e($variante->variante_id); ?> | 
                            +$<?php echo e(number_format($variante->precio_adicional, 0, ',', '.')); ?>

                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Estado del stock -->
            <div class="text-center">
                <div class="text-2xl font-bold <?php if($tipoAlerta === 'agotado'): ?> text-red-600 <?php elseif($tipoAlerta === 'critico'): ?> text-yellow-600 <?php else: ?> text-blue-600 <?php endif; ?>">
                    <?php echo e(number_format($stockActual)); ?>

                </div>
                <div class="text-sm text-gray-500">Unidades disponibles</div>
                <div class="text-sm text-gray-500">Mínimo: <?php echo e(number_format($stockMinimo)); ?></div>
            </div>
        </div>

        <?php if($stockMinimo > 0): ?>
            <!-- Barra de progreso -->
            <div class="mt-3">
                <div style="background-color: #e2e8f0; border-radius: 10px; height: 20px; overflow: hidden;">
                    <div style="height: 100%; border-radius: 10px; background: <?php if($tipoAlerta === 'agotado'): ?> linear-gradient(90deg, #dc3545, #fd7e14) <?php elseif($tipoAlerta === 'critico'): ?> linear-gradient(90deg, #fd7e14, #ffc107) <?php else: ?> linear-gradient(90deg, #3B82F6, #93c5fd) <?php endif; ?>; width: <?php echo e(min(100, ($stockActual / $stockMinimo) * 100)); ?>%;"></div>
                </div>
                <div class="text-center mt-1 font-bold">
                    <?php echo e($porcentajeActual); ?>% del stock mínimo
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Información adicional -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">📋 Información Adicional</h3>
        </div>
        <div class="grid grid-2">
            <div>
                <div class="text-sm text-gray-500">Producto ID</div>
                <div class="font-bold"><?php echo e($producto->producto_id); ?></div>
            </div>
            <div>
                <div class="text-sm text-gray-500">Variante ID</div>
                <div class="font-bold"><?php echo e($variante->variante_id); ?></div>
            </div>
            <div>
                <div class="text-sm text-gray-500">Categoría</div>
                <div class="font-bold"><?php echo e($producto->categoria->nombre ?? 'N/A'); ?></div>
            </div>
            <div>
                <div class="text-sm text-gray-500">Marca</div>
                <div class="font-bold"><?php echo e($producto->marca->nombre ?? 'N/A'); ?></div>
            </div>
            <div>
                <div class="text-sm text-gray-500">Precio base</div>
                <div class="font-bold">$<?php echo e(number_format($producto->precio, 0, ',', '.')); ?></div>
            </div>
            <div>
                <div class="text-sm text-gray-500">Precio total</div>
                <div class="font-bold">$<?php echo e(number_format($producto->precio + $variante->precio_adicional, 0, ',', '.')); ?></div>
            </div>
        </div>
    </div>

    <!-- Recomendaciones -->
    <div class="alert alert-warning">
        <h4 class="font-bold mb-2">💡 Recomendaciones</h4>
        <?php if($tipoAlerta === 'agotado'): ?>
            <ul class="mb-0" style="padding-left: 20px;">
                <li><strong>Inmediato (0-2 horas):</strong> Contactar al proveedor para solicitar reposición urgente</li>
                <li><strong>Hoy mismo:</strong> Actualizar la página del producto para mostrar "Temporalmente agotado"</li>
                <li><strong>En 24 horas:</strong> Implementar notificación de "Volver a tener stock" para clientes interesados</li>
            </ul>
        <?php elseif($tipoAlerta === 'critico'): ?>
            <ul class="mb-0" style="padding-left: 20px;">
                <li><strong>Acción urgente:</strong> Realizar un pedido de reposición en las próximas 24-48 horas</li>
                <li><strong>Monitoreo:</strong> Revisar el stock mínimo para evitar futuros agotamientos</li>
                <li><strong>Análisis:</strong> Evaluar la demanda para ajustar el inventario</li>
            </ul>
        <?php else: ?>
            <ul class="mb-0" style="padding-left: 20px;">
                <li><strong>Monitoreo:</strong> El stock está por debajo del mínimo recomendado</li>
                <li><strong>Planificación:</strong> Se recomienda planificar una reposición en los próximos días</li>
                <li><strong>Análisis:</strong> Revisar tendencias de venta para optimizar el inventario</li>
            </ul>
        <?php endif; ?>
    </div>

    <!-- Botones de acción -->
    <div class="text-center">
        <a href="<?php echo e($productoUrl); ?>" class="btn">Ver Producto</a>
        <a href="<?php echo e(route('admin.inventario.variantes.reporte')); ?>" class="btn btn-secondary">Reporte de Inventario</a>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('correo.layouts.base', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\osman\OneDrive\Documentos\GitHub\4GMovil\resources\views\correo\stock-bajo-variante.blade.php ENDPATH**/ ?>