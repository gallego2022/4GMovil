@extends('layouts.error')

@section('title', 'Demasiadas Solicitudes - 429')

@section('content')
    <!-- Código de error -->
    <div class="error-code">429</div>
    
    <!-- Icono -->
    <div class="error-icon">
        <i class="fas fa-tachometer-alt"></i>
    </div>
    
    <!-- Título -->
    <h1 class="error-title">¡Demasiadas Solicitudes!</h1>
    
    <!-- Descripción -->
    <p class="error-description">
        Has realizado demasiadas solicitudes en poco tiempo. 
        Por favor, espera un momento antes de intentar nuevamente.
    </p>
    
    <!-- Contador de tiempo -->
    <div class="error-info">
        <h4>Tiempo de Espera</h4>
        <div style="margin: 1rem 0; text-align: center;">
            <div id="countdown" style="font-size: 3rem; font-weight: 800; color: var(--accent-via); margin-bottom: 0.5rem;">60</div>
            <p style="color: var(--text-secondary); font-size: 0.9rem;">segundos restantes</p>
        </div>
    </div>
    
    <!-- Botones de acción -->
    <div class="error-actions">
        <button onclick="window.location.reload()" class="error-btn error-btn-primary" id="retry-btn">
            <i class="fas fa-redo"></i>
            Intentar Nuevamente
        </button>
        <a href="{{ route('landing') }}" class="error-btn error-btn-secondary">
            <i class="fas fa-home"></i>
            Ir al Inicio
        </a>
    </div>
    
    <!-- Información adicional -->
    <div class="error-info">
        <h4>¿Por qué sucede esto?</h4>
        <p>
            Este límite protege nuestros servidores de uso excesivo 
            y asegura una experiencia óptima para todos los usuarios.
        </p>
    </div>
    
    <!-- Servicios de límite -->
    <div class="error-services">
        <div class="error-service-card">
            <div class="error-service-icon" style="color: #8b5cf6;">
                <i class="fas fa-gauge-high"></i>
            </div>
            <h3 class="error-service-title">Límites</h3>
            <p class="error-service-description">Controlamos el tráfico para mantener la calidad</p>
        </div>
        <div class="error-service-card">
            <div class="error-service-icon" style="color: #06b6d4;">
                <i class="fas fa-users"></i>
            </div>
            <h3 class="error-service-title">Usuarios</h3>
            <p class="error-service-description">Garantizamos acceso equitativo para todos</p>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Contador regresivo
    let timeLeft = 60;
    const countdownElement = document.getElementById('countdown');
    const retryBtn = document.getElementById('retry-btn');
    
    // Deshabilitar botón inicialmente
    retryBtn.style.opacity = '0.6';
    retryBtn.style.pointerEvents = 'none';
    
    const countdown = setInterval(() => {
        timeLeft--;
        countdownElement.textContent = timeLeft;
        
        if (timeLeft <= 0) {
            clearInterval(countdown);
            countdownElement.textContent = '0';
            countdownElement.parentElement.querySelector('p').textContent = '¡Ya puedes intentar nuevamente!';
            
            // Habilitar botón
            retryBtn.style.opacity = '1';
            retryBtn.style.pointerEvents = 'auto';
            retryBtn.innerHTML = '<i class="fas fa-check"></i> Listo para Intentar';
        }
    }, 1000);
});
</script>
@endpush