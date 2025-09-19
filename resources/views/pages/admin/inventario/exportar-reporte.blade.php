@extends('layouts.app-new')

@section('title', 'Exportar Reporte de Inventario - 4GMovil')

@push('jquery-script')
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Encabezado -->
    <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white"{{ >{{ __('admin.reports.export_report') }} }}de{{ {{ __('admin.inventory.title') }}< }}/h1>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-300">Generar y descargar reportes del inventario</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.inventario.dashboard') }}" 
                   class="inline-flex items-center rounded-md bg-gray-100 dark:bg-gray-800 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-100 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M17 10a.75.75 0 01-.75.75H5.612l4.158 4.158a.75.75 0 11-1.06 1.06l-5.5-5.5a.75.75 0 010-1.06l5.5-5.5a.75.75 0 111.06 1.06L5.612 9.25H16.25A.75.75 0 0117 10z" clip-rule="evenodd" />
                    </svg>
                   {{ {{ __('admin.actions.back') }} }}                </a>
            </div>
        </div>
    </div>

    <!-- Opciones de exportación -->
    <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Opciones de Exportación</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Exportar a PDF -->
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-6 hover:border-brand-500 dark:hover:border-brand-400 transition-colors">
                <div class="flex items-center mb-4">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h4 class="text-lg font-medium text-gray-900 dark:text-white">Exportar a PDF</h4>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Generar reporte completo en formato PDF</p>
                    </div>
                </div>
                
                <div class="space-y-3">
                    <div class="flex items-center">
                        <input type="checkbox" id="pdf_resumen" name="pdf_resumen" class="h-4 w-4 text-brand-600 focus:ring-brand-500 border-gray-300 rounded" checked>
                        <label for="pdf_resumen" class="ml-2 text-sm text-gray-700 dark:text-gray-300">Resumen general</label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" id="pdf_alertas" name="pdf_alertas" class="h-4 w-4 text-brand-600 focus:ring-brand-500 border-gray-300 rounded" checked>
                        <label for="pdf_alertas" class="ml-2 text-sm text-gray-700 dark:text-gray-300">Alertas de inventario</label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" id="pdf_productos" name="pdf_productos" class="h-4 w-4 text-brand-600 focus:ring-brand-500 border-gray-300 rounded" checked>
                        <label for="pdf_productos" class="ml-2 text-sm text-gray-700 dark:text-gray-300">Lista de productos</label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" id="pdf_categorias" name="pdf_categorias" class="h-4 w-4 text-brand-600 focus:ring-brand-500 border-gray-300 rounded" checked>
                        <label for="pdf_categorias" class="ml-2 text-sm text-gray-700 dark:text-gray-300"{{ >{{ __('admin.fields.value') }} }}por categoría</label>
                    </div>
                </div>
                
                <button id="exportPDF" class="mt-4 w-full inline-flex justify-center items-center rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Exportar PDF
                </button>
            </div>

            <!-- Exportar a Excel -->
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-6 hover:border-brand-500 dark:hover:border-brand-400 transition-colors">
                <div class="flex items-center mb-4">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                                                                 <div class="ml-4">
                            <h4 class="text-lg font-medium text-gray-900 dark:text-white">Exportar a Excel</h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Generar reporte en formato CSV</p>
                        </div>
                </div>
                
                <div class="space-y-3">
                    <div class="flex items-center">
                        <input type="checkbox" id="excel_resumen" name="excel_resumen" class="h-4 w-4 text-brand-600 focus:ring-brand-500 border-gray-300 rounded" checked>
                        <label for="excel_resumen" class="ml-2 text-sm text-gray-700 dark:text-gray-300">Resumen general</label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" id="excel_alertas" name="excel_alertas" class="h-4 w-4 text-brand-600 focus:ring-brand-500 border-gray-300 rounded" checked>
                        <label for="excel_alertas" class="ml-2 text-sm text-gray-700 dark:text-gray-300">Alertas de inventario</label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" id="excel_productos" name="excel_productos" class="h-4 w-4 text-brand-600 focus:ring-brand-500 border-gray-300 rounded" checked>
                        <label for="excel_productos" class="ml-2 text-sm text-gray-700 dark:text-gray-300">Lista de productos</label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" id="excel_categorias" name="excel_categorias" class="h-4 w-4 text-brand-600 focus:ring-brand-500 border-gray-300 rounded" checked>
                        <label for="excel_categorias" class="ml-2 text-sm text-gray-700 dark:text-gray-300"{{ >{{ __('admin.fields.value') }} }}por categoría</label>
                    </div>
                </div>
                
                                                     <button id="exportExcel" class="mt-4 w-full inline-flex justify-center items-center rounded-md bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-600">
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Exportar Excel
                    </button>
            </div>
        </div>
    </div>

    <!-- Vista previa del reporte -->
    <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Vista Previa del Reporte</h3>
        
        <!-- Resumen general -->
        <div class="mb-6">
            <h4 class="text-md font-medium text-gray-900 dark:text-white mb-3">Resumen General</h4>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0v10a2 2 0 01-2 2H7a2 2 0 01-2-2V11m14-6H5a2 2 0 00-2 2v2h18V7a2 2 0 00-2-2z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-blue-600 dark:text-blue-400"{{ >{{ __('admin.stats.total_products') }}< }}/p>
                            <p class="text-lg font-semibold text-blue-900 dark:text-blue-100">{{ $reporte['resumen_general']['total_productos'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-600 dark:text-green-400"{{ >{{ __('admin.fields.value') }} }}Total</p>
                            <p class="text-lg font-semibold text-green-900 dark:text-green-100">${{ number_format($reporte['resumen_general']['valor_total_inventario'] ?? 0, 2) }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-yellow-600 dark:text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-yellow-600 dark:text-yellow-400">Stock Bajo</p>
                            <p class="text-lg font-semibold text-yellow-900 dark:text-yellow-100">{{ $reporte['resumen_general']['productos_stock_bajo'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-red-50 dark:bg-red-900/20 p-4 rounded-lg">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-600 dark:text-red-400"{{ >{{ __('admin.inventory.out_of_stock') }}< }}/p>
                            <p class="text-lg font-semibold text-red-900 dark:text-red-100">{{ $reporte['resumen_general']['productos_sin_stock'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alertas de inventario -->
        @if(isset($reporte['alertas']) && !empty($reporte['alertas']))
        <div class="mb-6">
            <h4 class="text-md font-medium text-gray-900 dark:text-white mb-3">Alertas de{{ {{ __('admin.inventory.title') }}< }}/h4>
            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Alertas Activas</h3>
                        <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach($reporte['alertas'] as $alerta)
                                <li>{{ $alerta }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!--{{ {{ __('admin.fields.value') }} }}por categoría -->
        @if(isset($reporte['valor_por_categoria']) && !empty($reporte['valor_por_categoria']))
        <div class="mb-6">
            <h4 class="text-md font-medium text-gray-900 dark:text-white mb-3"{{ >{{ __('admin.fields.value') }} }}por{{ {{ __('admin.fields.category') }}< }}/h4>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"{{ >{{ __('admin.fields.category') }}< }}/th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Productos</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Stock</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"{{ >{{ __('admin.fields.value') }}< }}/th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($reporte['valor_por_categoria'] as $item)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ $item['categoria']->nombre ?? {{ '{{ __('admin.fields.without_category') }}' }} }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ $item['productos_count'] ?? 0 }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ $item['stock_total'] ?? 0 }}
                            </td>
                                                         <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 dark:text-white">
                                 ${{ number_format($item['valor_total'], 2) }}
                             </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>

    <!--{{ {{ __('admin.messages.info') }} }}adicional -->
    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200"{{ >{{ __('admin.messages.info') }} }}del Reporte</h3>
                <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                    <p>• El reporte incluirá todos los datos seleccionados en el formato elegido.</p>
                    <p>• Los archivos se generarán con la fecha y hora actual.</p>
                    <p>• Para reportes grandes, la generación puede tomar unos momentos.</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Exportar a PDF
        $('#exportPDF').on('click', function() {
            const secciones = [];
            
            if ($('#pdf_resumen').is(':checked')) secciones.push('resumen');
            if ($('#pdf_alertas').is(':checked')) secciones.push('alertas');
            if ($('#pdf_productos').is(':checked')) secciones.push('productos');
            if ($('#pdf_categorias').is(':checked')) secciones.push('categorias');
            
            if (secciones.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Selecciona secciones',
                    text: 'Debes seleccionar al menos una sección para exportar.',
                    confirmButtonColor: '#0088ff'
                });
                return;
            }
            
            Swal.fire({
                title: 'Generando PDF...',
                text: 'Por favor espera mientras se genera el reporte.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Llamada AJAX para obtener el HTML
            $.ajax({
                url: '{{ route("admin.inventario.exportar-pdf") }}',
                method: 'POST',
                data: {
                    secciones: secciones,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        //{{ {{ __('admin.actions.create') }} }}un elemento temporal para el HTML
                        const tempDiv = document.createElement('div');
                        tempDiv.innerHTML = response.html;
                        tempDiv.style.position = 'absolute';
                        tempDiv.style.left = '-9999px';
                        tempDiv.style.top = '0';
                        tempDiv.style.width = '800px';
                        tempDiv.style.backgroundColor = 'white';
                        document.body.appendChild(tempDiv);
                        
                                               // Convertir HTML a PDF usando html2canvas y jsPDF
                       html2canvas(tempDiv, {
                           scale: 2,
                           useCORS: true,
                           allowTaint: true,
                           backgroundColor: '#ffffff'
                       }).then(function(canvas) {
                           const imgData = canvas.toDataURL('image/png');
                           const pdf = new window.jspdf.jsPDF('p', 'mm', 'a4');
                           const imgWidth = 210;
                           const pageHeight = 295;
                           const imgHeight = canvas.height * imgWidth / canvas.width;
                           let heightLeft = imgHeight;
                           let position = 0;
                            
                            pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                            heightLeft -= pageHeight;
                            
                            while (heightLeft >= 0) {
                                position = heightLeft - imgHeight;
                                pdf.addPage();
                                pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                                heightLeft -= pageHeight;
                            }
                            
                            // Descargar el PDF
                            pdf.save(response.filename);
                            
                            //{{ {{ __('admin.actions.clear') }} }}                            document.body.removeChild(tempDiv);
                            
                            Swal.fire({
                                icon: 'success',
                                title: 'PDF Generado',
                                text: 'El reporte se ha descargado correctamente.',
                                confirmButtonColor: '#0088ff'
                            });
                        }).catch(function(error) {
                            document.body.removeChild(tempDiv);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Error al generar el PDF: ' + error.message,
                                confirmButtonColor: '#0088ff'
                            });
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.error || 'Error al generar el PDF',
                            confirmButtonColor: '#0088ff'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    let errorMessage = 'Hubo un error al generar el PDF. Por favor intenta de nuevo.';
                    
                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        errorMessage = xhr.responseJSON.error;
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: errorMessage,
                        confirmButtonColor: '#0088ff'
                    });
                }
            });
        });
        
        // Exportar a Excel (CSV)
        $('#exportExcel').on('click', function() {
            const secciones = [];
            
            if ($('#excel_resumen').is(':checked')) secciones.push('resumen');
            if ($('#excel_alertas').is(':checked')) secciones.push('alertas');
            if ($('#excel_productos').is(':checked')) secciones.push('productos');
            if ($('#excel_categorias').is(':checked')) secciones.push('categorias');
            
            if (secciones.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Selecciona secciones',
                    text: 'Debes seleccionar al menos una sección para exportar.',
                    confirmButtonColor: '#0088ff'
                });
                return;
            }
            
                         Swal.fire({
                 title: 'Generando Excel...',
                 text: 'Por favor espera mientras se genera el reporte.',
                 allowOutsideClick: false,
                 allowEscapeKey: false,
                 showConfirmButton: false,
                 didOpen: () => {
                     Swal.showLoading();
                 }
             });
            
            // Llamada AJAX para generar el CSV
            $.ajax({
                url: '{{ route("admin.inventario.exportar-excel") }}',
                method: 'POST',
                data: {
                    secciones: secciones,
                    _token: '{{ csrf_token() }}'
                },
                xhrFields: {
                    responseType: 'blob'
                },
                success: function(data) {
                    //{{ {{ __('admin.actions.create') }} }}enlace de descarga
                    const blob = new Blob([data], { type: 'text/csv' });
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = 'reporte_inventario_' + new Date().toISOString().slice(0, 19).replace(/:/g, '-') + '.csv';
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    document.body.removeChild(a);
                    
                                         Swal.fire({
                         icon: 'success',
                         title: 'Excel Generado',
                         text: 'El reporte se ha descargado correctamente.',
                         confirmButtonColor: '#0088ff'
                     });
                },
                                 error: function(xhr, status, error) {
                     let errorMessage = 'Hubo un error al generar el Excel. Por favor intenta de nuevo.';
                     
                     if (xhr.responseJSON && xhr.responseJSON.error) {
                         errorMessage = xhr.responseJSON.error;
                     }
                     
                     Swal.fire({
                         icon: 'error',
                         title: 'Error',
                         text: errorMessage,
                         confirmButtonColor: '#0088ff'
                     });
                 }
            });
        });
        
        // Validar que al menos una sección esté seleccionada
        $('input[type="checkbox"]').on('change', function() {
            const tipo = $(this).attr('id').split('_')[0];
            const checkboxes = $(`input[id^="${tipo}_"]`);
            const checked = checkboxes.filter(':checked').length;
            
            if (checked === 0) {
                $(this).prop('checked', true);
                Swal.fire({
                    icon: 'warning',
                    title: 'Sección requerida',
                    text: 'Debes seleccionar al menos una sección para exportar.',
                    confirmButtonColor: '#0088ff'
                });
            }
        });
    });
</script>
@endpush

@endsection 
