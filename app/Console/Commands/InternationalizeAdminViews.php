<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InternationalizeAdminViews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:i18n {--view= : Specific view to internationalize} {--dry-run : Show what would be changed without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Internationalize admin views by replacing hardcoded Spanish text with translation functions';

    /**
     * Common translation patterns for admin views
     */
    protected $translationPatterns = [
        // Dashboard patterns
        'Dashboard' => '__(\'admin.dashboard.title\')',
        'Bienvenido al panel de administración de 4GMovil' => '__(\'admin.dashboard.welcome\')',
        'Inventario' => '__(\'admin.dashboard.inventory\')',
        'Nuevo producto' => '__(\'admin.dashboard.new_product\')',
        
        // Stats patterns
        'Total Productos' => '__(\'admin.stats.total_products\')',
        'Total Usuarios' => '__(\'admin.stats.total_users\')',
        'Total Categorías' => '__(\'admin.stats.total_categories\')',
        'Total Marcas' => '__(\'admin.stats.total_brands\')',
        'Ver Productos' => '__(\'admin.stats.view_products\')',
        'Ver Usuarios' => '__(\'admin.stats.view_users\')',
        'Ver categorías' => '__(\'admin.stats.view_categories\')',
        'Ver marcas' => '__(\'admin.stats.view_brands\')',
        
        // Webhooks patterns
        '📊 Estadísticas de Webhooks y Pagos' => '__(\'admin.webhooks.title\')',
        'Monitoreo en tiempo real de eventos de Stripe' => '__(\'admin.webhooks.subtitle\')',
        'Total Eventos' => '__(\'admin.webhooks.total_events\')',
        'Procesados' => '__(\'admin.webhooks.processed\')',
        'Fallidos' => '__(\'admin.webhooks.failed\')',
        'Pendientes' => '__(\'admin.webhooks.pending\')',
        '🔔 Eventos Recientes de Webhooks' => '__(\'admin.webhooks.recent_events\')',
        'Últimos eventos procesados por Stripe' => '__(\'admin.webhooks.recent_subtitle\')',
        'Filtros' => '__(\'admin.webhooks.filters\')',
        'Estado' => '__(\'admin.webhooks.status\')',
        'Todos los estados' => '__(\'admin.webhooks.all_statuses\')',
        'Tipo de Evento' => '__(\'admin.webhooks.event_type\')',
        'Todos los tipos' => '__(\'admin.webhooks.all_types\')',
        'Pago Exitoso' => '__(\'admin.webhooks.payment_successful\')',
        'Pago Fallido' => '__(\'admin.webhooks.payment_failed\')',
        'Pago Cancelado' => '__(\'admin.webhooks.payment_cancelled\')',
        'Fecha Desde' => '__(\'admin.webhooks.date_from\')',
        'Fecha Hasta' => '__(\'admin.webhooks.date_to\')',
        'ID de Pedido' => '__(\'admin.webhooks.order_id\')',
        'Ej: 123' => '__(\'admin.webhooks.order_id_placeholder\')',
        'Límite de resultados' => '__(\'admin.webhooks.results_limit\')',
        'Filtrar' => '__(\'admin.webhooks.filter\')',
        'Limpiar' => '__(\'admin.webhooks.clear\')',
        'Evento' => '__(\'admin.webhooks.event\')',
        'Pedido' => '__(\'admin.webhooks.order\')',
        'Fecha' => '__(\'admin.webhooks.date\')',
        'Intentos' => '__(\'admin.webhooks.attempts\')',
        'Procesado' => '__(\'admin.webhooks.processed_status\')',
        'Fallido' => '__(\'admin.webhooks.failed_status\')',
        'Pendiente' => '__(\'admin.webhooks.pending_status\')',
        'N/A' => '__(\'admin.webhooks.not_available\')',
        
        // Orders patterns
        '🛒 Estadísticas de Pedidos' => '__(\'admin.orders.title\')',
        'Estado actual de los pedidos en el sistema' => '__(\'admin.orders.subtitle\')',
        'Total Pedidos' => '__(\'admin.orders.total_orders\')',
        'Pendientes' => '__(\'admin.orders.pending_orders\')',
        'Confirmados' => '__(\'admin.orders.confirmed_orders\')',
        'Cancelados' => '__(\'admin.orders.cancelled_orders\')',
        
        // Products patterns
        'Últimos productos agregados' => '__(\'admin.products.recent_title\')',
        'Los productos más recientes en el catálogo' => '__(\'admin.products.recent_subtitle\')',
        'Producto' => '__(\'admin.products.product\')',
        'Categoría' => '__(\'admin.products.category\')',
        'Precio' => '__(\'admin.products.price\')',
        'Acciones' => '__(\'admin.products.actions\')',
        'Editar' => '__(\'admin.products.edit\')',
        'Nuevo' => '__(\'admin.products.new_status\')',
        'No hay productos' => '__(\'admin.products.no_products\')',
        'No hay productos registrados en el catálogo.' => '__(\'admin.products.no_products_message\')',
        'No hay productos registrados' => '__(\'admin.products.no_products_message\')',
        
        // Common actions
        'Crear' => '__(\'admin.actions.create\')',
        'Editar' => '__(\'admin.actions.edit\')',
        'Eliminar' => '__(\'admin.actions.delete\')',
        'Guardar' => '__(\'admin.actions.save\')',
        'Cancelar' => '__(\'admin.actions.cancel\')',
        'Buscar' => '__(\'admin.actions.search\')',
        'Filtrar' => '__(\'admin.actions.filter\')',
        'Limpiar' => '__(\'admin.actions.clear\')',
        'Mostrar' => '__(\'admin.actions.show\')',
        'Ocultar' => '__(\'admin.actions.hide\')',
        'Ver' => '__(\'admin.actions.view\')',
        'Volver' => '__(\'admin.actions.back\')',
        'Siguiente' => '__(\'admin.actions.next\')',
        'Anterior' => '__(\'admin.actions.previous\')',
        'Primero' => '__(\'admin.actions.first\')',
        'Último' => '__(\'admin.actions.last\')',
        'Todos' => '__(\'admin.actions.all\')',
        'Ninguno' => '__(\'admin.actions.none\')',
        
        // Status and states
        'Activo' => '__(\'admin.status.active\')',
        'Inactivo' => '__(\'admin.status.inactive\')',
        'Verificado' => '__(\'admin.status.verified\')',
        'No Verificado' => '__(\'admin.status.not_verified\')',
        'Habilitado' => '__(\'admin.status.enabled\')',
        'Deshabilitado' => '__(\'admin.status.disabled\')',
        'Pendiente' => '__(\'admin.status.pending\')',
        'Completado' => '__(\'admin.status.completed\')',
        'Cancelado' => '__(\'admin.status.cancelled\')',
        'Sí' => '__(\'admin.status.yes\')',
        'No' => '__(\'admin.status.no\')',
        
        // Form fields
        'Nombre' => '__(\'admin.fields.name\')',
        'Email' => '__(\'admin.fields.email\')',
        'Teléfono' => '__(\'admin.fields.phone\')',
        'Foto' => '__(\'admin.fields.photo\')',
        'Categoría' => '__(\'admin.fields.category\')',
        'Marca' => '__(\'admin.fields.brand\')',
        'Precio' => '__(\'admin.fields.price\')',
        'Descripción' => '__(\'admin.fields.description\')',
        'Estado' => '__(\'admin.fields.status\')',
        'Fecha de Creación' => '__(\'admin.fields.created_at\')',
        'Fecha de Actualización' => '__(\'admin.fields.updated_at\')',
        'Acciones' => '__(\'admin.fields.actions\')',
        'Tipo' => '__(\'admin.fields.type\')',
        'Requerido' => '__(\'admin.fields.required\')',
        'Opcional' => '__(\'admin.fields.optional\')',
        'Unidad' => '__(\'admin.fields.unit\')',
        'Campo' => '__(\'admin.fields.field\')',
        'Etiqueta' => '__(\'admin.fields.label\')',
        'Valor' => '__(\'admin.fields.value\')',
        'Placeholder' => '__(\'admin.fields.placeholder\')',
        'Buscar por nombre, email, ID...' => '__(\'admin.fields.search_placeholder\')',
        'Buscar usuarios...' => '__(\'admin.fields.search_users\')',
        'No hay datos disponibles' => '__(\'admin.fields.no_data\')',
        'No hay usuarios registrados en el sistema' => '__(\'admin.fields.no_users\')',
        'No hay productos registrados' => '__(\'admin.fields.no_products\')',
        'No hay categorías registradas' => '__(\'admin.fields.no_categories\')',
        'No hay marcas registradas' => '__(\'admin.fields.no_brands\')',
        'No hay pedidos registrados' => '__(\'admin.fields.no_orders\')',
        'No hay especificaciones registradas' => '__(\'admin.fields.no_specifications\')',
        'Sin categoría' => '__(\'admin.fields.without_category\')',
        'Sin datos' => '__(\'admin.fields.without_data\')',
        
        // User management
        'Usuarios' => '__(\'admin.users.title\')',
        'Crear Usuario' => '__(\'admin.users.create_user\')',
        'Editar Usuario' => '__(\'admin.users.edit_user\')',
        'Eliminar Usuario' => '__(\'admin.users.delete_user\')',
        'Detalles del Usuario' => '__(\'admin.users.user_details\')',
        'Lista de Usuarios' => '__(\'admin.users.user_list\')',
        'Filtrar por Estado' => '__(\'admin.users.filter_by_status\')',
        'Todos los Estados' => '__(\'admin.users.all_statuses\')',
        'Buscar Usuarios' => '__(\'admin.users.search_users\')',
        'Usuario creado exitosamente' => '__(\'admin.users.user_created\')',
        'Usuario actualizado exitosamente' => '__(\'admin.users.user_updated\')',
        'Usuario eliminado exitosamente' => '__(\'admin.users.user_deleted\')',
        
        // Specifications management
        'Especificaciones' => '__(\'admin.specifications.title\')',
        'Crear Especificación' => '__(\'admin.specifications.create_specification\')',
        'Editar Especificación' => '__(\'admin.specifications.edit_specification\')',
        'Eliminar Especificación' => '__(\'admin.specifications.delete_specification\')',
        'Detalles de la Especificación' => '__(\'admin.specifications.specification_details\')',
        'Lista de Especificaciones' => '__(\'admin.specifications.specification_list\')',
        'Especificación creada exitosamente' => '__(\'admin.specifications.specification_created\')',
        'Especificación actualizada exitosamente' => '__(\'admin.specifications.specification_updated\')',
        'Especificación eliminada exitosamente' => '__(\'admin.specifications.specification_deleted\')',
        'Tipo de Campo' => '__(\'admin.specifications.field_type\')',
        'Nombre del Campo' => '__(\'admin.specifications.field_name\')',
        'Etiqueta del Campo' => '__(\'admin.specifications.field_label\')',
        'Es Requerido' => '__(\'admin.specifications.is_required\')',
        'Unidad del Campo' => '__(\'admin.specifications.field_unit\')',
        'Modifica la especificación técnica' => '__(\'admin.specifications.modify_specification\')',
        
        // Inventory management
        'Inventario' => '__(\'admin.inventory.title\')',
        'Nivel de Stock' => '__(\'admin.inventory.stock_level\')',
        'Alerta de Stock Bajo' => '__(\'admin.inventory.low_stock_alert\')',
        'Sin Stock' => '__(\'admin.inventory.out_of_stock\')',
        'En Stock' => '__(\'admin.inventory.in_stock\')',
        'Movimientos de Stock' => '__(\'admin.inventory.stock_movements\')',
        'Reporte de Stock' => '__(\'admin.inventory.stock_report\')',
        'Exportar Reporte' => '__(\'admin.inventory.export_report\')',
        
        // Reports
        'Reportes' => '__(\'admin.reports.title\')',
        'Generar Reporte' => '__(\'admin.reports.generate_report\')',
        'Exportar Reporte' => '__(\'admin.reports.export_report\')',
        'Reporte generado exitosamente' => '__(\'admin.reports.report_generated\')',
        'Valor por Categoría' => '__(\'admin.reports.value_by_category\')',
        'Movimientos de Stock' => '__(\'admin.reports.stock_movements\')',
        'Reporte de Ventas' => '__(\'admin.reports.sales_report\')',
        'Reporte de Inventario' => '__(\'admin.reports.inventory_report\')',
        
        // Messages and notifications
        'Operación exitosa' => '__(\'admin.messages.success\')',
        'Error en la operación' => '__(\'admin.messages.error\')',
        'Advertencia' => '__(\'admin.messages.warning\')',
        'Información' => '__(\'admin.messages.info\')',
        '¿Estás seguro de que deseas eliminar este elemento?' => '__(\'admin.messages.confirm_delete\')',
        '¿Estás seguro de que deseas realizar esta acción?' => '__(\'admin.messages.confirm_action\')',
        'Operación realizada exitosamente' => '__(\'admin.messages.operation_successful\')',
        'La operación falló' => '__(\'admin.messages.operation_failed\')',
        'Datos cargados exitosamente' => '__(\'admin.messages.data_loaded\')',
        'Datos guardados exitosamente' => '__(\'admin.messages.data_saved\')',
        'Datos eliminados exitosamente' => '__(\'admin.messages.data_deleted\')',
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🌍 Starting admin views internationalization...');
        
        $adminViewsPath = resource_path('views/pages/admin');
        
        if (!File::exists($adminViewsPath)) {
            $this->error('Admin views directory not found: ' . $adminViewsPath);
            return 1;
        }

        $viewToProcess = $this->option('view');
        $dryRun = $this->option('dry-run');
        
        if ($viewToProcess) {
            $this->processView($adminViewsPath . '/' . $viewToProcess, $dryRun);
        } else {
            $this->processAllViews($adminViewsPath, $dryRun);
        }
        
        $this->info('✅ Internationalization complete!');
        return 0;
    }

    /**
     * Process all admin views
     */
    protected function processAllViews($path, $dryRun)
    {
        $views = File::allFiles($path);
        $processedCount = 0;
        
        foreach ($views as $view) {
            if ($view->getExtension() === 'php') {
                $this->processView($view->getPathname(), $dryRun);
                $processedCount++;
            }
        }
        
        $this->info("Processed {$processedCount} views");
    }

    /**
     * Process a single view file
     */
    protected function processView($filePath, $dryRun)
    {
        if (!File::exists($filePath)) {
            $this->error("View file not found: {$filePath}");
            return;
        }

        $content = File::get($filePath);
        $originalContent = $content;
        $changesCount = 0;
        
        $this->line("Processing: " . basename($filePath));
        
        foreach ($this->translationPatterns as $spanishText => $translationFunction) {
            // Skip if the text is already in a translation function
            if (strpos($content, "__('admin.") !== false) {
                // Find all existing translation functions and skip those areas
                $content = $this->replaceTextSafely($content, $spanishText, $translationFunction);
            } else {
                // Look for the text in various contexts
                $patterns = [
                    // In HTML attributes
                    '>' . $spanishText . '<' => '>' . $translationFunction . '<',
                    // In quotes
                    '"' . $spanishText . '"' => '"' . $translationFunction . '"',
                    "'" . $spanishText . "'" => "'" . $translationFunction . "'",
                    // Standalone text
                    ' ' . $spanishText . ' ' => ' ' . $translationFunction . ' ',
                    // At start of line
                    $spanishText . ' ' => $translationFunction . ' ',
                    // At end of line
                    ' ' . $spanishText => ' ' . $translationFunction,
                ];
                
                foreach ($patterns as $pattern => $replacement) {
                    $newContent = str_replace($pattern, $replacement, $content);
                    if ($newContent !== $content) {
                        $content = $newContent;
                        $changesCount++;
                    }
                }
            }
        }
        
        if ($changesCount > 0) {
            if ($dryRun) {
                $this->warn("Would make {$changesCount} changes to " . basename($filePath));
            } else {
                File::put($filePath, $content);
                $this->info("Made {$changesCount} changes to " . basename($filePath));
            }
        } else {
            $this->line("No changes needed for " . basename($filePath));
        }
    }

    /**
     * Replace text safely, avoiding translation functions
     */
    protected function replaceTextSafely($content, $spanishText, $translationFunction)
    {
        // Use a more sophisticated approach to avoid replacing inside translation functions
        // First, protect all existing translation functions
        $protectedContent = preg_replace_callback(
            '/__\([^)]+\)/',
            function($matches) {
                return '___PROTECTED_TRANSLATION___' . base64_encode($matches[0]) . '___END_PROTECTED___';
            },
            $content
        );
        
        // Now apply replacements to the protected content
        $patterns = [
            // In HTML attributes
            '>' . $spanishText . '<' => '>' . $translationFunction . '<',
            // In quotes
            '"' . $spanishText . '"' => '"' . $translationFunction . '"',
            "'" . $spanishText . "'" => "'" . $translationFunction . "'",
            // Standalone text
            ' ' . $spanishText . ' ' => ' ' . $translationFunction . ' ',
            // At start of line
            $spanishText . ' ' => $translationFunction . ' ',
            // At end of line
            ' ' . $spanishText => ' ' . $translationFunction,
        ];
        
        foreach ($patterns as $pattern => $replacement) {
            $protectedContent = str_replace($pattern, $replacement, $protectedContent);
        }
        
        // Restore the protected translation functions
        $finalContent = preg_replace_callback(
            '/___PROTECTED_TRANSLATION___(.+?)___END_PROTECTED___/',
            function($matches) {
                return base64_decode($matches[1]);
            },
            $protectedContent
        );
        
        return $finalContent;
    }
}
