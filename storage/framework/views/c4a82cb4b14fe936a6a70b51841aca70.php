

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-wrap -mx-4">
        <!-- Formulario de checkout -->
        <div class="w-full lg:w-2/3 px-4 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-2xl font-bold mb-6">Finalizar Compra</h2>

                <?php if(session('mensaje')): ?>
                    <div class="mb-4 rounded-md p-4 <?php echo e(session('tipo', 'success') === 'success' ? 'bg-green-50 text-green-800' : 'bg-red-50 text-red-800'); ?>">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <?php if(session('tipo', 'success') === 'success'): ?>
                                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                <?php else: ?>
                                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                <?php endif; ?>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium"><?php echo e(session('mensaje')); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if($errors->any()): ?>
                    <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">Por favor, corrige los siguientes errores:</h3>
                                <ul class="mt-1 text-sm text-red-700 list-disc list-inside">
                                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li><?php echo e($error); ?></li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                
                <form id="checkoutForm" action="<?php echo e(route('checkout.process')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    
                    <!-- Dirección de envío -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-4">Dirección de envío</h3>
                        
                        <?php if($direcciones->isEmpty()): ?>
                            <div class="bg-yellow-50 border border-yellow-200 rounded p-4 mb-4">
                                <p class="text-yellow-700">No tienes direcciones guardadas.</p>
                                <a href="<?php echo e(route('direcciones.create')); ?>" class="text-primary hover:text-blue-700 mt-2 inline-block">
                                    <i class="fas fa-plus-circle mr-1"></i>
                                    Agregar nueva dirección
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="space-y-4">
                                <?php $__currentLoopData = $direcciones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $direccion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="border rounded p-4 hover:border-blue-500 transition-colors <?php echo e(old('direccion_id') == $direccion->direccion_id ? 'border-blue-500' : ''); ?>">
                                        <label class="flex items-start cursor-pointer">
                                            <input type="radio" 
                                                   name="direccion_id" 
                                                   value="<?php echo e($direccion->direccion_id); ?>" 
                                                   class="mt-1" 
                                                   required
                                                   <?php echo e(old('direccion_id') == $direccion->direccion_id ? 'checked' : ''); ?>>
                                            <div class="ml-3">
                                                <div class="flex items-center mb-1">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo e($direccion->tipo_direccion === 'casa' ? 'bg-green-100 text-green-800' : ($direccion->tipo_direccion === 'apartamento' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800')); ?>">
                                                        <?php echo e(ucfirst($direccion->tipo_direccion)); ?>

                                                    </span>
                                                </div>
                                                <p class="text-gray-900"><?php echo e($direccion->direccion); ?></p>
                                                <p class="text-gray-600"><?php echo e($direccion->barrio); ?></p>
                                                <p class="text-gray-600"><?php echo e($direccion->ciudad); ?>, <?php echo e($direccion->departamento); ?></p>
                                                <p class="text-gray-600">Teléfono: <?php echo e($direccion->telefono); ?></p>
                                            </div>
                                        </label>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                            
                            <a href="<?php echo e(route('direcciones.create')); ?>" class="text-primary hover:text-blue-700 mt-4 inline-block">
                                <i class="fas fa-plus-circle mr-1"></i>
                                Agregar otra dirección
                            </a>
                        <?php endif; ?>

                        <?php $__errorArgs = ['direccion_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-2 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    
                    <!-- Método de pago -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-4">Método de pago</h3>
                        
                        <div class="space-y-4">
                            <?php $__currentLoopData = $metodosPago; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $metodo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="border rounded p-4 hover:border-blue-500 transition-colors <?php echo e(old('metodo_pago_id') == $metodo->metodo_id ? 'border-blue-500' : ''); ?>">
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" 
                                               name="metodo_pago_id" 
                                               value="<?php echo e($metodo->metodo_id); ?>" 
                                               class="mr-3" 
                                               required
                                               <?php echo e(old('metodo_pago_id') == $metodo->metodo_id ? 'checked' : ''); ?>>
                                        
                                        <?php if($metodo->nombre === 'Stripe'): ?>
                                            <div class="flex items-center">
                                                <svg class="w-6 h-6 mr-2 text-blue-600" viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M13.976 9.15c-2.172-.806-3.356-1.426-3.356-2.409 0-.831.683-1.305 1.901-1.305 2.227 0 4.515.858 6.09 1.631l.89-5.494C18.252.975 15.697 0 12.165 0 9.667 0 7.589.654 6.104 1.872 4.56 3.147 3.757 4.992 3.757 7.218c0 4.039 2.467 5.76 6.476 7.219 2.585.831 3.47 1.426 3.47 2.338 0 .914-.796 1.431-2.126 1.431-1.72 0-4.516-1.053-6.378-2.168l-.889 5.52c2.172 1.281 5.274 2.196 8.876 2.196 2.585 0 4.729-.624 6.199-1.588 1.544-1.013 2.347-2.847 2.347-5.077 0-4.716-2.508-6.489-6.594-7.88zM24 16.716V0h-5.98v16.716H24z"/>
                                                </svg>
                                                <span class="font-medium"><?php echo e($metodo->nombre); ?></span>
                                                <span class="ml-2 text-sm text-gray-500">(Tarjeta de crédito/débito)</span>
                                            </div>
                                        <?php else: ?>
                                            <span class="font-medium"><?php echo e($metodo->nombre); ?></span>
                                        <?php endif; ?>
                                    </label>
                                    
                                    <?php if($metodo->nombre === 'Stripe'): ?>
                                        <div class="mt-2 ml-9">
                                            <p class="text-sm text-gray-600">
                                                Pago seguro con tarjeta de crédito o débito. Tus datos están protegidos con encriptación SSL.
                                            </p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>

                        <?php $__errorArgs = ['metodo_pago_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-2 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    
                    <button type="submit" 
                            id="confirmarPedido"
                            class="w-full bg-primary text-white py-3 rounded-lg font-medium hover:bg-blue-700 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                            <?php echo e($direcciones->isEmpty() ? 'disabled' : ''); ?>>
                        <span class="inline-flex items-center">
                            <span class="mr-2">Confirmar Pedido</span>
                            <span id="spinner" class="hidden">
                                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </span>
                        </span>
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Resumen del pedido -->
        <div class="w-full lg:w-1/3 px-4">
            <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                <h3 class="text-lg font-semibold mb-4">Resumen del pedido</h3>
                
                <div class="space-y-4 mb-6">
                    <?php $total = 0; ?>
                    <?php $__currentLoopData = $cart; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium"><?php echo e($item['name']); ?></p>
                                <p class="text-gray-600">Cantidad: <?php echo e($item['quantity']); ?></p>
                            </div>
                            <p class="font-medium">$<?php echo e(number_format($item['price'] * $item['quantity'], 0, ',', '.')); ?></p>
                        </div>
                        <?php $total += $item['price'] * $item['quantity']; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                
                <div class="border-t pt-4">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-medium">$<?php echo e(number_format($total, 0, ',', '.')); ?></span>
                    </div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-600">Envío</span>
                        <span class="font-medium">Gratis</span>
                    </div>
                    <div class="flex justify-between items-center text-lg font-bold">
                        <span>Total</span>
                        <span class="text-primary">$<?php echo e(number_format($total, 0, ',', '.')); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('checkoutForm');
    const submitButton = document.getElementById('confirmarPedido');
    const spinner = document.getElementById('spinner');
    const cart = <?php echo json_encode($cart, 15, 512) ?>;

    // Función para verificar stock en tiempo real
    async function verificarStock() {
        try {
            const response = await fetch('<?php echo e(route("checkout.verificar-stock")); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ cart: cart })
            });

            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error al verificar stock:', error);
            return { success: false, errores: ['Error al verificar stock'] };
        }
    }

    // Función para mostrar alerta de stock
    function mostrarAlertaStock(errores) {
        let mensaje = '<ul class="text-left">';
        errores.forEach(error => {
            mensaje += `<li class="mb-1">• ${error}</li>`;
        });
        mensaje += '</ul>';

        Swal.fire({
            title: 'Stock Insuficiente',
            html: mensaje,
            icon: 'warning',
            confirmButtonText: 'Entendido',
            confirmButtonColor: '#0088ff',
            showCancelButton: true,
            cancelButtonText: 'Ver Carrito',
            cancelButtonColor: '#6c757d'
        }).then((result) => {
            if (result.dismiss === Swal.DismissReason.cancel) {
                window.location.href = '<?php echo e(route("landing")); ?>';
            }
        });
    }

    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        // Validar dirección seleccionada
        const direccionSeleccionada = form.querySelector('input[name="direccion_id"]:checked');
        if (!direccionSeleccionada) {
            Swal.fire({
                title: 'Error',
                text: 'Por favor, selecciona una dirección de envío',
                icon: 'error',
                confirmButtonText: 'Entendido',
                confirmButtonColor: '#0088ff'
            });
            return;
        }

        // Validar método de pago seleccionado
        const metodoPagoSeleccionado = form.querySelector('input[name="metodo_pago_id"]:checked');
        if (!metodoPagoSeleccionado) {
            Swal.fire({
                title: 'Error',
                text: 'Por favor, selecciona un método de pago',
                icon: 'error',
                confirmButtonText: 'Entendido',
                confirmButtonColor: '#0088ff'
            });
            return;
        }

        // Mostrar loading
        Swal.fire({
            title: 'Verificando stock...',
            text: 'Por favor espera mientras verificamos la disponibilidad de los productos',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Verificar stock antes de procesar
        const stockData = await verificarStock();
        
        if (!stockData.success) {
            Swal.close();
            mostrarAlertaStock(stockData.errores);
            return;
        }

        // Si todo está bien, continuar con el proceso
        Swal.close();
        
        // Deshabilitar el botón y mostrar spinner
        submitButton.disabled = true;
        spinner.classList.remove('hidden');

        // Enviar el formulario
        form.submit();
    });

    // Habilitar/deshabilitar botón según selecciones
    function validarFormulario() {
        const direccionSeleccionada = form.querySelector('input[name="direccion_id"]:checked');
        const metodoPagoSeleccionado = form.querySelector('input[name="metodo_pago_id"]:checked');
        submitButton.disabled = !direccionSeleccionada || !metodoPagoSeleccionado;
    }

    // Agregar listeners para los radio buttons
    form.querySelectorAll('input[type="radio"]').forEach(radio => {
        radio.addEventListener('change', validarFormulario);
    });

    // Validar estado inicial
    validarFormulario();

    // Verificar stock al cargar la página
    verificarStock().then(data => {
        if (!data.success) {
            mostrarAlertaStock(data.errores);
        }
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.landing', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Proyecto V11.3\4GMovil\resources\views/checkout/index.blade.php ENDPATH**/ ?>