<?php

return [

    /*
    |--------------------------------------------------------------------------
    | View Storage Paths
    |--------------------------------------------------------------------------
    |
    | Most templating systems load templates from disk. Here you may specify
    | an array of paths that should be checked for your views. Of course
    | the usual Laravel view path has already been registered for you.
    |
    */

    'paths' => [
        resource_path('views'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Compiled View Path
    |--------------------------------------------------------------------------
    |
    | This option determines where all the compiled Blade templates will be
    | stored for your application. Typically, this is within the storage
    | directory. However, as usual, you are free to change this value.
    |
    */

    'compiled' => env(
        'VIEW_COMPILED_PATH',
        storage_path('framework/views')
    ),

    /*
    |--------------------------------------------------------------------------
    | View Debug Mode
    |--------------------------------------------------------------------------
    |
    | When view debug mode is enabled, the compiled view will be checked
    | on every request to see if the view has expired. If it has, a new
    | compiled view will be generated. This should be disabled in production.
    |
    */

    'debug' => (bool) env('VIEW_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | View Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for view caching. This helps improve performance by
    | caching compiled views. The cache path should be writable.
    |
    */

    'cache' => [
        'enabled' => env('VIEW_CACHE_ENABLED', true),
        'path' => env('VIEW_CACHE_PATH', storage_path('framework/views')),
        'ttl' => env('VIEW_CACHE_TTL', 3600),
    ],

    /*
    |--------------------------------------------------------------------------
    | Blade Directives
    |--------------------------------------------------------------------------
    |
    | Custom Blade directives can be registered here. This allows you to
    | extend Blade with custom functionality.
    |
    */

    'directives' => [
        // Add custom directives here if needed
    ],

    /*
    |--------------------------------------------------------------------------
    | View Composers
    |--------------------------------------------------------------------------
    |
    | View composers allow you to attach data to views automatically.
    | You can register view composers here.
    |
    */

    'composers' => [
        // Add view composers here if needed
    ],

];