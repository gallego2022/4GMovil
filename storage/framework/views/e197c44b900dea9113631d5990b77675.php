<?php $__env->startSection('title', 'Detalle de Pedido - 4GMovil'); ?>
<?php $__env->startSection('meta-description', 'Detalle completo de tu pedido en 4GMovil'); ?>

<?php $__env->startSection('content'); ?>
<div class="bg-gray-100 min-h-screen py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Botón de volver -->
        <div class="mb-6">
            <a href="<?php echo e(route('pedidos.historial')); ?>" class="inline-flex items-center text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left mr-2"></i>
                Volver al historial
            </a>
        </div>

        <div class="bg-white shadow-xl rounded-lg overflow-hidden">
            <!-- Encabezado del pedido -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-800 px-6 py-8 text-white">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-2xl font-bold mb-2">Pedido #<?php echo e($pedido->pedido_id); ?></h1>
                        <p class="text-blue-100">
                            <?php if($pedido->fecha_pedido instanceof \DateTime): ?>
                                Realizado el <?php echo e($pedido->fecha_pedido->format('d/m/Y')); ?> a las <?php echo e($pedido->fecha_pedido->format('H:i')); ?>

                            <?php else: ?>
                                Realizado el <?php echo e(date('d/m/Y', strtotime($pedido->fecha_pedido))); ?> a las <?php echo e(date('H:i', strtotime($pedido->fecha_pedido))); ?>

                            <?php endif; ?>
                        </p>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        <?php switch($pedido->estado->nombre):
                            case ('Pendiente'): ?>
                                bg-yellow-100 text-yellow-800
                                <?php break; ?>
                            <?php case ('Confirmado'): ?>
                                bg-green-100 text-green-800
                                <?php break; ?>
                            <?php case ('Cancelado'): ?>
                                bg-red-100 text-red-800
                                <?php break; ?>
                            <?php default: ?>
                                bg-gray-100 text-gray-800
                        <?php endswitch; ?>">
                        <?php echo e($pedido->estado->nombre); ?>

                    </span>
                </div>
            </div>

            <!-- Contenido del pedido -->
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Productos -->
                    <div>
                        <h2 class="text-lg font-semibold mb-4">Productos</h2>
                        <div class="space-y-4">
                            <?php $__currentLoopData = $pedido->detalles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detalle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg">
                                    <?php if($detalle->producto->imagenes->isNotEmpty()): ?>
                                        <img src="<?php echo e(asset('storage/' . $detalle->producto->imagenes[0]->ruta_imagen)); ?>" 
                                             alt="<?php echo e($detalle->producto->nombre_producto); ?>"
                                             class="w-16 h-16 object-cover rounded">
                                    <?php else: ?>
                                        <div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center">
                                            <i class="fas fa-image text-gray-400"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div class="flex-grow">
                                        <h3 class="font-medium"><?php echo e($detalle->producto->nombre_producto); ?></h3>
                                        <div class="grid grid-cols-2 gap-2 mt-2 text-sm text-gray-600">
                                            <p>Cantidad: <?php echo e($detalle->cantidad); ?></p>
                                            <p>Precio: $<?php echo e(number_format($detalle->precio_unitario, 0, ',', '.')); ?></p>
                                            <p class="col-span-2 font-medium text-primary">
                                                Subtotal: $<?php echo e(number_format($detalle->cantidad * $detalle->precio_unitario, 0, ',', '.')); ?>

                                            </p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>

                        <!-- Resumen de costos -->
                        <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Subtotal</span>
                                    <span class="font-medium">$<?php echo e(number_format($pedido->detalles->sum(function($detalle) { return $detalle->cantidad * $detalle->precio_unitario; }), 0, ',', '.')); ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Envío</span>
                                    <span class="font-medium">Gratis</span>
                                </div>
                                <div class="flex justify-between text-lg font-bold border-t border-gray-200 pt-2 mt-2">
                                    <span>Total</span>
                                    <span class="text-primary">$<?php echo e(number_format($pedido->detalles->sum(function($detalle) { return $detalle->cantidad * $detalle->precio_unitario; }), 0, ',', '.')); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información adicional -->
                    <div class="space-y-6">
                        <!-- Dirección de envío -->
                        <div>
                            <h2 class="text-lg font-semibold mb-4">Dirección de envío</h2>
                            <div class="p-4 bg-gray-50 rounded-lg">
                                <div class="flex items-center mb-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo e($pedido->direccion->tipo_direccion === 'casa' ? 'bg-green-100 text-green-800' : 
                                        ($pedido->direccion->tipo_direccion === 'apartamento' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800')); ?>">
                                        <?php echo e(ucfirst($pedido->direccion->tipo_direccion)); ?>

                                    </span>
                                </div>
                                <p class="text-gray-900"><?php echo e($pedido->direccion->direccion); ?></p>
                                <p class="text-gray-600 mt-1"><?php echo e($pedido->direccion->barrio); ?></p>
                                <p class="text-gray-600"><?php echo e($pedido->direccion->ciudad); ?>, <?php echo e($pedido->direccion->departamento); ?></p>
                                <p class="text-gray-600">Código Postal: <?php echo e($pedido->direccion->codigo_postal); ?></p>
                                <p class="text-gray-600">Teléfono: <?php echo e($pedido->direccion->telefono); ?></p>
                                <?php if($pedido->direccion->instrucciones): ?>
                                    <p class="text-gray-600 mt-2">
                                        <span class="font-medium">Instrucciones:</span><br>
                                        <?php echo e($pedido->direccion->instrucciones); ?>

                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Información de pago -->
                        <div>
                            <h2 class="text-lg font-semibold mb-4">Información de pago</h2>
                            <div class="p-4 bg-gray-50 rounded-lg">
                                <?php if($pedido->pago): ?>
                                    <p class="font-medium">
                                        <?php echo e(\App\Helpers\PaymentHelper::getPaymentMethodName($pedido)); ?>

                                    </p>
                                    <p class="text-gray-600 mt-1">Estado: <?php echo e(ucfirst($pedido->pago->estado ?? 'pendiente')); ?></p>
                                    <p class="text-gray-600">Fecha: 
                                        <?php if($pedido->pago->fecha_pago instanceof \Carbon\Carbon): ?>
                                            <?php echo e($pedido->pago->fecha_pago->format('d/m/Y H:i')); ?>

                                        <?php else: ?>
                                            <?php echo e(date('d/m/Y H:i', strtotime($pedido->pago->fecha_pago))); ?>

                                        <?php endif; ?>
                                    </p>
                                <?php else: ?>
                                    <p class="text-gray-600">No hay información de pago disponible</p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Botones de acción -->
                        <div class="flex flex-col sm:flex-row gap-4">
                            <button onclick="window.print()" 
                                class="inline-flex justify-center items-center px-6 py-3 border border-gray-300 rounded-md shadow-sm text-base font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                <i class="fas fa-print mr-2"></i>
                                Imprimir pedido
                            </button>
                            <a href="#" 
                                class="inline-flex justify-center items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-primary hover:bg-blue-700 transition-colors">
                                <i class="fas fa-headset mr-2"></i>
                                Contactar soporte
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('styles'); ?>
<style>
@media print {
    .container {
        max-width: 100% !important;
    }
    .shadow-md {
        box-shadow: none !important;
    }
    .bg-primary, .hover\:bg-blue-700 {
        background-color: #000 !important;
    }
    .text-primary {
        color: #000 !important;
    }
    button, a {
        display: none !important;
    }
}
</style>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.landing', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\osman\OneDrive\Documentos\GitHub\4GMovil\resources\views\modules\cliente\pedidos\detalle.blade.php ENDPATH**/ ?>