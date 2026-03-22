<?php

namespace App\Domain\Produto;

use App\Domain\Shared\IRepository;
use App\Domain\Produto\Models\ProdutoEntity;

interface IProdutoRepository extends IRepository
{
    public function create(ProdutoEntity $entity): ProdutoEntity;
    public function update(int $id, ProdutoEntity $entity): ProdutoEntity;
    public function findByUsuarioId(int $usuarioId): array;
}