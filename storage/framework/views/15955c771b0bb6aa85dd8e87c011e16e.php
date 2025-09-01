

<?php $__env->startSection('title', 'Error del Servidor - 500'); ?>

<?php $__env->startSection('content'); ?>
    <!-- Código de error -->
    <div class="error-code">500</div>
    
    <!-- Icono -->
    <div class="error-icon">
        <i class="fas fa-server"></i>
    </div>
    
    <!-- Título -->
    <h1 class="error-title">¡Error del Servidor!</h1>
    
    <!-- Descripción -->
    <p class="error-description">
        Algo salió mal en nuestro servidor. 
        Nuestro equipo técnico ya está trabajando para solucionarlo.
    </p>
    
    <!-- Botones de acción -->
    <div class="error-actions">
        <button onclick="window.location.reload()" class="error-btn error-btn-primary">
            <i class="fas fa-redo"></i>
            Intentar Nuevamente
        </button>
        <a href="<?php echo e(route('landing')); ?>" class="error-btn error-btn-secondary">
            <i class="fas fa-home"></i>
            Ir al Inicio
        </a>
    </div>
    
    <!-- Estado del servicio -->
    <div class="error-info">
        <h4>Estado del Servicio</h4>
        <div style="display: flex; align-items: center; justify-content: center; gap: 0.5rem; margin: 1rem 0;">
            <div style="width: 12px; height: 12px; background: #10b981; border-radius: 50%; animation: pulse 2s infinite;"></div>
            <span style="color: #10b981; font-weight: 600;">Servicios principales operativos</span>
        </div>
        <p>
            Si el problema persiste, puedes verificar el estado de nuestros servicios 
            o contactar soporte técnico.
        </p>
        <div style="margin-top: 1rem; color: rgba(255, 255, 255, 0.9); font-size: 0.9rem;">
            <i class="fas fa-clock"></i> Tiempo estimado de resolución: 15-30 minutos
        </div>
    </div>
    
    <!-- Servicios de estado -->
    <div class="error-services">
        <div class="error-service-card">
            <div class="error-service-icon">
                <i class="fas fa-tools" style="color: #f59e0b;"></i>
            </div>
            <h3 class="error-service-title">Mantenimiento</h3>
            <p class="error-service-description">Nuestro equipo está trabajando en una solución</p>
        </div>
        <div class="error-service-card">
            <div class="error-service-icon">
                <i class="fas fa-chart-line" style="color: #10b981;"></i>
            </div>
            <h3 class="error-service-title">Monitoreo</h3>
            <p class="error-service-description">Supervisamos constantemente nuestros sistemas</p>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<style>
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
</style>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.error', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Proyecto V11.3\4GMovil\resources\views/errors/500.blade.php ENDPATH**/ ?>