<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocalizationConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'country_code',
        'language_code',
        'currency_code',
        'timezone',
        'date_format',
        'time_format',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relación con usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Configuraciones por defecto
    public static function getDefaultConfig()
    {
        return [
            'country_code' => 'CO',
            'language_code' => 'es',
            'currency_code' => 'COP',
            'timezone' => 'America/Bogota',
            'date_format' => 'd/m/Y',
            'time_format' => 'H:i',
        ];
    }

    // Obtener configuración del usuario o crear una por defecto
    public static function getConfigForUser($userId = null)
    {
        if ($userId) {
            $config = self::where('user_id', $userId)->first();
            if ($config) {
                return $config;
            }
        }

        // Crear configuración por defecto
        return new self(self::getDefaultConfig());
    }

    // Obtener país actual
    public function getCountryNameAttribute()
    {
        $countries = [
            'CO' => 'Colombia',
            'MX' => 'México',
            'AR' => 'Argentina',
            'CL' => 'Chile',
            'PE' => 'Perú',
            'VE' => 'Venezuela',
            'EC' => 'Ecuador',
            'BO' => 'Bolivia',
            'UY' => 'Uruguay',
            'PY' => 'Paraguay',
            'ES' => 'España',
            'US' => 'Estados Unidos',
        ];

        return $countries[$this->country_code] ?? 'Colombia';
    }

    // Obtener idioma actual
    public function getLanguageNameAttribute()
    {
        $languages = [
            'es' => 'Español Latinoamericano',
            'es-ES' => 'Español (España)',
            'en' => 'English',
            'pt' => 'Português',
        ];

        return $languages[$this->language_code] ?? 'Español Latinoamericano';
    }

    // Obtener moneda actual
    public function getCurrencyNameAttribute()
    {
        $currencies = [
            'COP' => 'Peso colombiano (COP)',
            'MXN' => 'Peso mexicano (MXN)',
            'ARS' => 'Peso argentino (ARS)',
            'CLP' => 'Peso chileno (CLP)',
            'PEN' => 'Sol peruano (PEN)',
            'VES' => 'Bolívar venezolano (VES)',
            'USD' => 'Dólar estadounidense (USD)',
            'EUR' => 'Euro (EUR)',
        ];

        return $currencies[$this->currency_code] ?? 'Peso colombiano (COP)';
    }
}