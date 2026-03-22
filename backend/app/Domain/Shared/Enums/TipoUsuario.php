<?php

namespace App\Domain\Shared\Enums;

enum TipoUsuario: int
{
    case ADMIN = 1;
    case CLIENTE = 2;

    public function getName(): string
    {
        return match($this) {
            self::ADMIN => 'Administrador',
            self::CLIENTE => 'Cliente',
        };
    }
}
