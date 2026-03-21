<?php

namespace App\Providers;

use App\Domain\Produto\IProdutoRepository;
use App\Domain\Usuario\IUsuarioRepository;
use App\Infrastructure\Repositories\ProdutoRepository;
use App\Infrastructure\Repositories\UsuarioRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(IUsuarioRepository::class, UsuarioRepository::class);
        $this->app->bind(IProdutoRepository::class, ProdutoRepository::class);
    }

    public function boot(): void
    {
        //
    }
}
