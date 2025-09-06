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
        
        \Log::info('SetLocale Middleware - Valores de sesión:', [
            'locale' => $locale,
            'currency' => $currency,
            'country' => $country
        ]);
        
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

        // Si no hay idioma configurado, usar el por defecto
        if (!$locale) {
            $locale = 'es';
            $currency = 'COP';
            $country = 'CO';
            Session::put('locale', $locale);
            Session::put('currency', $currency);
            Session::put('country', $country);
        }

        // Establecer el locale
        App::setLocale($locale);
        
        // Asegurar que la sesión se guarde
        Session::save();
        
        \Log::info('SetLocale Middleware - Locale establecido:', [
            'final_locale' => App::getLocale(),
            'session_locale' => Session::get('locale'),
            'session_currency' => Session::get('currency')
        ]);

        return $next($request);
    }
}
