# 🛠️ HERRAMIENTAS Y SCRIPTS DE REFACTORING

## 📋 ÍNDICE
1. [Herramientas de Desarrollo](#herramientas-de-desarrollo)
2. [Scripts de Automatización](#scripts-de-automatización)
3. [Comandos Útiles](#comandos-útiles)
4. [Configuraciones](#configuraciones)
5. [Testing Tools](#testing-tools)
6. [Performance Tools](#performance-tools)

---

## 🔧 HERRAMIENTAS DE DESARROLLO

### **Análisis de Código**
```bash
# PHPStan - Análisis estático
composer require --dev phpstan/phpstan
./vendor/bin/phpstan analyse app --level=8

# PHP CS Fixer - Formateo de código
composer require --dev friendsofphp/php-cs-fixer
./vendor/bin/php-cs-fixer fix app/

# PHP Mess Detector - Detección de problemas
composer require --dev phpmd/phpmd
./vendor/bin/phpmd app text cleancode,codesize,controversial,design,naming,unusedcode
```

### **Testing**
```bash
# PHPUnit - Testing framework
./vendor/bin/phpunit --coverage-html coverage/

# Pest - Testing alternativo
composer require --dev pestphp/pest --dev
./vendor/bin/pest --coverage

# Laravel Dusk - Testing de navegador
composer require --dev laravel/dusk
php artisan dusk
```

### **Debugging**
```bash
# Laravel Telescope - Debugging
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate

# Laravel Debugbar
composer require barryvdh/laravel-debugbar --dev

# Xdebug - Profiling
# Configurar en php.ini
```

---

## 🤖 SCRIPTS DE AUTOMATIZACIÓN

### **Script 1: Backup Automático**
```bash
#!/bin/bash
# backup-project.sh

DATE=$(date +%Y%m%d_%H%M%S)
PROJECT_NAME="4GMovil"
BACKUP_DIR="/backups/refactoring"

echo "🔄 Creando backup del proyecto..."

# Crear directorio de backup
mkdir -p $BACKUP_DIR

# Backup de archivos del proyecto
tar -czf $BACKUP_DIR/${PROJECT_NAME}_${DATE}.tar.gz \
    --exclude='vendor' \
    --exclude='node_modules' \
    --exclude='storage/logs' \
    --exclude='storage/framework/cache' \
    .

# Backup de base de datos
php artisan db:backup --destination=local --destinationPath=$BACKUP_DIR/db_${DATE}.sql

echo "✅ Backup completado: ${PROJECT_NAME}_${DATE}.tar.gz"
echo "✅ Base de datos: db_${DATE}.sql"
```

### **Script 2: Análisis de Código**
```bash
#!/bin/bash
# analyze-code.sh

echo "🔍 Iniciando análisis de código..."

# Crear directorio de reportes
mkdir -p reports

# PHPStan
echo "📊 Ejecutando PHPStan..."
./vendor/bin/phpstan analyse app --level=8 --output-format=json > reports/phpstan.json

# PHP CS Fixer
echo "🎨 Verificando estándares de código..."
./vendor/bin/php-cs-fixer fix --dry-run --diff > reports/cs-fixer.diff

# PHPMD
echo "🚨 Detectando problemas de código..."
./vendor/bin/phpmd app text cleancode,codesize,controversial,design,naming,unusedcode > reports/phpmd.txt

# Contar líneas de código
echo "📈 Contando líneas de código..."
find app -name "*.php" -exec wc -l {} + | tail -1 > reports/lines.txt

echo "✅ Análisis completado. Revisar carpeta 'reports'"
```

### **Script 3: Testing Automático**
```bash
#!/bin/bash
# run-tests.sh

echo "🧪 Ejecutando tests..."

# Limpiar caché
php artisan config:clear
php artisan cache:clear

# Ejecutar tests unitarios
echo "📋 Tests unitarios..."
./vendor/bin/phpunit --testsuite=Unit --coverage-text > reports/unit-tests.txt

# Ejecutar tests de integración
echo "🔗 Tests de integración..."
./vendor/bin/phpunit --testsuite=Feature --coverage-text > reports/integration-tests.txt

# Generar reporte de cobertura
echo "📊 Generando reporte de cobertura..."
./vendor/bin/phpunit --coverage-html reports/coverage

# Verificar cobertura mínima
COVERAGE=$(./vendor/bin/phpunit --coverage-text | grep "Lines:" | awk '{print $2}' | sed 's/%//')
if (( $(echo "$COVERAGE >= 80" | bc -l) )); then
    echo "✅ Cobertura de tests: ${COVERAGE}% (OK)"
else
    echo "⚠️  Cobertura de tests: ${COVERAGE}% (BAJA)"
fi

echo "✅ Tests completados"
```

### **Script 4: Refactoring Helper**
```bash
#!/bin/bash
# refactor-helper.sh

CONTROLLER_NAME=$1
SERVICE_NAME=$2

if [ -z "$CONTROLLER_NAME" ] || [ -z "$SERVICE_NAME" ]; then
    echo "❌ Uso: ./refactor-helper.sh <ControllerName> <ServiceName>"
    echo "Ejemplo: ./refactor-helper.sh CheckoutController CheckoutService"
    exit 1
fi

echo "🔄 Iniciando refactoring de $CONTROLLER_NAME..."

# Crear estructura de directorios
mkdir -p app/Services
mkdir -p app/Repositories/Contracts
mkdir -p app/Repositories/Eloquent

# Crear service
echo "📝 Creando $SERVICE_NAME..."
cat > app/Services/$SERVICE_NAME.php << EOF
<?php

namespace App\Services;

class $SERVICE_NAME
{
    public function __construct()
    {
        //
    }

    // TODO: Implementar métodos del service
}
EOF

# Crear interface del repository
echo "📝 Creando interface del repository..."
cat > app/Repositories/Contracts/${SERVICE_NAME}RepositoryInterface.php << EOF
<?php

namespace App\Repositories\Contracts;

interface ${SERVICE_NAME}RepositoryInterface
{
    // TODO: Definir métodos del repository
}
EOF

# Crear repository
echo "📝 Creando repository..."
cat > app/Repositories/Eloquent/${SERVICE_NAME}Repository.php << EOF
<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\${SERVICE_NAME}RepositoryInterface;

class ${SERVICE_NAME}Repository implements ${SERVICE_NAME}RepositoryInterface
{
    // TODO: Implementar métodos del repository
}
EOF

# Crear test del service
echo "📝 Creando test del service..."
cat > tests/Unit/Services/${SERVICE_NAME}Test.php << EOF
<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\\$SERVICE_NAME;

class ${SERVICE_NAME}Test extends TestCase
{
    protected \$service;

    protected function setUp(): void
    {
        parent::setUp();
        \$this->service = new $SERVICE_NAME();
    }

    // TODO: Implementar tests
}
EOF

echo "✅ Refactoring helper completado para $CONTROLLER_NAME"
echo "📁 Archivos creados:"
echo "   - app/Services/$SERVICE_NAME.php"
echo "   - app/Repositories/Contracts/${SERVICE_NAME}RepositoryInterface.php"
echo "   - app/Repositories/Eloquent/${SERVICE_NAME}Repository.php"
echo "   - tests/Unit/Services/${SERVICE_NAME}Test.php"
```

---

## ⚡ COMANDOS ÚTILES

### **Laravel Artisan Commands**
```bash
# Limpiar caché
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Optimizar
php artisan optimize
php artisan config:cache
php artisan route:cache

# Generar código
php artisan make:service ServiceName
php artisan make:repository RepositoryName
php artisan make:test ServiceNameTest --unit

# Migraciones
php artisan migrate:fresh --seed
php artisan migrate:rollback
php artisan migrate:status

# Queue
php artisan queue:work
php artisan queue:failed
php artisan queue:retry all
```

### **Composer Commands**
```bash
# Actualizar dependencias
composer update
composer install --optimize-autoloader

# Análisis de dependencias
composer audit
composer outdated

# Generar autoload
composer dump-autoload
composer dump-autoload --optimize
```

### **Git Commands**
```bash
# Crear rama de refactoring
git checkout -b feature/refactoring-v1
git push -u origin feature/refactoring-v1

# Commits frecuentes
git add .
git commit -m "refactor: [FASE] descripción del cambio"
git push

# Ver historial de cambios
git log --oneline --graph
git diff HEAD~1
```

---

## ⚙️ CONFIGURACIONES

### **PHPStan Configuration**
```yaml
# phpstan.neon
parameters:
    level: 8
    paths:
        - app
    excludePaths:
        - app/Console/Kernel.php
    checkMissingIterableValueType: false
    checkGenericClassInNonGenericObjectType: false
```

### **PHP CS Fixer Configuration**
```php
// .php-cs-fixer.php
<?php

$finder = PhpCsFixer\Finder::create()
    ->in([
        __DIR__ . '/app',
        __DIR__ . '/tests',
    ])
    ->name('*.php')
    ->notName('*.blade.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12' => true,
        'array_syntax' => ['syntax' => 'short'],
        'ordered_imports' => ['sort_algorithm' => 'alpha'],
        'no_unused_imports' => true,
        'not_operator_with_successor_space' => true,
        'trailing_comma_in_multiline' => true,
        'phpdoc_scalar' => true,
        'unary_operator_spaces' => true,
        'binary_operator_spaces' => true,
        'blank_line_before_statement' => [
            'statements' => ['break', 'continue', 'declare', 'return', 'throw', 'try'],
        ],
        'phpdoc_single_line_var_spacing' => true,
        'phpdoc_var_without_name' => true,
        'method_argument_space' => [
            'on_multiline' => 'ensure_fully_multiline',
            'keep_multiple_spaces_after_comma' => true,
        ],
        'single_trait_insert_per_statement' => true,
    ])
    ->setFinder($finder);
```

### **PHPUnit Configuration**
```xml
<!-- phpunit.xml -->
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="vendor/phpunit/phpunit/phpunit.xsd"
         bootstrap="vendor/autoload.php"
         colors="true"
>
    <testsuites>
        <testsuite name="Unit">
            <directory suffix="Test.php">./tests/Unit</directory>
        </testsuite>
        <testsuite name="Feature">
            <directory suffix="Test.php">./tests/Feature</directory>
        </testsuite>
    </testsuites>
    <source>
        <include>
            <directory suffix=".php">./app</directory>
        </include>
    </source>
    <php>
        <env name="APP_ENV" value="testing"/>
        <env name="BCRYPT_ROUNDS" value="4"/>
        <env name="CACHE_DRIVER" value="array"/>
        <env name="DB_CONNECTION" value="sqlite"/>
        <env name="DB_DATABASE" value=":memory:"/>
        <env name="MAIL_MAILER" value="array"/>
        <env name="QUEUE_CONNECTION" value="sync"/>
        <env name="SESSION_DRIVER" value="array"/>
        <env name="TELESCOPE_ENABLED" value="false"/>
    </php>
</phpunit>
```

---

## 🧪 TESTING TOOLS

### **Laravel Testing Helpers**
```php
// tests/TestCase.php
<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Configuraciones comunes para tests
        $this->withoutExceptionHandling();
        $this->withoutMiddleware();
    }

    protected function createUser($attributes = [])
    {
        return \App\Models\User::factory()->create($attributes);
    }

    protected function createProduct($attributes = [])
    {
        return \App\Models\Producto::factory()->create($attributes);
    }

    protected function assertServiceMethod($service, $method, $expected)
    {
        $result = $service->$method();
        $this->assertEquals($expected, $result);
    }
}
```

### **Service Testing Template**
```php
// tests/Unit/Services/ServiceNameTest.php
<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\ServiceName;
use Mockery;

class ServiceNameTest extends TestCase
{
    protected $service;
    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->repository = Mockery::mock('App\Repositories\Contracts\RepositoryInterface');
        $this->service = new ServiceName($this->repository);
    }

    public function test_method_name()
    {
        // Arrange
        $expected = 'expected result';
        $this->repository->shouldReceive('method')
            ->once()
            ->andReturn($expected);

        // Act
        $result = $this->service->method();

        // Assert
        $this->assertEquals($expected, $result);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
```

---

## 📊 PERFORMANCE TOOLS

### **Laravel Telescope Configuration**
```php
// config/telescope.php
<?php

use Laravel\Telescope\Http\Middleware\Authorize;
use Laravel\Telescope\Watchers;

return [
    'domain' => env('TELESCOPE_DOMAIN'),
    'path' => env('TELESCOPE_PATH', 'telescope'),
    'driver' => env('TELESCOPE_DRIVER', 'database'),
    'storage' => [
        'database' => [
            'connection' => env('DB_CONNECTION', 'mysql'),
            'chunk' => 1000,
        ],
    ],
    'enabled' => env('TELESCOPE_ENABLED', true),
    'middleware' => [
        'web',
        Authorize::class,
    ],
    'ignore_paths' => [
        'nova-api*',
        'horizon*',
        'telescope*',
    ],
    'ignore_commands' => [
        //
    ],
    'watchers' => [
        Watchers\CacheWatcher::class => env('TELESCOPE_CACHE_WATCHER', true),
        Watchers\CommandWatcher::class => env('TELESCOPE_COMMAND_WATCHER', true),
        Watchers\ExceptionWatcher::class => env('TELESCOPE_EXCEPTION_WATCHER', true),
        Watchers\JobWatcher::class => env('TELESCOPE_JOB_WATCHER', true),
        Watchers\LogWatcher::class => env('TELESCOPE_LOG_WATCHER', true),
        Watchers\MailWatcher::class => env('TELESCOPE_MAIL_WATCHER', true),
        Watchers\ModelWatcher::class => env('TELESCOPE_MODEL_WATCHER', true),
        Watchers\NotificationWatcher::class => env('TELESCOPE_NOTIFICATION_WATCHER', true),
        Watchers\QueryWatcher::class => env('TELESCOPE_QUERY_WATCHER', true),
        Watchers\RequestWatcher::class => env('TELESCOPE_REQUEST_WATCHER', true),
        Watchers\ScheduleWatcher::class => env('TELESCOPE_SCHEDULE_WATCHER', true),
    ],
];
```

### **Performance Monitoring Script**
```bash
#!/bin/bash
# monitor-performance.sh

echo "📊 Monitoreando performance..."

# Crear directorio de métricas
mkdir -p metrics

# Medir tiempo de respuesta de endpoints críticos
echo "⏱️  Mediendo tiempo de respuesta..."

# Endpoint de productos
curl -w "@curl-format.txt" -o /dev/null -s "http://localhost/productos" > metrics/products-response.txt

# Endpoint de checkout
curl -w "@curl-format.txt" -o /dev/null -s "http://localhost/checkout" > metrics/checkout-response.txt

# Endpoint de inventario
curl -w "@curl-format.txt" -o /dev/null -s "http://localhost/inventario" > metrics/inventory-response.txt

# Analizar logs de Laravel
echo "📝 Analizando logs..."
tail -n 1000 storage/logs/laravel.log | grep -E "(ERROR|WARNING)" > metrics/errors.txt

# Verificar uso de memoria
echo "💾 Verificando uso de memoria..."
php -r "echo 'Memoria usada: ' . memory_get_usage(true) / 1024 / 1024 . ' MB' . PHP_EOL;" > metrics/memory.txt

echo "✅ Monitoreo completado. Revisar carpeta 'metrics'"
```

### **Curl Format File**
```txt
# curl-format.txt
     time_namelookup:  %{time_namelookup}\n
        time_connect:  %{time_connect}\n
     time_appconnect:  %{time_appconnect}\n
    time_pretransfer:  %{time_pretransfer}\n
       time_redirect:  %{time_redirect}\n
  time_starttransfer:  %{time_starttransfer}\n
                     ----------\n
          time_total:  %{time_total}\n
```

---

## 🎯 COMANDOS DE REFACTORING RÁPIDO

### **Crear Service y Repository**
```bash
# Crear service
php artisan make:service CheckoutService

# Crear repository interface
php artisan make:repository CheckoutRepositoryInterface --interface

# Crear repository implementation
php artisan make:repository CheckoutRepository --implementation

# Crear test
php artisan make:test CheckoutServiceTest --unit
```

### **Análisis Rápido**
```bash
# Análisis completo en un comando
./analyze-code.sh && ./run-tests.sh && ./monitor-performance.sh

# Verificar calidad del código
./vendor/bin/phpstan analyse app --level=8 && ./vendor/bin/php-cs-fixer fix --dry-run
```

### **Backup y Rollback**
```bash
# Backup antes de cambios importantes
./backup-project.sh

# Rollback si algo sale mal
git reset --hard HEAD~1
git clean -fd
```

---

## 📈 MÉTRICAS DE SEGUIMIENTO

### **Script de Métricas**
```bash
#!/bin/bash
# generate-metrics.sh

echo "📊 Generando métricas de refactoring..."

# Contar líneas de código por controller
echo "📈 Líneas por controller:"
find app/Http/Controllers -name "*.php" -exec wc -l {} + | sort -nr

# Contar servicios creados
echo "🔧 Servicios creados:"
find app/Services -name "*.php" | wc -l

# Contar repositories creados
echo "🗄️  Repositories creados:"
find app/Repositories -name "*.php" | wc -l

# Cobertura de tests
echo "🧪 Cobertura de tests:"
./vendor/bin/phpunit --coverage-text | grep "Lines:"

# Comandos ejecutados
echo "⚡ Comandos ejecutados:"
git log --oneline --grep="refactor" | wc -l

echo "✅ Métricas generadas"
```

---

*Herramientas y scripts para automatizar el proceso de refactoring*
*Versión: 1.0*
*Última actualización: [Fecha]*
