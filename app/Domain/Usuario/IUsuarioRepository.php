<?php

namespace App\Domain\Usuario;

use App\Domain\Shared\IRepository;
use App\Domain\Usuario\Models\UsuarioEntity;

interface IUsuarioRepository extends IRepository
{
    /**
     * @param UsuarioEntity $entity
     * @return UsuarioEntity
     */
    public function create(UsuarioEntity $entity): UsuarioEntity;

    /**
     * @param int $id
     * @param UsuarioEntity $entity
     * @return UsuarioEntity
     */
    public function update(int $id, UsuarioEntity $entity): UsuarioEntity;
}