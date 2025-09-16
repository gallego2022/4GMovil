<?php

namespace App\Http\Middleware;

use App\Models\LocalizationConfig;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Obtener idioma de la sesión o configuración del usuario
        $locale = Session::get('locale');
        $currency = Session::get('currency');
        $country = Session::get('country');
        
        // Si no hay configuración en sesión y el usuario está autenticado, cargar desde BD
        if (!$locale && auth()->check()) {
            $config = LocalizationConfig::where('user_id', auth()->id())->first();
            if ($config) {
                $locale = $config->language_code;
                $currency = $config->currency_code;
                $country = $config->country_code;
                Session::put('locale', $locale);
                Session::put('currency', $currency);
                Session::put('country', $country);
            }
        }

        // Si no hay idioma configurado, intentar auto-detectar por encabezados y región
        if (!$locale) {
            // 1) Detectar por Accept-Language (solo permitir es, en, pt)
            $preferred = $request->getPreferredLanguage(['es', 'en', 'pt']);
            $detectedLocale = $preferred ?: 'es';

            // 1.1) Si viene variante 'es-ES' en los encabezados, priorizar España/EUR
            $acceptLanguage = (string) $request->header('Accept-Language', '');
            $isSpainSpanish = stripos($acceptLanguage, 'es-es') !== false;

            // 2) Mapear país/moneda por idioma cuando no tenemos IP/geo
            // Nota: se permite solo COP, USD, BRL, EUR
            $languageConfigs = [
                'es' => ['country' => 'CO', 'currency' => 'COP'],
                'en' => ['country' => 'US', 'currency' => 'USD'],
                'pt' => ['country' => 'BR', 'currency' => 'BRL'],
            ];

            $locale = $detectedLocale;
            // Si detectamos español de España, forzar ES/EUR
            if ($isSpainSpanish) {
                $country = 'ES';
                $currency = 'EUR';
            } else {
                $country = $languageConfigs[$locale]['country'] ?? 'CO';
                $currency = $languageConfigs[$locale]['currency'] ?? 'COP';
            }

            Session::put('locale', $locale);
            Session::put('currency', $currency);
            Session::put('country', $country);
        }

        // Establecer el locale
        App::setLocale($locale);
        
        // Asegurar que la sesión se guarde
        Session::save();
        
        // Log solo en desarrollo
        if (config('app.debug')) {
            \Log::info('SetLocale Middleware - Configuración aplicada:', [
                'final_locale' => App::getLocale(),
                'session_locale' => Session::get('locale'),
                'session_currency' => Session::get('currency'),
                'session_country' => Session::get('country')
            ]);
        }

        return $next($request);
    }
}
