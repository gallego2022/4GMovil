<?php

namespace App\Services;

use App\Models\Producto;
use App\Models\VarianteProducto;
use App\Services\RedisCacheService;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class OptimizedStockAlertService
{
    protected $cacheService;

    public function __construct(RedisCacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Obtiene alertas de stock optimizadas agrupando variantes por producto
     */
    public function getOptimizedStockAlerts(?string $tipo = null, int $page = 1): array
    {
        try {
            // Obtener estadísticas generales (sin paginación)
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
            ->filter(function ($producto) {
                // Si el producto tiene variantes, verificar si todas están críticas
                if ($producto->variantes->isNotEmpty()) {
                    return $this->tieneVariantesCriticas($producto);
                }
                
                // Si no tiene variantes, verificar stock del producto principal
                return $this->esStockCritico($producto->stock, $producto->stock_inicial, $producto);
            })
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
            ->filter(function ($producto) {
                // Excluir productos que ya están en stock crítico
                if ($producto->variantes->isNotEmpty()) {
                    if ($this->tieneVariantesCriticas($producto)) {
                        return false;
                    }
                    return $this->tieneVariantesStockBajo($producto);
                }
                
                // Si no tiene variantes, verificar que no esté crítico y que esté bajo
                if ($this->esStockCritico($producto->stock, $producto->stock_inicial, $producto)) {
                    return false;
                }
                
                return $this->esStockBajo($producto->stock, $producto->stock_inicial, $producto);
            })
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
                    'stock_minimo' => $variante->producto ? $this->calcularStockMinimo($variante->producto->stock_inicial) : 0,
                    'porcentaje' => 0
                ];
            });
    }

    /**
     * Verifica si un producto tiene variantes con stock crítico
     */
    private function tieneVariantesCriticas(Producto $producto): bool
    {
        return $producto->variantes->filter(function ($variante) use ($producto) {
            return $this->esStockCritico($variante->stock, $producto->stock_inicial, $producto);
        })->isNotEmpty();
    }

    /**
     * Verifica si un producto tiene variantes con stock bajo
     */
    private function tieneVariantesStockBajo(Producto $producto): bool
    {
        return $producto->variantes->filter(function ($variante) use ($producto) {
            return $this->esStockBajo($variante->stock, $producto->stock_inicial, $producto);
        })->isNotEmpty();
    }

    /**
     * Verifica si el stock es crítico
     * Usa stock_minimo del producto si existe, sino calcula el 20% del stock_inicial
     */
    private function esStockCritico(int $stock, ?int $stockInicial, ?Producto $producto = null): bool
    {
        // Si hay producto, usar su umbral crítico (stock_minimo)
        if ($producto && $producto->stock_minimo !== null && $producto->stock_minimo > 0) {
            return $stock < $producto->stock_minimo && $stock > 0;
        }
        
        // Fallback: calcular basado en stock_inicial
        if ($stockInicial === null || $stockInicial <= 0) {
            return $stock < 5 && $stock > 0;
        }
        
        $umbralCritico = (int) ceil(($stockInicial * 20) / 100);
        return $stock < $umbralCritico && $stock > 0;
    }

    /**
     * Verifica si el stock está bajo
     * Solo muestra alerta si el stock está por debajo del stock_minimo pero por encima de 0
     * Si el stock está por encima del stock_minimo, no se muestra ninguna alerta
     */
    private function esStockBajo(int $stock, ?int $stockInicial, ?Producto $producto = null): bool
    {
        // Obtener el umbral crítico (stock_minimo)
        $umbralCritico = null;
        
        if ($producto && $producto->stock_minimo !== null && $producto->stock_minimo > 0) {
            $umbralCritico = $producto->stock_minimo;
        } elseif ($stockInicial !== null && $stockInicial > 0) {
            $umbralCritico = (int) ceil(($stockInicial * 20) / 100);
        } else {
            $umbralCritico = 5; // Fallback
        }
        
        // Si hay producto con stock_maximo, usar como umbral bajo
        // Pero solo si el stock está por debajo del stock_minimo
        if ($producto && $producto->stock_maximo !== null && $producto->stock_maximo > 0) {
            // Stock bajo: está por encima del crítico pero por debajo del máximo
            // PERO solo si está por debajo del mínimo (no por encima)
            if ($stock >= $umbralCritico) {
                return false; // Si está por encima o igual al mínimo, no mostrar alerta
            }
            return $stock < $producto->stock_maximo && $stock > 0;
        }
        
        // Fallback: calcular basado en stock_inicial
        if ($stockInicial === null || $stockInicial <= 0) {
            // Solo mostrar si está por debajo del umbral crítico (5)
            return $stock < 5 && $stock > 0;
        }
        
        $umbralBajo = (int) ceil(($stockInicial * 60) / 100);
        $umbralCritico = (int) ceil(($stockInicial * 20) / 100);
        
        // Solo mostrar si está por debajo del umbral crítico
        if ($stock >= $umbralCritico) {
            return false; // Si está por encima o igual al mínimo, no mostrar alerta
        }
        
        return $stock < $umbralBajo && $stock > 0;
    }

    /**
     * Enriquece un producto con información de alertas
     */
    private function enriquecerProductoConAlertas(Producto $producto): array
    {
        $variantesConProblemas = $producto->variantes->filter(function ($variante) use ($producto) {
            return $this->esStockCritico($variante->stock, $producto->stock_inicial, $producto) ||
                   $this->esStockBajo($variante->stock, $producto->stock_inicial, $producto);
        });

        return [
            'producto' => $producto,
            'variantes_problematicas' => $variantesConProblemas,
            'total_variantes_problematicas' => $variantesConProblemas->count(),
            'tipo_alerta' => $this->determinarTipoAlerta($producto, $variantesConProblemas),
            'stock_actual' => $producto->stock,
            'stock_minimo' => $producto->stock_minimo ?? $this->calcularStockMinimo($producto->stock_inicial),
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
     */
    public function getVariantesProblematicas(int $productoId): Collection
    {
        $producto = Producto::with(['variantes', 'categoria', 'marca'])->findOrFail($productoId);
        
        return $producto->variantes->filter(function ($variante) use ($producto) {
            return $this->esStockCritico($variante->stock, $producto->stock_inicial, $producto) ||
                   $this->esStockBajo($variante->stock, $producto->stock_inicial, $producto);
        })->map(function ($variante) use ($producto) {
            return [
                'variante' => $variante,
                'tipo_alerta' => $this->esStockCritico($variante->stock, $producto->stock_inicial, $producto) ? 'critico' : 'bajo',
                'stock_actual' => $variante->stock,
                'stock_minimo' => $producto->stock_minimo ?? $this->calcularStockMinimo($producto->stock_inicial),
                'porcentaje' => $this->calcularPorcentaje($variante->stock, $producto->stock_inicial)
            ];
        });
    }
}
