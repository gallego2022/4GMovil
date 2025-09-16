@extends('layouts.error')

@section('title', 'Sesión Expirada - 419')

@section('content')
    <!-- Código de error -->
    <div class="error-code">419</div>
    
    <!-- Icono -->
    <div class="error-icon">
        <i class="fas fa-clock"></i>
    </div>
    
    <!-- Título -->
    <h1 class="error-title">¡Sesión Expirada!</h1>
    
    <!-- Descripción -->
    <p class="error-description">
        Tu sesión ha expirado por inactividad. 
        Por seguridad, necesitas iniciar sesión nuevamente.
    </p>
    
    <!-- Botones de acción -->
    <div class="error-actions">
        <a href="{{ route('login') }}" class="error-btn error-btn-primary">
            <i class="fas fa-sign-in-alt"></i>
            Iniciar Sesión
        </a>
        <a href="{{ route('landing') }}" class="error-btn error-btn-secondary">
            <i class="fas fa-home"></i>
            Ir al Inicio
        </a>
    </div>
    
    <!-- Consejos de seguridad -->
    <div class="error-info">
        <h4>Consejos de Seguridad</h4>
        <div style="text-align: left; margin-top: 1rem;">
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem; color: var(--text-primary);">
                <i class="fas fa-check" style="color: #10b981;"></i>
                <span>Cierra sesión cuando termines de usar la aplicación</span>
            </div>
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem; color: var(--text-primary);">
                <i class="fas fa-check" style="color: #10b981;"></i>
                <span>No compartas tu sesión con otros</span>
            </div>
            <div style="display: flex; align-items: center; gap: 0.5rem; color: var(--text-primary);">
                <i class="fas fa-check" style="color: #10b981;"></i>
                <span>Usa contraseñas seguras y únicas</span>
            </div>
        </div>
    </div>
    
    <!-- Servicios de seguridad -->
    <div class="error-services">
        <div class="error-service-card">
            <div class="error-service-icon" style="color: #3b82f6;">
                <i class="fas fa-shield-alt"></i>
            </div>
            <h3 class="error-service-title">Protección</h3>
            <p class="error-service-description">Tu cuenta está protegida con autenticación segura</p>
        </div>
        <div class="error-service-card">
            <div class="error-service-icon" style="color: #10b981;">
                <i class="fas fa-lock"></i>
            </div>
            <h3 class="error-service-title">Privacidad</h3>
            <p class="error-service-description">Tus datos están seguros con nosotros</p>
        </div>
    </div>
@endsection