<?php

namespace App\Helpers;

use App\Models\LocalizationConfig;

class ViewHelper
{
    /**
     * Obtener configuración de localización actual
     */
    public static function getCurrentConfig($userId = null)
    {
        return LocalizationConfig::getConfigForUser($userId);
    }

    /**
     * Obtener país actual
     */
    public static function getCurrentCountry($userId = null)
    {
        $config = self::getCurrentConfig($userId);
        return $config->country_name;
    }

    /**
     * Obtener idioma actual
     */
    public static function getCurrentLanguage($userId = null)
    {
        $config = self::getCurrentConfig($userId);
        return $config->language_name;
    }

    /**
     * Obtener moneda actual
     */
    public static function getCurrentCurrency($userId = null)
    {
        $config = self::getCurrentConfig($userId);
        return $config->currency_name;
    }

    /**
     * Obtener código de moneda actual
     */
    public static function getCurrentCurrencyCode($userId = null)
    {
        $config = self::getCurrentConfig($userId);
        return $config->currency_code;
    }

    /**
     * Formatear precio con la moneda actual
     */
    public static function formatPrice($amount, $userId = null)
    {
        return CurrencyHelper::formatPrice($amount);
    }

    /**
     * Formatear fecha con la configuración actual
     */
    public static function formatDate($date, $userId = null)
    {
        return DateHelper::formatDate($date, null, $userId);
    }

    /**
     * Obtener bandera del país actual
     */
    public static function getCurrentFlag($userId = null)
    {
        $config = self::getCurrentConfig($userId);
        
        $flags = [
            'CO' => '🇨🇴',
            'MX' => '🇲🇽',
            'AR' => '🇦🇷',
            'CL' => '🇨🇱',
            'PE' => '🇵🇪',
            'VE' => '🇻🇪',
            'EC' => '🇪🇨',
            'BO' => '🇧🇴',
            'UY' => '🇺🇾',
            'PY' => '🇵🇾',
            'ES' => '🇪🇸',
            'US' => '🇺🇸',
        ];

        return $flags[$config->country_code] ?? '🇨🇴';
    }

    /**
     * Aplicar configuración de localización
     */
    public static function applyLocalization()
    {
        $locale = session('locale', 'es');
        $currency = session('currency', 'COP');
        $country = session('country', 'CO');
        
        app()->setLocale($locale);
        
        return [
            'locale' => $locale,
            'currency' => $currency,
            'country' => $country
        ];
    }
}
