<?php $__env->startSection('title', 'Acceso Denegado - 403'); ?>

<?php $__env->startSection('content'); ?>
    <!-- Código de error -->
    <div class="error-code">403</div>
    
    <!-- Icono -->
    <div class="error-icon">
        <i class="fas fa-shield-alt"></i>
    </div>
    
    <!-- Título -->
    <h1 class="error-title">¡Acceso Denegado!</h1>
    
    <!-- Descripción -->
    <p class="error-description">
        No tienes permisos para acceder a esta página. 
        Si crees que esto es un error, contacta al administrador.
    </p>
    
    <!-- Botones de acción -->
    <div class="error-actions">
        <a href="<?php echo e(route('landing')); ?>" class="error-btn error-btn-primary">
            <i class="fas fa-home"></i>
            Ir al Inicio
        </a>
        <a href="<?php echo e(route('login')); ?>" class="error-btn error-btn-secondary">
            <i class="fas fa-sign-in-alt"></i>
            Iniciar Sesión
        </a>
    </div>
    
    <!-- Información de contacto -->
    <div class="error-info">
        <h4>¿Necesitas ayuda?</h4>
        <p>
            Si crees que deberías tener acceso a esta página, 
            contacta con nuestro equipo de soporte.
        </p>
        <div style="margin-top: 1rem; display: flex; flex-direction: column; gap: 0.5rem; align-items: center;">
            <div style="color: rgba(255, 255, 255, 0.9); display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-envelope"></i> 
                <span>soporte@4gmovil.com</span>
            </div>
            <div style="color: rgba(255, 255, 255, 0.9); display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-phone"></i> 
                <span>+57 300 123 4567</span>
            </div>
        </div>
    </div>
    
    <!-- Servicios de ayuda -->
    <div class="error-services">
        <div class="error-service-card">
            <div class="error-service-icon">
                <i class="fas fa-user-shield" style="color: #ef4444;"></i>
            </div>
            <h3 class="error-service-title">Seguridad</h3>
            <p class="error-service-description">Tu cuenta está protegida por nuestros sistemas de seguridad</p>
        </div>
        <div class="error-service-card">
            <div class="error-service-icon">
                <i class="fas fa-key" style="color: #f59e0b;"></i>
            </div>
            <h3 class="error-service-title">Permisos</h3>
            <p class="error-service-description">Verifica que tengas los permisos necesarios</p>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.error', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\osman\OneDrive\Documentos\GitHub\4GMovil\resources\views\errors\403.blade.php ENDPATH**/ ?>