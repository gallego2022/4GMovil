<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Session;

class CurrencyHelper
{
    /**
     * Obtener la moneda actual de la sesión
     */
    public static function getCurrentCurrency()
    {
        return Session::get('currency', 'COP');
    }

    /**
     * Obtener el país actual de la sesión
     */
    public static function getCurrentCountry()
    {
        return Session::get('country', 'CO');
    }

    /**
     * Formatear precio según la moneda actual
     */
    public static function formatPrice($amount, $currency = null)
    {
        $currency = $currency ?: self::getCurrentCurrency();
        
        $formats = [
            'COP' => [
                'symbol' => '$',
                'position' => 'before',
                'decimals' => 0,
                'thousands_separator' => '.',
                'decimal_separator' => ','
            ],
            'USD' => [
                'symbol' => '$',
                'position' => 'before',
                'decimals' => 2,
                'thousands_separator' => ',',
                'decimal_separator' => '.'
            ],
            'BRL' => [
                'symbol' => 'R$',
                'position' => 'before',
                'decimals' => 2,
                'thousands_separator' => '.',
                'decimal_separator' => ','
            ],
            'EUR' => [
                'symbol' => '€',
                'position' => 'after',
                'decimals' => 2,
                'thousands_separator' => '.',
                'decimal_separator' => ','
            ],
            'MXN' => [
                'symbol' => '$',
                'position' => 'before',
                'decimals' => 2,
                'thousands_separator' => ',',
                'decimal_separator' => '.'
            ],
        ];

        $format = $formats[$currency] ?? $formats['COP'];
        
        $formattedAmount = number_format(
            $amount,
            $format['decimals'],
            $format['decimal_separator'],
            $format['thousands_separator']
        );

        if ($format['position'] === 'before') {
            return $format['symbol'] . ' ' . $formattedAmount;
        } else {
            return $formattedAmount . ' ' . $format['symbol'];
        }
    }

    /**
     * Obtener el símbolo de la moneda actual
     */
    public static function getCurrencySymbol($currency = null)
    {
        $currency = $currency ?: self::getCurrentCurrency();
        
        $symbols = [
            'COP' => '$',
            'USD' => '$',
            'BRL' => 'R$',
            'EUR' => '€',
            'MXN' => '$',
        ];

        return $symbols[$currency] ?? '$';
    }

    /**
     * Convertir precio entre monedas (ejemplo básico)
     */
    public static function convertPrice($amount, $fromCurrency, $toCurrency)
    {
        // Tasas de cambio básicas (en un proyecto real, esto vendría de una API)
        $rates = [
            'COP' => 1,
            'USD' => 0.00025,
            'BRL' => 0.0012,
            'EUR' => 0.00023,
            'MXN' => 0.0045,
        ];

        $amountInCOP = $amount / ($rates[$fromCurrency] ?? 1);
        return $amountInCOP * ($rates[$toCurrency] ?? 1);
    }

    /**
     * Obtener información de la moneda actual
     */
    public static function getCurrencyInfo($currency = null)
    {
        $currency = $currency ?: self::getCurrentCurrency();
        
        $info = [
            'COP' => [
                'name' => 'Peso colombiano',
                'symbol' => '$',
                'code' => 'COP',
                'country' => 'Colombia'
            ],
            'USD' => [
                'name' => 'Dólar estadounidense',
                'symbol' => '$',
                'code' => 'USD',
                'country' => 'Estados Unidos'
            ],
            'BRL' => [
                'name' => 'Real brasileño',
                'symbol' => 'R$',
                'code' => 'BRL',
                'country' => 'Brasil'
            ],
            'EUR' => [
                'name' => 'Euro',
                'symbol' => '€',
                'code' => 'EUR',
                'country' => 'Europa'
            ],
            'MXN' => [
                'name' => 'Peso mexicano',
                'symbol' => '$',
                'code' => 'MXN',
                'country' => 'México'
            ],
        ];

        return $info[$currency] ?? $info['COP'];
    }
}