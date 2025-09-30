<?php

namespace App\Http\Controllers\Publico;

use App\Http\Controllers\Base\WebController;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Collection;

class SearchController extends WebController
{
    /**
     * Búsqueda unificada de productos y páginas estáticas.
     */
    public function index(Request $request)
    {
        $this->applyLocalization();

        $q = trim((string) $request->get('q', ''));

        // Productos (nombre, categoría, marca)
        $productos = Collection::make();
        if ($q !== '') {
            $productos = Producto::with(['categoria', 'marca', 'imagenes'])
                ->where(function ($query) use ($q) {
                    $query->where('nombre_producto', 'like', "%{$q}%")
                          ->orWhereHas('categoria', function ($sub) use ($q) {
                              $sub->where('nombre', 'like', "%{$q}%");
                          })
                          ->orWhereHas('marca', function ($sub) use ($q) {
                              $sub->where('nombre', 'like', "%{$q}%");
                          });
                })
                ->where('stock', '>', 0)
                ->orderBy('created_at', 'desc')
                ->paginate(12)
                ->appends($request->query());
        }

        // Páginas estáticas conocidas (con palabras clave) - solo incluir si la ruta existe
        $pages = Collection::make([
            [ 'title' => 'Inicio', 'route' => 'landing', 'keywords' => ['home', 'principal', 'inicio'] ],
            [ 'title' => 'Catálogo de Productos', 'route' => 'productos.lista', 'keywords' => ['catalogo', 'productos', 'tienda', 'catalogo completo'] ],
            [ 'title' => 'Nosotros', 'route' => 'nosotros', 'keywords' => ['about', 'quienes somos', 'nosotros', 'empresa'] ],
            [ 'title' => 'Contáctanos', 'route' => 'contactanos', 'keywords' => ['contacto', 'contactanos', 'ayuda', 'soporte', 'contactar'] ],
            [ 'title' => 'Servicio Técnico', 'route' => 'servicio-tecnico', 'keywords' => ['servicio', 'tecnico', 'reparacion', 'soporte', 'garantia'] ],
            [ 'title' => 'Servicios', 'route' => 'servicios', 'keywords' => ['servicios', 'ofertas', 'soporte'] ],
            [ 'title' => 'Iniciar Sesión', 'route' => 'login', 'keywords' => ['login', 'ingresar', 'entrar', 'acceder'] ],
            [ 'title' => 'Recuperar Contraseña', 'route' => 'password.request', 'keywords' => ['password', 'contraseña', 'recuperar', 'reset'] ],
            [ 'title' => 'Verificación OTP', 'route' => 'otp.verify.form', 'keywords' => ['otp', 'verificacion', 'codigo'] ],
            [ 'title' => 'Resultados de Búsqueda', 'route' => 'buscar', 'keywords' => ['buscar', 'resultados'] ],
        ])->filter(function($p){
            return Route::has($p['route']);
        })->values();

        $qNorm = Str::lower(Str::ascii($q));
        $paginas = Collection::make($pages)->filter(function ($page) use ($qNorm) {
            if ($qNorm === '') { return false; }
            $haystack = Str::lower(Str::ascii($page['title'] . ' ' . implode(' ', $page['keywords'] ?? [])));
            return Str::contains($haystack, $qNorm);
        })->map(function($page){
            return [ 'title' => $page['title'], 'route' => $page['route'] ];
        })->values();

        return View::make('pages.search.results', [
            'q' => $q,
            'productos' => $productos,
            'paginas' => $paginas,
        ]);
    }

    /**
     * Sugerencias en tiempo real (JSON) para autocomplete.
     */
    public function sugerencias(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        if ($q === '') {
            return Response::json([
                'productos' => [],
                'paginas' => [],
            ]);
        }

        $productos = Producto::select(['producto_id', 'nombre_producto', 'precio'])
            ->with([
                'marca:marca_id,nombre',
                'categoria:categoria_id,nombre',
                'imagenes' => function($q){
                    $q->select('imagen_id','producto_id','ruta_imagen','principal','activo','orden')
                      ->where('activo', true)
                      ->orderBy('principal', 'desc')
                      ->orderBy('orden', 'asc')
                      ->limit(1);
                }
            ])
            ->where(function ($query) use ($q) {
                $query->where('nombre_producto', 'like', "%{$q}%")
                      ->orWhereHas('categoria', function ($sub) use ($q) { $sub->where('nombre', 'like', "%{$q}%"); })
                      ->orWhereHas('marca', function ($sub) use ($q) { $sub->where('nombre', 'like', "%{$q}%"); });
            })
            ->where('stock', '>', 0)
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get()
            ->map(function($p){
                $imagen = optional($p->imagenes->first());
                $imagenUrl = $imagen ? $imagen->url_completa : null;
                return [
                    'id' => $p->producto_id,
                    'nombre' => $p->nombre_producto,
                    'precio' => $p->precio,
                    'marca' => optional($p->marca)->nombre,
                    'categoria' => optional($p->categoria)->nombre,
                    'imagen' => $imagenUrl,
                    'url' => route('productos.show', ['producto' => $p->producto_id]),
                ];
            });

        $pages = Collection::make([
            [ 'title' => 'Inicio', 'route' => 'landing', 'keywords' => ['home', 'principal', 'inicio'] ],
            [ 'title' => 'Catálogo de Productos', 'route' => 'productos.lista', 'keywords' => ['catalogo', 'productos', 'tienda', 'catalogo completo'] ],
            [ 'title' => 'Nosotros', 'route' => 'nosotros', 'keywords' => ['about', 'quienes somos', 'nosotros', 'empresa'] ],
            [ 'title' => 'Contáctanos', 'route' => 'contactanos', 'keywords' => ['contacto', 'contactanos', 'ayuda', 'soporte', 'contactar'] ],
            [ 'title' => 'Servicio Técnico', 'route' => 'servicio-tecnico', 'keywords' => ['servicio', 'tecnico', 'reparacion', 'soporte', 'garantia'] ],
            [ 'title' => 'Servicios', 'route' => 'servicios', 'keywords' => ['servicios', 'ofertas', 'soporte'] ],
            [ 'title' => 'Iniciar Sesión', 'route' => 'login', 'keywords' => ['login', 'ingresar', 'entrar', 'acceder'] ],
            [ 'title' => 'Recuperar Contraseña', 'route' => 'password.request', 'keywords' => ['password', 'contraseña', 'recuperar', 'reset'] ],
            [ 'title' => 'Verificación OTP', 'route' => 'otp.verify.form', 'keywords' => ['otp', 'verificacion', 'codigo'] ],
            [ 'title' => 'Resultados de Búsqueda', 'route' => 'buscar', 'keywords' => ['buscar', 'resultados'] ],
        ])->filter(function($p){
            return Route::has($p['route']);
        })->values();

        $qNorm = Str::lower(Str::ascii($q));
        $paginas = Collection::make($pages)
            ->filter(function ($page) use ($qNorm) {
                $haystack = Str::lower(Str::ascii($page['title'] . ' ' . implode(' ', $page['keywords'] ?? [])));
                return Str::contains($haystack, $qNorm);
            })
            ->take(5)
            ->map(function($page){
                return [
                    'title' => $page['title'],
                    'url' => route($page['route'])
                ];
            })
            ->values();

        return response()->json([
            'productos' => $productos,
            'paginas' => $paginas,
        ]);
    }
}


