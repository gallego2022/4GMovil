<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\ProductoRepositoryInterface;
use App\Repositories\ProductoRepository;
use App\Interfaces\UsuarioRepositoryInterface;
use App\Repositories\UsuarioRepository;
use App\Interfaces\CategoriaRepositoryInterface;
use App\Repositories\CategoriaRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(ProductoRepositoryInterface::class, ProductoRepository::class);
        $this->app->bind(UsuarioRepositoryInterface::class, UsuarioRepository::class);
        $this->app->bind(CategoriaRepositoryInterface::class, CategoriaRepository::class);
    }

    public function boot()
    {
        //
    }
} 