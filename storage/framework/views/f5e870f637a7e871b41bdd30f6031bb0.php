<?php $__env->startSection('title', 'Página no encontrada - 404'); ?>

<?php $__env->startSection('content'); ?>
    <!-- Código de error -->
    <div class="error-code">404</div>
    
    <!-- Icono -->
    <div class="error-icon">
        <i class="fas fa-search"></i>
    </div>
    
    <!-- Título -->
    <h1 class="error-title">¡Oops! Página no encontrada</h1>
    
    <!-- Descripción -->
    <p class="error-description">
        La página que buscas parece haber desaparecido en el ciberespacio. 
        No te preocupes, nuestros robots están trabajando para encontrarla.
    </p>
    
    <!-- Botones de acción -->
    <div class="error-actions">
        <a href="<?php echo e(route('landing')); ?>" class="error-btn error-btn-primary">
            <i class="fas fa-home"></i>
            Ir al Inicio
        </a>
        <a href="<?php echo e(route('productos.lista')); ?>" class="error-btn error-btn-secondary">
            <i class="fas fa-shopping-bag"></i>
            Ver Catálogo
        </a>
    </div>
    
    <!-- Barra de búsqueda -->
    <div class="error-info">
        <h4>¿Qué estabas buscando?</h4>
        <form action="<?php echo e(route('productos.lista')); ?>" method="GET" class="error-search-form">
            <input type="text" 
                   name="search" 
                   placeholder="Buscar productos, servicios..."
                   class="error-search-input">
            <button type="submit" class="error-search-btn">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>
    
    <!-- Servicios destacados -->
    <div class="error-services">
        <div class="error-service-card">
            <div class="error-service-icon">
                <i class="fas fa-mobile-alt" style="color: #6366f1;"></i>
            </div>
            <h3 class="error-service-title">Productos</h3>
            <p class="error-service-description">Encuentra los mejores dispositivos móviles</p>
        </div>
        <div class="error-service-card">
            <div class="error-service-icon">
                <i class="fas fa-headset" style="color: #10b981;"></i>
            </div>
            <h3 class="error-service-title">Soporte</h3>
            <p class="error-service-description">Estamos aquí para ayudarte 24/7</p>
        </div>
        
    </div>
    
    
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.error', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\osman\OneDrive\Documentos\GitHub\4GMovil\resources\views/errors/404.blade.php ENDPATH**/ ?>