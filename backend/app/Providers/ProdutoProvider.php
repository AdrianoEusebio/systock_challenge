<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Domain\Produto\IProdutoRepository;
use App\Infrastructure\Repositories\ProdutoRepository;
use App\Domain\Usuario\IUsuarioRepository;

class ProdutoProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(IProdutoRepository::class, ProdutoRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
