<?php

namespace App\Helpers;

use App\Models\LocalizationConfig;
use Carbon\Carbon;

class DateHelper
{
    /**
     * Formatear fecha según la configuración del usuario
     */
    public static function formatDate($date, $format = null, $userId = null)
    {
        if (!$date) return '';

        $carbon = $date instanceof Carbon ? $date : Carbon::parse($date);
        
        if (!$format) {
            $config = LocalizationConfig::getConfigForUser($userId);
            $format = $config->date_format;
        }

        return $carbon->format($format);
    }

    /**
     * Formatear fecha y hora según la configuración del usuario
     */
    public static function formatDateTime($date, $userId = null)
    {
        if (!$date) return '';

        $carbon = $date instanceof Carbon ? $date : Carbon::parse($date);
        $config = LocalizationConfig::getConfigForUser($userId);
        
        $dateFormat = $config->date_format;
        $timeFormat = $config->time_format;
        
        return $carbon->format($dateFormat . ' ' . $timeFormat);
    }

    /**
     * Obtener zona horaria del usuario
     */
    public static function getUserTimezone($userId = null)
    {
        $config = LocalizationConfig::getConfigForUser($userId);
        return $config->timezone;
    }

    /**
     * Convertir fecha a zona horaria del usuario
     */
    public static function toUserTimezone($date, $userId = null)
    {
        if (!$date) return null;

        $carbon = $date instanceof Carbon ? $date : Carbon::parse($date);
        $timezone = self::getUserTimezone($userId);
        
        return $carbon->setTimezone($timezone);
    }

    /**
     * Formatear fecha relativa (hace 2 horas, etc.)
     */
    public static function formatRelative($date, $userId = null)
    {
        if (!$date) return '';

        $carbon = $date instanceof Carbon ? $date : Carbon::parse($date);
        $timezone = self::getUserTimezone($userId);
        
        return $carbon->setTimezone($timezone)->diffForHumans();
    }
}
