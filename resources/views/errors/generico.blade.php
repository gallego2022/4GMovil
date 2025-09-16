@extends('layouts.error')

@section('title', 'Error - ' . ($exception->getStatusCode() ?? 'Desconocido')) 
@section('content')
    <!-- Código de error -->
    <div class="error-code">{{ $exception->getStatusCode() ?? '???' }}</div>
    
    <!-- Icono -->
    <div class="error-icon">
        <i class="fas fa-exclamation-triangle"></i>
    </div>
    
    <!-- Título -->
    <h1 class="error-title">¡Algo salió mal!</h1>
    
    <!-- Descripción -->
    <p class="error-description">
        {{ $exception->getMessage() ?? 'Ha ocurrido un error inesperado. Por favor, intenta nuevamente.' }}
    </p>
    
    <!-- Botones de acción -->
    <div class="error-actions">
        <a href="{{ route('landing') }}" class="error-btn error-btn-primary">
            <i class="fas fa-home"></i>
            Ir al Inicio
        </a>
        <button onclick="window.history.back()" class="error-btn error-btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Volver Atrás
        </button>
    </div>
    
    <!-- Información de contacto -->
    <div class="error-info">
        <h4>¿Necesitas ayuda?</h4>
        <p>
            Si el problema persiste, contacta con nuestro equipo de soporte.
        </p>
        <div style="margin-top: 1rem; display: flex; flex-direction: column; gap: 0.5rem; align-items: center;">
            <div style="display: flex; align-items: center; gap: 0.5rem; color: var(--text-primary);">
                <i class="fas fa-envelope"></i> 
                <span>soporte@4gmovil.com</span>
            </div>
            <div style="display: flex; align-items: center; gap: 0.5rem; color: var(--text-primary);">
                <i class="fas fa-phone"></i> 
                <span>+57 300 123 4567</span>
            </div>
        </div>
    </div>
    
    <!-- Información técnica (solo en desarrollo) -->
    @if(app()->environment('local') && isset($exception))
        <div class="error-info" style="text-align: left; font-family: monospace; font-size: 0.8rem;">
            <h4>Información Técnica</h4>
            <div style="background: rgba(0, 0, 0, 0.06); padding: 1rem; border-radius: 8px; margin-top: 0.5rem; border: 1px solid var(--card-border); color: var(--text-secondary);">
                <p><strong>Archivo:</strong> {{ $exception->getFile() ?? 'N/A' }}</p>
                <p><strong>Línea:</strong> {{ $exception->getLine() ?? 'N/A' }}</p>
                <p><strong>Código:</strong> {{ $exception->getCode() ?? 'N/A' }}</p>
            </div>
        </div>
    @endif
    
    <!-- Servicios de ayuda -->
    <div class="error-services">
        <div class="error-service-card">
            <div class="error-service-icon" style="color: #ef4444;">
                <i class="fas fa-life-ring"></i>
            </div>
            <h3 class="error-service-title">Soporte</h3>
            <p class="error-service-description">Nuestro equipo está disponible para ayudarte</p>
        </div>
        <div class="error-service-card">
            <div class="error-service-icon" style="color: #f59e0b;">
                <i class="fas fa-bug"></i>
            </div>
            <h3 class="error-service-title">Reportar</h3>
            <p class="error-service-description">Ayúdanos a mejorar reportando este error</p>
        </div>
    </div>
@endsection