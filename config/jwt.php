<?php

return [
    /*
    |--------------------------------------------------------------------------
    | JWT Secret Key
    |--------------------------------------------------------------------------
    |
    | Esta es la clave secreta utilizada para firmar los tokens JWT.
    | Por defecto usa APP_KEY, pero puedes configurar JWT_SECRET en .env
    |
    */

    'secret' => env('JWT_SECRET', env('APP_KEY')),

    /*
    |--------------------------------------------------------------------------
    | JWT Expiration Time
    |--------------------------------------------------------------------------
    |
    | Tiempo de expiración del token en segundos.
    | Por defecto: 3600 segundos (1 hora)
    |
    */

    'expiration' => env('JWT_EXPIRATION', 3600),

    /*
    |--------------------------------------------------------------------------
    | JWT Algorithm
    |--------------------------------------------------------------------------
    |
    | Algoritmo de encriptación utilizado para firmar los tokens.
    | Opciones: HS256, HS384, HS512, RS256, RS384, RS512
    |
    */

    'algorithm' => env('JWT_ALGORITHM', 'HS256'),

    /*
    |--------------------------------------------------------------------------
    | JWT Issuer
    |--------------------------------------------------------------------------
    |
    | Identificador del emisor del token (iss claim)
    |
    */

    'issuer' => env('JWT_ISSUER', env('APP_URL', 'http://localhost')),

    /*
    |--------------------------------------------------------------------------
    | JWT Audience
    |--------------------------------------------------------------------------
    |
    | Identificador del destinatario del token (aud claim)
    |
    */

    'audience' => env('JWT_AUDIENCE', env('APP_URL', 'http://localhost')),
];

