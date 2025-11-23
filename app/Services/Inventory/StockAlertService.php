<?php

namespace App\Services\Inventory;

use App\Models\Producto;
use App\Models\VarianteProducto;
use App\Services\Infrastructure\RedisCacheService;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class StockAlertService
{
    protected $cacheService;

    public function __construct(RedisCacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Limpia el caché de alertas
     */
    public function limpiarCache(): void
    {
        $this->cacheService->forget('alertas:estadisticas');
        $this->cacheService->forgetAlertas();
        $this->cacheService->forgetDashboard();
        $this->cacheService->forgetInventarioStats();
    }

    /**
     * Obtiene alertas de stock optimizadas agrupando variantes por producto
     */
    public function getOptimizedStockAlerts(?string $tipo = null, int $page = 1, bool $forzarActualizacion = false): array
    {
        try {
            // Si se fuerza la actualización, limpiar caché primero
            if ($forzarActualizacion) {
                $this->limpiarCache();
                // Limpiar también el caché de estadísticas específicamente
                $this->cacheService->forget('alertas:estadisticas');
            }
            
            // Obtener estadísticas generales (sin paginación)
            // Si se fuerza actualización, no usar caché, calcular directamente
            if ($forzarActualizacion) {
                try {
                    $estadisticas = [
                        'productos_criticos_count' => $this->getProductosCriticos()->count(),
                        'productos_stock_bajo_count' => $this->getProductosStockBajo()->count(),
                        'variantes_agotadas_count' => $this->getVariantesAgotadas()->count(),
                    ];
                } catch (\Exception $e) {
                    $estadisticas = [
                        'productos_criticos_count' => 0,
                        'productos_stock_bajo_count' => 0,
                        'variantes_agotadas_count' => 0,
                    ];
                }
            } else {
                $estadisticas = $this->cacheService->remember('alertas:estadisticas', 900, function () {
                    try {
                        return [
                            'productos_criticos_count' => $this->getProductosCriticos()->count(),
                            'productos_stock_bajo_count' => $this->getProductosStockBajo()->count(),
                            'variantes_agotadas_count' => $this->getVariantesAgotadas()->count(),
                        ];
                    } catch (\Exception $e) {
                    return [
                        'productos_criticos_count' => 0,
                        'productos_stock_bajo_count' => 0,
                        'variantes_agotadas_count' => 0,
                    ];
                }
            });
            }

            // Asegurar que todas las claves existan
            $estadisticas = array_merge([
                'productos_criticos_count' => 0,
                'productos_stock_bajo_count' => 0,
                'variantes_agotadas_count' => 0,
            ], $estadisticas);

            $totalAlertas = $estadisticas['productos_criticos_count'] + 
                           $estadisticas['productos_stock_bajo_count'] + 
                           $estadisticas['variantes_agotadas_count'];

            // Obtener datos paginados según el tipo
            $datosPaginados = null;
            if ($tipo) {
                try {
                    $datosPaginados = $this->getAlertasPaginadas($tipo, $page);
                } catch (\Exception $e) {
                    $datosPaginados = null;
                }
            }

            return [
                'productos_criticos_count' => $estadisticas['productos_criticos_count'],
                'productos_stock_bajo_count' => $estadisticas['productos_stock_bajo_count'],
                'variantes_agotadas_count' => $estadisticas['variantes_agotadas_count'],
                'total_alertas' => $totalAlertas,
                'datos_paginados' => $datosPaginados,
                'tipo_actual' => $tipo ?? 'criticos',
                'page_actual' => $page
            ];
        } catch (\Exception $e) {
            // Retornar valores por defecto en caso de error
            return [
                'productos_criticos_count' => 0,
                'productos_stock_bajo_count' => 0,
                'variantes_agotadas_count' => 0,
                'total_alertas' => 0,
                'datos_paginados' => null,
                'tipo_actual' => $tipo ?? 'criticos',
                'page_actual' => $page
            ];
        }
    }

    /**
     * Obtiene alertas paginadas según el tipo
     */
    private function getAlertasPaginadas(string $tipo, int $page): LengthAwarePaginator
    {
        $perPage = 10;
        $offset = ($page - 1) * $perPage;

        switch ($tipo) {
            case 'criticos':
                $productos = $this->getProductosCriticos();
                $total = $productos->count();
                $items = $productos->slice($offset, $perPage)->values();
                break;
            case 'bajo':
                $productos = $this->getProductosStockBajo();
                $total = $productos->count();
                $items = $productos->slice($offset, $perPage)->values();
                break;
            case 'agotadas':
                $variantes = $this->getVariantesAgotadas();
                $total = $variantes->count();
                $items = $variantes->slice($offset, $perPage)->values();
                break;
            default:
                $productos = $this->getProductosCriticos();
                $total = $productos->count();
                $items = $productos->slice($offset, $perPage)->values();
        }
        
        return new LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    /**
     * Obtiene productos con stock crítico (agrupando variantes)
     */
    public function getProductosCriticos(): Collection
    {
        return Producto::with(['variantes', 'categoria', 'marca', 'imagenes'])
            ->where('activo', true)
            ->get()
            ->map(function ($producto) {
                // Recargar relaciones si no están cargadas
                if (!$producto->relationLoaded('categoria')) {
                    $producto->load('categoria');
                }
                if (!$producto->relationLoaded('marca')) {
                    $producto->load('marca');
                }
                if (!$producto->relationLoaded('imagenes')) {
                    $producto->load('imagenes');
                }
                return $this->enriquecerProductoConAlertas($producto);
            })
            ->filter(function ($alerta) {
                // Validación estricta: solo incluir si realmente tiene variantes problemáticas o stock crítico
                $producto = $alerta['producto'];
                
                // Si tiene variantes, DEBE tener al menos una variante problemática con stock crítico
                if ($producto->variantes->isNotEmpty()) {
                    // Verificar que realmente tenga variantes problemáticas
                    if ($alerta['total_variantes_problematicas'] === 0) {
                        return false;
                    }
                    
                    // Verificar que al menos una variante esté en stock crítico (no solo bajo)
                    // Y que la variante esté disponible
                    $tieneVarianteCritica = $alerta['variantes_problematicas']->filter(function ($variante) use ($producto) {
                        // Verificar que la variante esté disponible
                        if (!$variante->disponible) {
                            return false;
                        }
                        return $this->esStockCritico($variante->stock, $producto->stock_inicial, $producto);
                    })->isNotEmpty();
                    
                    return $tieneVarianteCritica;
                }
                
                // Si no tiene variantes, verificar que realmente esté en stock crítico
                $stockDisponible = $producto->stock_disponible ?? $producto->stock;
                return $this->esStockCritico($stockDisponible, $producto->stock_inicial, $producto);
            });
    }

    /**
     * Obtiene productos con stock bajo (agrupando variantes)
     * Excluye productos que ya están en stock crítico
     */
    public function getProductosStockBajo(): Collection
    {
        return Producto::with(['variantes', 'categoria', 'marca', 'imagenes'])
            ->where('activo', true)
            ->get()
            ->map(function ($producto) {
                // Recargar relaciones si no están cargadas
                if (!$producto->relationLoaded('categoria')) {
                    $producto->load('categoria');
                }
                if (!$producto->relationLoaded('marca')) {
                    $producto->load('marca');
                }
                if (!$producto->relationLoaded('imagenes')) {
                    $producto->load('imagenes');
                }
                return $this->enriquecerProductoConAlertas($producto);
            })
            ->filter(function ($alerta) {
                // Validación estricta: solo incluir si realmente tiene variantes problemáticas o stock bajo
                $producto = $alerta['producto'];
                
                // Si tiene variantes, DEBE tener al menos una variante problemática
                if ($producto->variantes->isNotEmpty()) {
                    // Verificar que realmente tenga variantes problemáticas
                    if ($alerta['total_variantes_problematicas'] === 0) {
                        return false;
                    }
                    
                    // Recargar las variantes para asegurar que tenemos los datos más recientes
                    $producto->load('variantes');
                    
                    // Verificar nuevamente que las variantes problemáticas realmente estén por debajo de los umbrales
                    // Esto es una doble validación para asegurar que no se incluyan productos incorrectamente
                    $variantesRealesProblematicas = $producto->variantes
                        ->where('disponible', true)
                        ->filter(function ($variante) use ($producto) {
                            // Verificar que realmente esté por debajo de los umbrales
                            $esCritico = $this->esStockCritico($variante->stock, $producto->stock_inicial, $producto);
                            $esBajo = $this->esStockBajo($variante->stock, $producto->stock_inicial, $producto);
                            
                            return $esCritico || $esBajo;
                        });
                    
                    // Si después de la doble validación no hay variantes problemáticas, excluir
                    if ($variantesRealesProblematicas->isEmpty()) {
                        return false;
                    }
                    
                    // Excluir si tiene variantes críticas (deben estar en tab crítico, no bajo)
                    $tieneVarianteCritica = $variantesRealesProblematicas->filter(function ($variante) use ($producto) {
                        return $this->esStockCritico($variante->stock, $producto->stock_inicial, $producto);
                    })->isNotEmpty();
                    
                    if ($tieneVarianteCritica) {
                        return false; // Excluir si tiene variantes críticas
                    }
                    
                    // Debe tener al menos una variante con stock bajo (no crítico)
                    $tieneVarianteBaja = $variantesRealesProblematicas->filter(function ($variante) use ($producto) {
                        return $this->esStockBajo($variante->stock, $producto->stock_inicial, $producto);
                    })->isNotEmpty();
                    
                    return $tieneVarianteBaja;
                }
                
                // Si no tiene variantes, verificar que realmente esté en stock bajo (no crítico)
                $stockDisponible = $producto->stock_disponible ?? $producto->stock;
                if ($this->esStockCritico($stockDisponible, $producto->stock_inicial, $producto)) {
                    return false; // Excluir si está crítico
                }
                return $this->esStockBajo($stockDisponible, $producto->stock_inicial, $producto);
            });
    }

    /**
     * Obtiene variantes completamente agotadas
     */
    private function getVariantesAgotadas(): Collection
    {
        return VarianteProducto::with(['producto.categoria', 'producto.marca', 'producto.imagenes'])
            ->where('disponible', true)
            ->where('stock', 0)
            ->get()
            ->map(function ($variante) {
                // Recargar relaciones del producto si no están cargadas
                if ($variante->producto) {
                    if (!$variante->producto->relationLoaded('categoria')) {
                        $variante->producto->load('categoria');
                    }
                    if (!$variante->producto->relationLoaded('marca')) {
                        $variante->producto->load('marca');
                    }
                    if (!$variante->producto->relationLoaded('imagenes')) {
                        $variante->producto->load('imagenes');
                    }
                }
                return [
                    'variante' => $variante,
                    'producto' => $variante->producto,
                    'tipo_alerta' => 'agotado',
                    'stock_actual' => 0,
                    'umbral_stock_critico' => $variante->producto ? $this->calcularStockMinimo($variante->producto->stock_inicial) : 0,
                    'porcentaje' => 0
                ];
            });
    }

    /**
     * Verifica si un producto tiene variantes con stock crítico
     * Solo cuenta variantes disponibles (disponible = true)
     */
    private function tieneVariantesCriticas(Producto $producto): bool
    {
        return $producto->variantes
            ->where('disponible', true)
            ->filter(function ($variante) use ($producto) {
            return $this->esStockCritico($variante->stock, $producto->stock_inicial, $producto);
            })
            ->isNotEmpty();
    }

    /**
     * Verifica si un producto tiene variantes con stock bajo
     * Solo cuenta variantes disponibles (disponible = true)
     */
    private function tieneVariantesStockBajo(Producto $producto): bool
    {
        return $producto->variantes
            ->where('disponible', true)
            ->filter(function ($variante) use ($producto) {
            return $this->esStockBajo($variante->stock, $producto->stock_inicial, $producto);
            })
            ->isNotEmpty();
    }

    /**
     * Verifica si el stock es crítico
     * Usa umbral_stock_critico del producto si existe, sino calcula el 20% del stock_inicial
     * Stock crítico: cuando el stock está por debajo o igual al umbral crítico
     */
    private function esStockCritico(int $stock, ?int $stockInicial, ?Producto $producto = null): bool
    {
        // Si hay producto, usar su umbral crítico (umbral_stock_critico)
        // Usar getAttribute que respeta los accessors
        $umbralCritico = $producto ? $producto->getAttribute('umbral_stock_critico') : null;
        if ($umbralCritico === null || $umbralCritico <= 0) {
        // Fallback: calcular basado en stock_inicial
        if ($stockInicial === null || $stockInicial <= 0) {
                $umbralCritico = 5; // Fallback mínimo
            } else {
                $umbralCritico = (int) ceil(($stockInicial * 20) / 100);
            }
        }
        
        // Stock crítico: cuando está por debajo o igual al umbral crítico Y mayor que 0
        return $stock > 0 && $stock <= $umbralCritico;
    }

    /**
     * Verifica si el stock está bajo
     * Solo muestra alerta si el stock está por debajo del umbral_stock_bajo pero por encima del crítico
     * Si el stock está por encima del umbral_stock_bajo, no se muestra ninguna alerta
     */
    private function esStockBajo(int $stock, ?int $stockInicial, ?Producto $producto = null): bool
    {
        // Si el stock es 0 o negativo, no es stock bajo (sería sin stock)
        if ($stock <= 0) {
            return false;
        }
        
        // Obtener el umbral crítico (umbral_stock_critico)
        $umbralCritico = null;
        
        // Usar getAttribute que respeta los accessors
        $umbralCriticoFromProduct = $producto ? $producto->getAttribute('umbral_stock_critico') : null;
        if ($umbralCriticoFromProduct !== null && $umbralCriticoFromProduct > 0) {
            $umbralCritico = $umbralCriticoFromProduct;
        } elseif ($stockInicial !== null && $stockInicial > 0) {
            $umbralCritico = (int) ceil(($stockInicial * 20) / 100);
        } else {
            $umbralCritico = 5; // Fallback
        }
        
        // Primero verificar que NO esté en stock crítico
        // Si está en o por debajo del umbral crítico, no es stock bajo, es crítico
        if ($stock <= $umbralCritico) {
            return false; // Si está en o por debajo del umbral crítico, no mostrar como stock bajo
        }
        
        // Obtener el umbral bajo (umbral_stock_bajo)
        $umbralBajo = $producto ? $producto->getAttribute('umbral_stock_bajo') : null;
        if ($umbralBajo === null || $umbralBajo <= 0) {
        // Fallback: calcular basado en stock_inicial
        if ($stockInicial === null || $stockInicial <= 0) {
            return false; // Sin stock inicial, no mostrar stock bajo
            }
            $umbralBajo = (int) ceil(($stockInicial * 60) / 100);
        }
        
        // Stock bajo: está por encima del crítico pero por debajo o igual al umbral bajo
        // IMPORTANTE: Solo mostrar si está por debajo o igual al umbral bajo
        return $stock <= $umbralBajo;
    }

    /**
     * Enriquece un producto con información de alertas
     */
    private function enriquecerProductoConAlertas(Producto $producto): array
    {
        // Solo considerar variantes disponibles (disponible = true)
        // Verificar que realmente estén por debajo de los umbrales
        $variantesConProblemas = $producto->variantes
            ->where('disponible', true)
            ->filter(function ($variante) use ($producto) {
                // Verificar que la variante realmente esté por debajo de los umbrales
                $esCritico = $this->esStockCritico($variante->stock, $producto->stock_inicial, $producto);
                $esBajo = $this->esStockBajo($variante->stock, $producto->stock_inicial, $producto);
                
                // Solo incluir si realmente está en stock crítico o bajo
                return $esCritico || $esBajo;
        });

        return [
            'producto' => $producto,
            'variantes_problematicas' => $variantesConProblemas,
            'total_variantes_problematicas' => $variantesConProblemas->count(),
            'tipo_alerta' => $this->determinarTipoAlerta($producto, $variantesConProblemas),
            'stock_actual' => $producto->stock,
            'umbral_stock_critico' => $producto->getAttribute('umbral_stock_critico') ?? $this->calcularStockMinimo($producto->stock_inicial),
            'porcentaje' => $this->calcularPorcentaje($producto->stock, $producto->stock_inicial)
        ];
    }

    /**
     * Determina el tipo de alerta para un producto
     */
    private function determinarTipoAlerta(Producto $producto, Collection $variantesProblema): string
    {
        if ($producto->variantes->isNotEmpty()) {
            $tieneCriticas = $variantesProblema->filter(function ($variante) use ($producto) {
                return $this->esStockCritico($variante->stock, $producto->stock_inicial, $producto);
            })->isNotEmpty();

            return $tieneCriticas ? 'critico' : 'bajo';
        }

        return $this->esStockCritico($producto->stock, $producto->stock_inicial, $producto) ? 'critico' : 'bajo';
    }

    /**
     * Calcula el stock mínimo
     */
    private function calcularStockMinimo(?int $stockInicial): int
    {
        if ($stockInicial === null || $stockInicial <= 0) {
            return 10;
        }
        
        return (int) ceil(($stockInicial * 20) / 100);
    }

    /**
     * Calcula el porcentaje de stock
     */
    private function calcularPorcentaje(int $stock, ?int $stockInicial): float
    {
        if ($stockInicial === null || $stockInicial <= 0) {
            return 0;
        }
        
        return round(($stock / $stockInicial) * 100, 1);
    }

    /**
     * Obtiene las variantes problemáticas de un producto específico
     * Usa la misma lógica que enriquecerProductoConAlertas para consistencia
     */
    public function getVariantesProblematicas(int $productoId): Collection
    {
        $producto = Producto::with(['variantes', 'categoria', 'marca'])->findOrFail($productoId);
        
        // Solo considerar variantes disponibles (disponible = true)
        // Verificar que realmente estén por debajo de los umbrales
        return $producto->variantes
            ->where('disponible', true)
            ->filter(function ($variante) use ($producto) {
                // Verificar que la variante realmente esté por debajo de los umbrales
                $esCritico = $this->esStockCritico($variante->stock, $producto->stock_inicial, $producto);
                $esBajo = $this->esStockBajo($variante->stock, $producto->stock_inicial, $producto);
                
                // Solo incluir si realmente está en stock crítico o bajo
                return $esCritico || $esBajo;
            })
            ->map(function ($variante) use ($producto) {
            return [
                'variante' => $variante,
                'tipo_alerta' => $this->esStockCritico($variante->stock, $producto->stock_inicial, $producto) ? 'critico' : 'bajo',
                'stock_actual' => $variante->stock,
                    'umbral_stock_critico' => $producto->getAttribute('umbral_stock_critico') ?? $this->calcularStockMinimo($producto->stock_inicial),
                'porcentaje' => $this->calcularPorcentaje($variante->stock, $producto->stock_inicial)
            ];
        });
    }
}
