<?php $__env->startSection('title', 'Productos con Variantes'); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">Productos con Variantes</h1>
        </div>
    </div>

    <div class="row">
        <?php $__currentLoopData = $productos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $producto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <?php if($producto->imagenes->count() > 0): ?>
                    <img src="<?php echo e($producto->imagenes->first()->url); ?>" class="card-img-top" alt="<?php echo e($producto->nombre_producto); ?>" style="height: 200px; object-fit: cover;">
                <?php else: ?>
                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                        <i class="fas fa-image fa-3x text-muted"></i>
                    </div>
                <?php endif; ?>
                
                <div class="card-body">
                    <h5 class="card-title"><?php echo e($producto->nombre_producto); ?></h5>
                    <p class="card-text"><?php echo e(Str::limit($producto->descripcion, 100)); ?></p>
                    
                    <div class="mb-3">
                        <span class="h5 text-primary">$<?php echo e(number_format($producto->precio, 0, ',', '.')); ?></span>
                        
                        <?php if($producto->tieneVariantes()): ?>
                            <div class="mt-2">
                                <small class="text-muted">
                                    <i class="fas fa-palette"></i> 
                                    <?php echo e($producto->variantes->count()); ?> colores disponibles
                                </small>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if($producto->tieneVariantes()): ?>
                        <!-- Selector de variantes -->
                        <div class="mb-3">
                            <label class="form-label">Color:</label>
                            <div class="d-flex flex-wrap gap-2">
                                <?php $__currentLoopData = $producto->variantes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $variante): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" 
                                               name="variante_<?php echo e($producto->producto_id); ?>" 
                                               id="variante_<?php echo e($variante->variante_id); ?>"
                                               value="<?php echo e($variante->variante_id); ?>"
                                               data-precio-adicional="<?php echo e($variante->precio_adicional); ?>"
                                               data-stock="<?php echo e($variante->stock_disponible); ?>"
                                               data-nombre="<?php echo e($variante->nombre); ?>"
                                               data-codigo-color="<?php echo e($variante->codigo_color); ?>"
                                               <?php echo e($loop->first ? 'checked' : ''); ?>>
                                        <label class="form-check-label d-flex align-items-center" for="variante_<?php echo e($variante->variante_id); ?>">
                                            <span class="badge me-2" style="background-color: <?php echo e($variante->codigo_color); ?>; width: 20px; height: 20px; border-radius: 50%; border: 1px solid #ddd;"></span>
                                            <?php echo e($variante->nombre); ?>

                                            <?php if($variante->precio_adicional > 0): ?>
                                                <small class="text-muted ms-1">(+$<?php echo e(number_format($variante->precio_adicional, 0, ',', '.')); ?>)</small>
                                            <?php endif; ?>
                                        </label>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>

                        <!-- Información de stock por variante -->
                        <div class="mb-3">
                            <?php $__currentLoopData = $producto->variantes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $variante): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="stock-info" id="stock_info_<?php echo e($variante->variante_id); ?>" style="display: <?php echo e($loop->first ? 'block' : 'none'); ?>;">
                                    <small class="text-muted">
                                        <?php if($variante->stock_disponible > 0): ?>
                                            <i class="fas fa-check-circle text-success"></i> 
                                            <?php echo e($variante->stock_disponible); ?> unidades disponibles
                                        <?php else: ?>
                                            <i class="fas fa-times-circle text-danger"></i> 
                                            Sin stock
                                        <?php endif; ?>
                                    </small>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php else: ?>
                        <!-- Producto sin variantes -->
                        <div class="mb-3">
                            <small class="text-muted">
                                <?php if($producto->tieneStockSuficienteReal(1)): ?>
                                    <i class="fas fa-check-circle text-success"></i> 
                                    <?php echo e($producto->stock_disponible_variantes); ?> unidades disponibles
                                <?php else: ?>
                                    <i class="fas fa-times-circle text-danger"></i> 
                                    Sin stock
                                <?php endif; ?>
                            </small>
                        </div>
                    <?php endif; ?>

                    <!-- Cantidad -->
                    <div class="mb-3">
                        <label class="form-label">Cantidad:</label>
                        <div class="input-group">
                            <button class="btn btn-outline-secondary" type="button" onclick="cambiarCantidad(this, -1)">-</button>
                            <input type="number" class="form-control text-center cantidad-input" value="1" min="1" max="100" data-producto-id="<?php echo e($producto->producto_id); ?>">
                            <button class="btn btn-outline-secondary" type="button" onclick="cambiarCantidad(this, 1)">+</button>
                        </div>
                    </div>

                    <!-- Precio total -->
                    <div class="mb-3">
                        <strong>Total: <span class="precio-total" data-producto-id="<?php echo e($producto->producto_id); ?>">$<?php echo e(number_format($producto->precio, 0, ',', '.')); ?></span></strong>
                    </div>

                    <!-- Formulario para agregar al carrito -->
                    <form class="form-agregar-carrito" data-producto-id="<?php echo e($producto->producto_id); ?>">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="producto_id" value="<?php echo e($producto->producto_id); ?>">
                        <input type="hidden" name="variante_id" class="variante-seleccionada" value="<?php echo e($producto->tieneVariantes() ? $producto->variantes->first()->variante_id : ''); ?>">
                        <input type="hidden" name="cantidad" class="cantidad-seleccionada" value="1">
                        
                        <button type="submit" class="btn btn-primary btn-agregar-carrito w-100" 
                                <?php echo e((!$producto->tieneVariantes() && !$producto->tieneStockSuficienteReal(1)) || ($producto->tieneVariantes() && !$producto->variantes->first()->tieneStockSuficiente(1)) ? 'disabled' : ''); ?>>
                            <i class="fas fa-shopping-cart"></i> Agregar al Carrito
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>

<!-- Modal de confirmación -->
<div class="modal fade" id="modalConfirmacion" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Producto Agregado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex align-items-center mb-3">
                    <img id="modal-imagen" src="" alt="" class="me-3" style="width: 60px; height: 60px; object-fit: cover;">
                    <div>
                        <h6 id="modal-nombre" class="mb-1"></h6>
                        <p id="modal-variante" class="text-muted mb-0"></p>
                    </div>
                </div>
                <p class="mb-0">El producto ha sido agregado a tu carrito correctamente.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Seguir Comprando</button>
                <a href="<?php echo e(route('checkout.index')); ?>" class="btn btn-primary">Ver Carrito</a>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('js/carrito.js')); ?>"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Manejar cambio de variante
    document.querySelectorAll('input[type="radio"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const productoId = this.name.replace('variante_', '');
            const varianteId = this.value;
            const precioAdicional = parseFloat(this.dataset.precioAdicional);
            const stock = parseInt(this.dataset.stock);
            const nombre = this.dataset.nombre;
            const codigoColor = this.dataset.codigoColor;
            
            // Actualizar precio total
            const precioBase = parseFloat(document.querySelector(`[data-producto-id="${productoId}"] .precio-total`).textContent.replace('$', '').replace(/\./g, ''));
            const precioTotal = precioBase + precioAdicional;
            
            document.querySelector(`[data-producto-id="${productoId}"] .precio-total`).textContent = `$${precioTotal.toLocaleString('es-CO')}`;
            
            // Actualizar variante seleccionada en el formulario
            document.querySelector(`[data-producto-id="${productoId}"] .variante-seleccionada`).value = varianteId;
            
            // Mostrar/ocultar información de stock
            document.querySelectorAll(`[data-producto-id="${productoId}"] .stock-info`).forEach(info => {
                info.style.display = 'none';
            });
            document.getElementById(`stock_info_${varianteId}`).style.display = 'block';
            
            // Habilitar/deshabilitar botón según stock
            const boton = document.querySelector(`[data-producto-id="${productoId}"] .btn-agregar-carrito`);
            boton.disabled = stock <= 0;
        });
    });

    // Manejar cambio de cantidad
    document.querySelectorAll('.cantidad-input').forEach(input => {
        input.addEventListener('change', function() {
            const productoId = this.dataset.productoId;
            const cantidad = parseInt(this.value);
            
            // Actualizar cantidad en el formulario
            document.querySelector(`[data-producto-id="${productoId}"] .cantidad-seleccionada`).value = cantidad;
            
            // Recalcular precio total
            const precioBase = parseFloat(document.querySelector(`[data-producto-id="${productoId}"] .precio-total`).textContent.replace('$', '').replace(/\./g, ''));
            const precioTotal = precioBase * cantidad;
            
            document.querySelector(`[data-producto-id="${productoId}"] .precio-total`).textContent = `$${precioTotal.toLocaleString('es-CO')}`;
        });
    });
});

function cambiarCantidad(button, cambio) {
    const input = button.parentNode.querySelector('.cantidad-input');
    const nuevaCantidad = parseInt(input.value) + cambio;
    
    if (nuevaCantidad >= 1 && nuevaCantidad <= 100) {
        input.value = nuevaCantidad;
        input.dispatchEvent(new Event('change'));
    }
}
</script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('styles'); ?>
<style>
.stock-info {
    font-size: 0.875rem;
}

.form-check-input:checked + .form-check-label {
    font-weight: bold;
}

.cantidad-input {
    max-width: 80px;
}

.precio-total {
    color: #0d6efd;
    font-size: 1.1rem;
}
</style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.landing', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\osman\OneDrive\Documentos\GitHub\4GMovil\resources\views\productos\con-variantes.blade.php ENDPATH**/ ?>