<?php $__env->startSection('title', 'Pago con Stripe - 4GMovil'); ?>
<?php $__env->startSection('meta-description', 'Procesa tu pago de forma segura con Stripe'); ?>

<?php $__env->startPush('head'); ?>
<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="bg-gray-100 min-h-screen py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow-xl rounded-lg overflow-hidden">
            <!-- Encabezado -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-800 px-6 py-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-white">Pago Seguro</h1>
                        <p class="text-blue-200 mt-2">Procesa tu pedido de forma segura con Stripe</p>
                    </div>
                    <div class="text-right">
                        <p class="text-blue-200 text-sm">Pedido #<?php echo e($pedido->pedido_id); ?></p>
                        <p class="text-white font-bold text-xl">$<?php echo e(number_format($pedido->total, 0, ',', '.')); ?></p>
                    </div>
                </div>
            </div>

            <!-- Contenido del pago -->
            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Formulario de pago -->
                    <div>
                        <h2 class="text-xl font-semibold mb-6">Información de Pago</h2>
                        
                        <!-- Mensajes de error/éxito -->
                        <div id="payment-messages" class="hidden mb-4 p-4 rounded-md">
                            <div id="payment-message" class="text-sm"></div>
                        </div>

                        <!-- Formulario de Stripe -->
                        <form id="payment-form">
                            <?php echo csrf_field(); ?>
                            <div class="space-y-4">
                                <!-- Elemento de tarjeta de Stripe -->
                                <div>
                                    <label for="card-element" class="block text-sm font-medium text-gray-700 mb-2">
                                        Información de la Tarjeta
                                    </label>
                                    <div id="card-element" class="p-3 border border-gray-300 rounded-md bg-white">
                                        <!-- Stripe Elements se insertará aquí -->
                                    </div>
                                    <div id="card-errors" class="mt-2 text-sm text-red-600" role="alert"></div>
                                </div>

                                <!-- Botón de pago -->
                                <button type="submit" id="submit-button" 
                                        class="w-full bg-blue-600 text-white py-3 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <span id="button-text">Pagar $<?php echo e(number_format($pedido->total, 0, ',', '.')); ?></span>
                                    <div id="spinner" class="hidden">
                                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </div>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Resumen del pedido -->
                    <div>
                        <h2 class="text-xl font-semibold mb-6">Resumen del Pedido</h2>
                        <div class="bg-gray-50 rounded-lg p-6">
                            <!-- Productos -->
                            <div class="space-y-4 mb-6">
                                <?php $__currentLoopData = $pedido->detalles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detalle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="flex items-center space-x-4">
                                    <div class="flex-shrink-0">
                                        <?php if($detalle->producto->imagenes->first()): ?>
                                            <img src="<?php echo e(asset('storage/' . $detalle->producto->imagenes->first()->ruta_imagen)); ?>" 
                                                 alt="<?php echo e($detalle->producto->nombre_producto); ?>" 
                                                 class="w-16 h-16 object-cover rounded-md">
                                        <?php else: ?>
                                            <div class="w-16 h-16 bg-gray-200 rounded-md flex items-center justify-center">
                                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="text-sm font-medium text-gray-900"><?php echo e($detalle->producto->nombre_producto); ?></h3>
                                        <p class="text-sm text-gray-500">Cantidad: <?php echo e($detalle->cantidad); ?></p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-medium text-gray-900">$<?php echo e(number_format($detalle->precio_unitario, 0, ',', '.')); ?></p>
                                    </div>
                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>

                            <!-- Totales -->
                            <div class="border-t border-gray-200 pt-4 space-y-2">
                                <div class="flex justify-between text-lg font-semibold">
                                    <span class="text-gray-900">Total:</span>
                                    <span class="text-gray-900">$<?php echo e(number_format($pedido->total, 0, ',', '.')); ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- Información de seguridad -->
                        <div class="mt-6 bg-blue-50 rounded-lg p-4">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                                <p class="text-sm text-blue-800">
                                    Tu información de pago está protegida con encriptación SSL de 256 bits
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts de Stripe -->
<script src="https://js.stripe.com/v3/"></script>
<script>
// Configuración de Stripe
const stripe = Stripe('<?php echo e(config("services.stripe.key")); ?>');
const elements = stripe.elements();

// Crear elemento de tarjeta
const cardElement = elements.create('card', {
    style: {
        base: {
            fontSize: '16px',
            color: '#424770',
            '::placeholder': {
                color: '#aab7c4',
            },
        },
        invalid: {
            color: '#9e2146',
        },
    },
});

// Montar el elemento de tarjeta
cardElement.mount('#card-element');

// Manejar errores de validación en tiempo real
cardElement.addEventListener('change', ({error}) => {
    const displayError = document.getElementById('card-errors');
    if (error) {
        displayError.textContent = error.message;
    } else {
        displayError.textContent = '';
    }
});

// Manejar el envío del formulario
const form = document.getElementById('payment-form');
const submitButton = document.getElementById('submit-button');
const buttonText = document.getElementById('button-text');
const spinner = document.getElementById('spinner');

form.addEventListener('submit', async (event) => {
    event.preventDefault();
    
    // Deshabilitar el botón
    submitButton.disabled = true;
    buttonText.classList.add('hidden');
    spinner.classList.remove('hidden');
    
    // Crear PaymentIntent
    try {
        const response = await fetch('<?php echo e(route("stripe.create-payment-intent")); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
            },
            body: JSON.stringify({
                pedido_id: <?php echo e($pedido->pedido_id); ?>,
                amount: <?php echo e($pedido->total * 100); ?> // Convertir a centavos
            })
        });
        
        const data = await response.json();
        
        if (data.error) {
            throw new Error(data.error);
        }
        
        // Confirmar el pago
        const {error, paymentIntent} = await stripe.confirmCardPayment(data.clientSecret, {
            payment_method: {
                card: cardElement,
                billing_details: {
                    name: '<?php echo e(Auth::user()->nombre_usuario); ?>',
                    email: '<?php echo e(Auth::user()->correo_electronico); ?>'
                }
            }
        });
        
        if (error) {
            throw new Error(error.message);
        }
        
        // Confirmar en el servidor
        const confirmResponse = await fetch('<?php echo e(route("stripe.confirm-payment")); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
            },
            body: JSON.stringify({
                payment_intent_id: paymentIntent.id,
                pedido_id: <?php echo e($pedido->pedido_id); ?>

            })
        });
        
        const confirmData = await confirmResponse.json();
        
        if (confirmData.success) {
            // Redirigir a página de éxito
            window.location.href = '<?php echo e(route("checkout.success", $pedido)); ?>';
        } else {
            throw new Error(confirmData.error || 'Error al confirmar el pago');
        }
        
    } catch (error) {
        // Mostrar error
        const messageContainer = document.getElementById('payment-messages');
        const messageElement = document.getElementById('payment-message');
        
        messageElement.textContent = error.message;
        messageContainer.classList.remove('hidden');
        messageContainer.classList.add('bg-red-50', 'text-red-800');
        
        // Rehabilitar el botón
        submitButton.disabled = false;
        buttonText.classList.remove('hidden');
        spinner.classList.add('hidden');
    }
});
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.landing', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\osman\OneDrive\Documentos\GitHub\4GMovil\resources\views\checkout\stripe-payment.blade.php ENDPATH**/ ?>