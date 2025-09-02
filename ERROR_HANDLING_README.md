# 🚨 Sistema de Manejo de Errores Mejorado

Este sistema proporciona un manejo de errores más específico y detallado para la aplicación 4GMovil.

## ✨ Características Principales

### 1. **Middleware de Errores de Base de Datos**
- **Archivo**: `app/Http/Middleware/DatabaseErrorHandler.php`
- **Función**: Captura errores de base de datos y proporciona información específica
- **Tipos de errores detectados**:
  - Columnas no encontradas
  - Tablas no encontradas
  - Errores de claves foráneas
  - Errores de sintaxis SQL
  - Errores de conexión

### 2. **Middleware de Errores Generales**
- **Archivo**: `app/Http/Middleware/DetailedErrorHandler.php`
- **Función**: Maneja errores de validación, autenticación y generales
- **Tipos de errores**:
  - Errores de validación
  - Errores de autenticación
  - Modelos no encontrados
  - Rutas no encontradas
  - Métodos HTTP no permitidos

### 3. **Trait para Controladores**
- **Archivo**: `app/Traits/ErrorHandler.php`
- **Función**: Proporciona métodos para manejar errores en controladores
- **Métodos disponibles**:
  - `handleDatabaseError()`
  - `handleValidationError()`
  - `handleModelNotFoundError()`
  - `handleGenericError()`

### 4. **Comando Artisan para Errores**
- **Comando**: `php artisan errors:show`
- **Función**: Muestra errores del sistema de manera organizada y legible

## 🚀 Uso del Sistema

### Comando de Errores

```bash
# Mostrar todos los errores
php artisan errors:show

# Mostrar solo errores de base de datos
php artisan errors:show --type=database

# Mostrar solo errores de validación
php artisan errors:show --type=validation

# Mostrar solo errores de autenticación
php artisan errors:show --type=auth

# Limitar número de errores mostrados
php artisan errors:show --limit=20

# Buscar errores específicos
php artisan errors:show --search="Column not found"

# Mostrar errores de una fecha específica
php artisan errors:show --date=2025-09-02

# Revisar un archivo de log específico
php artisan errors:show --file=/path/to/custom.log
```

### Uso en Controladores

```php
<?php

namespace App\Http\Controllers;

use App\Traits\ErrorHandler;
use Illuminate\Http\Request;

class MiController extends Controller
{
    use ErrorHandler;

    public function miMetodo(Request $request)
    {
        try {
            // Tu lógica aquí
            $resultado = MiModelo::create($request->all());
            
            return response()->json(['success' => true, 'data' => $resultado]);
            
        } catch (\Illuminate\Database\QueryException $e) {
            return $this->handleDatabaseError($e, [
                'context' => 'Creando nuevo registro',
                'user_id' => auth()->id()
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->handleValidationError($e, [
                'context' => 'Validando datos del formulario'
            ]);
            
        } catch (\Exception $e) {
            return $this->handleGenericError($e, [
                'context' => 'Operación general'
            ]);
        }
    }
}
```

### Configuración

El archivo `config/errors.php` permite configurar:

- **Logging**: Habilitar/deshabilitar logging de errores
- **Notificaciones**: Configurar notificaciones por email/Slack
- **Visualización**: Controlar qué detalles mostrar a los usuarios
- **Mensajes**: Personalizar mensajes de error para usuarios
- **Sugerencias**: Proporcionar acciones recomendadas para cada tipo de error

## 📊 Tipos de Errores Detectados

### Errores de Base de Datos
- **Missing Column**: Columna no encontrada
- **Missing Table**: Tabla no encontrada
- **Foreign Key Constraint**: Error de clave foránea
- **SQL Syntax**: Error de sintaxis SQL
- **Connection Error**: Error de conexión

### Errores de Validación
- **Required Fields**: Campos obligatorios
- **Email Format**: Formato de email inválido
- **Min/Max Length**: Longitud de campo inválida
- **Unique Values**: Valores duplicados
- **Numeric Values**: Valores no numéricos

### Errores de Autenticación
- **Unauthenticated**: Usuario no autenticado
- **Unauthorized**: Usuario sin permisos
- **Invalid Credentials**: Credenciales inválidas
- **Account Locked**: Cuenta bloqueada
- **Session Expired**: Sesión expirada

## 🔧 Instalación y Configuración

### 1. Registrar Middleware

Agregar en `app/Http/Kernel.php`:

```php
protected $middleware = [
    // ... otros middleware
    \App\Http\Middleware\DatabaseErrorHandler::class,
    \App\Http\Middleware\DetailedErrorHandler::class,
];
```

### 2. Configurar Variables de Entorno

```env
# Habilitar logging detallado de errores
ERROR_LOGGING_ENABLED=true
ERROR_LOG_LEVEL=error

# Habilitar notificaciones
ERROR_NOTIFICATIONS_ENABLED=true
ERROR_NOTIFY_EMAIL=true
ERROR_NOTIFY_EMAIL_RECIPIENTS=admin@example.com

# Configurar visualización
ERROR_SHOW_SUGGESTIONS=true
ERROR_USER_FRIENDLY_MESSAGES=true
```

### 3. Usar en Controladores

```php
use App\Traits\ErrorHandler;

class TuController extends Controller
{
    use ErrorHandler;
    
    // ... tus métodos
}
```

## 📈 Beneficios

1. **Errores Más Específicos**: Los usuarios reciben mensajes claros sobre qué salió mal
2. **Sugerencias de Acción**: Cada error incluye sugerencias para resolverlo
3. **Logging Detallado**: Información completa para debugging
4. **Respuestas Adaptativas**: Diferentes respuestas para AJAX y peticiones normales
5. **Monitoreo Centralizado**: Comando Artisan para revisar errores del sistema
6. **Configuración Flexible**: Personalizable según necesidades del proyecto

## 🐛 Debugging

### Ver Errores en Tiempo Real

```bash
# Seguir logs en tiempo real
tail -f storage/logs/laravel.log

# Usar el comando de errores
php artisan errors:show --type=database --limit=5
```

### Logs Estructurados

Los errores se loguean con contexto completo:
- URL de la petición
- Método HTTP
- ID del usuario
- IP del usuario
- User Agent
- Detalles del error
- Stack trace (si está habilitado)

## 🔮 Próximas Mejoras

- [ ] Dashboard web para visualizar errores
- [ ] Notificaciones automáticas por Slack/Email
- [ ] Métricas de errores en tiempo real
- [ ] Integración con servicios de monitoreo externos
- [ ] Sistema de alertas inteligentes
- [ ] Análisis predictivo de errores

---

**Nota**: Este sistema está diseñado para mejorar la experiencia de debugging y proporcionar información útil tanto a desarrolladores como a usuarios finales.
