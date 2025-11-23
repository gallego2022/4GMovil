<?php

namespace App\Http\Controllers\Servicios;

use App\Http\Controllers\Base\WebController;
use App\Models\LocalizationConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Redirect;

class LocalizationController extends WebController
{
    /**
     * Mostrar el modal de configuración de localización
     */
    public function showConfigModal()
    {
        $config = LocalizationConfig::getConfigForUser(Auth::id());
        
        $countries = [
            'CO' => ['name' => 'Colombia', 'flag' => '🇨🇴'],
            'US' => ['name' => 'Estados Unidos', 'flag' => '🇺🇸'],
            'BR' => ['name' => 'Brasil', 'flag' => '🇧🇷'],
            'ES' => ['name' => 'España', 'flag' => '🇪🇸'],
        ];

        $languages = [
            'es' => 'Español',
            'en' => 'English',
            'pt' => 'Português',
        ];

        $currencies = [
            'COP' => 'Peso colombiano (COP)',
            'USD' => 'Dólar estadounidense (USD)',
            'BRL' => 'Real brasileño (BRL)',
            'EUR' => 'Euro (EUR)',
        ];

        return Response::json([
            'config' => $config,
            'countries' => $countries,
            'languages' => $languages,
            'currencies' => $currencies,
        ]);
    }

    /**
     * Guardar la configuración de localización
     */
    public function saveConfig(Request $request)
    {
        $request->validate([
            'country_code' => 'nullable|string|max:2',
            'language_code' => 'required|string|max:5',
            'currency_code' => 'required|string|max:3',
        ]);

        try {
            // Inferir país si no llega y forzar EUR si es España
            $languageConfigs = [
                'es' => ['country' => 'CO', 'currency' => 'COP'],
                'en' => ['country' => 'US', 'currency' => 'USD'],
                'pt' => ['country' => 'BR', 'currency' => 'BRL'],
            ];

            $countryCode = strtoupper($request->country_code ?? Session::get('country') ?? ($languageConfigs[$request->language_code]['country'] ?? 'CO'));

            if ($countryCode === 'ES') {
                $request->merge(['currency_code' => 'EUR']);
            }

            // Establecer locale en la sesión directamente
            App::setLocale($request->language_code);
            Session::put('locale', $request->language_code);
            Session::put('country', $countryCode);
            Session::put('currency', $request->currency_code);

            // Si el usuario está autenticado, guardar en la base de datos
            if (Auth::check()) {
                $userId = Auth::id();
                $config = LocalizationConfig::where('usuario_id', $userId)->first();
                
                if (!$config) {
                    $config = new LocalizationConfig();
                    $config->usuario_id = $userId;
                }

                $config->country_code = $countryCode;
                $config->language_code = $request->language_code;
                $config->currency_code = $request->currency_code;
                $config->save();
            }

            return Response::json([
                'success' => true,
                'message' => __('messages.messages.save_success'),
                'config' => [
                    'country_code' => $request->country_code,
                    'language_code' => $request->language_code,
                    'currency_code' => $request->currency_code,
                ]
            ]);

        } catch (\Exception $e) {
            return Response::json([
                'success' => false,
                'message' => __('messages.messages.save_error'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cambiar idioma rápidamente
     */
    public function changeLanguage($language)
    {
        $allowedLanguages = ['es', 'en', 'pt'];
        
        if (in_array($language, $allowedLanguages)) {
            // Configuraciones por idioma
            $languageConfigs = [
                'es' => ['country' => 'CO', 'currency' => 'COP'],
                'en' => ['country' => 'US', 'currency' => 'USD'],
                'pt' => ['country' => 'BR', 'currency' => 'BRL'],
            ];
            
            $config = $languageConfigs[$language] ?? $languageConfigs['es'];

            // Si la sesión indica España, forzar EUR
            $sessionCountry = Session::get('country');
            if ($language === 'es' && $sessionCountry === 'ES') {
                $config = ['country' => 'ES', 'currency' => 'EUR'];
            }
            
            // Establecer locale
            App::setLocale($language);
            Session::put('locale', $language);
            Session::put('country', $config['country']);
            Session::put('currency', $config['currency']);
            
            // Actualizar configuración del usuario si está autenticado
            if (Auth::check()) {
                $userConfig = LocalizationConfig::where('usuario_id', Auth::id())->first();
                if (!$userConfig) {
                    $userConfig = new LocalizationConfig();
                    $userConfig->usuario_id = Auth::id();
                }
                
                $userConfig->language_code = $language;
                $userConfig->country_code = $config['country'];
                $userConfig->currency_code = $config['currency'];
                $userConfig->save();
            }
        }

        return Redirect::back();
    }

    /**
     * Obtener configuración actual
     */
    public function getCurrentConfig()
    {
        $userId = Auth::id();
        $config = LocalizationConfig::getConfigForUser($userId);
        
        // Si no hay configuración, usar valores por defecto de la sesión
        if (!$config->exists) {
            $config = (object) [
                'country_code' => Session::get('country', 'CO'),
                'language_code' => Session::get('locale', 'es'),
                'currency_code' => Session::get('currency', 'COP'),
            ];
        }
        
        return Response::json([
            'config' => $config,
            'current_locale' => App::getLocale(),
            'session_locale' => Session::get('locale'),
        ]);
    }
}
