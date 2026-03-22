<?php

namespace App\Domain\Usuario\Services;

use App\Domain\Shared\Enums\TipoUsuario;

class UsuarioService
{
    public function __construct()
    {
    }

    public function verificarAdministrador(int $tipoUsuario): bool
    {
        return $tipoUsuario === TipoUsuario::ADMIN->value;
    }

    public function verificarCliente(int $tipoUsuario): bool
    {
        return $tipoUsuario === TipoUsuario::CLIENTE->value;
    }
}