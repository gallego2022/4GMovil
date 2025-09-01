<?php

namespace App\Helpers;

class PhotoHelper
{
    /**
     * Verifica si una URL de foto es de Google
     */
    public static function isGooglePhotoUrl($url)
    {
        if (!$url) return false;
        
        return str_contains($url, 'googleusercontent.com') || 
               str_contains($url, 'lh3.googleusercontent.com') ||
               str_contains($url, 'lh4.googleusercontent.com') ||
               str_contains($url, 'lh5.googleusercontent.com') ||
               str_contains($url, 'lh6.googleusercontent.com');
    }

    /**
     * Verifica si una URL es externa (no local)
     */
    public static function isExternalUrl($url)
    {
        if (!$url) return false;
        
        return filter_var($url, FILTER_VALIDATE_URL) && 
               !str_starts_with($url, asset(''));
    }

    /**
     * Obtiene la URL correcta para mostrar la foto de perfil
     */
    public static function getPhotoUrl($photoPath)
    {
        if (!$photoPath) return null;
        
        // Si es una URL externa (Google, Facebook, etc.), devolver directamente
        if (self::isExternalUrl($photoPath)) {
            return $photoPath;
        }
        
        // Si es un archivo local, usar asset
        return asset('storage/' . $photoPath);
    }

    /**
     * Verifica si la foto es una URL de Google
     */
    public static function isGooglePhoto($url)
    {
        return self::isGooglePhotoUrl($url);
    }

    /**
     * Verifica si una foto puede ser eliminada (no es externa)
     */
    public static function canDeletePhoto($photoPath)
    {
        return !self::isExternalUrl($photoPath);
    }

    /**
     * Obtiene el tipo de foto (local o externa)
     */
    public static function getPhotoType($photoPath)
    {
        if (!$photoPath) return 'none';
        
        if (self::isExternalUrl($photoPath)) {
            if (self::isGooglePhotoUrl($photoPath)) {
                return 'google';
            }
            return 'external';
        }
        
        return 'local';
    }

    /**
     * Limpia la URL de Google para obtener una versión más estable
     */
    public static function cleanGooglePhotoUrl($url)
    {
        if (!self::isGooglePhotoUrl($url)) {
            return $url;
        }
        
        // Remover parámetros de tamaño para obtener la imagen original
        $url = preg_replace('/\?.*$/', '', $url);
        return $url;
    }
}
