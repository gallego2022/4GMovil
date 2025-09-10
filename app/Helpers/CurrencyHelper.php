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
    public static function formatPrice($amount, $currency = null, $fromCurrency = 'COP')
    {
        $currency = $currency ?: self::getCurrentCurrency();
        
        // Convertir el precio si la moneda actual es diferente a la moneda base
        if ($currency !== $fromCurrency) {
            $amount = self::convertPrice($amount, $fromCurrency, $currency);
        }
        
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
            'ARS' => [
                'symbol' => '$',
                'position' => 'before',
                'decimals' => 0,
                'thousands_separator' => '.',
                'decimal_separator' => ','
            ],
            'CLP' => [
                'symbol' => '$',
                'position' => 'before',
                'decimals' => 0,
                'thousands_separator' => '.',
                'decimal_separator' => ','
            ],
            'PEN' => [
                'symbol' => 'S/',
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
            'ARS' => '$',
            'CLP' => '$',
            'PEN' => 'S/',
        ];

        return $symbols[$currency] ?? '$';
    }

    /**
     * Convertir precio entre monedas
     */
    public static function convertPrice($amount, $fromCurrency, $toCurrency)
    {
        // Tasas de cambio actualizadas (diciembre 2024)
        $rates = [
            'COP' => 1,           // Peso colombiano (base)
            'USD' => 0.00025,     // 1 COP = 0.00025 USD (aproximadamente 4000 COP = 1 USD)
            'BRL' => 0.0012,      // 1 COP = 0.0012 BRL (aproximadamente 830 COP = 1 BRL)
            'EUR' => 0.00023,     // 1 COP = 0.00023 EUR (aproximadamente 4350 COP = 1 EUR)
            'MXN' => 0.0045,      // 1 COP = 0.0045 MXN (aproximadamente 220 COP = 1 MXN)
            'ARS' => 0.25,        // 1 COP = 0.25 ARS (aproximadamente 4 COP = 1 ARS)
            'CLP' => 0.4,         // 1 COP = 0.4 CLP (aproximadamente 2.5 COP = 1 CLP)
            'PEN' => 0.0009,      // 1 COP = 0.0009 PEN (aproximadamente 1100 COP = 1 PEN)
        ];

        // Si es la misma moneda, no convertir
        if ($fromCurrency === $toCurrency) {
            return $amount;
        }

        // Validar que las monedas existan
        if (!isset($rates[$fromCurrency]) || !isset($rates[$toCurrency])) {
            \Log::warning("Currency conversion failed: {$fromCurrency} to {$toCurrency}");
            return $amount; // Retornar el monto original si no se puede convertir
        }

        // Convertir a COP primero, luego a la moneda destino
        $amountInCOP = $amount / $rates[$fromCurrency];
        $convertedAmount = $amountInCOP * $rates[$toCurrency];
        
        // Log para debugging
        \Log::info("Currency conversion: {$amount} {$fromCurrency} = {$convertedAmount} {$toCurrency}");
        
        return $convertedAmount;
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
            'ARS' => [
                'name' => 'Peso argentino',
                'symbol' => '$',
                'code' => 'ARS',
                'country' => 'Argentina'
            ],
            'CLP' => [
                'name' => 'Peso chileno',
                'symbol' => '$',
                'code' => 'CLP',
                'country' => 'Chile'
            ],
            'PEN' => [
                'name' => 'Sol peruano',
                'symbol' => 'S/',
                'code' => 'PEN',
                'country' => 'Perú'
            ],
        ];

        return $info[$currency] ?? $info['COP'];
    }
}