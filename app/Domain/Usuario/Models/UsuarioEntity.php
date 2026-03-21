<?php

namespace App\Domain\Usuario\Models;

use App\Domain\Shared\BaseModelEntity;
use App\Domain\Shared\Enums\TipoUsuario;
use DateTime;
use Symfony\Component\HttpFoundation\JsonResponse;

class UsuarioEntity extends BaseModelEntity
{
    public function __construct(
        private string $nome,
        private string $cpf,
        private string $email,
        private string $senha,
        private TipoUsuario $tipoUsuario,
        int $id,
        ?DateTime $created_at = null,
        ?DateTime $updated_at = null,   
        ?DateTime $deleted_at = null,
        ?string $usuario_inclusao = null,
        ?string $usuario_alteracao = null
    ) {
        parent::__construct(
            id: $id,
            created_at: $created_at,
            updated_at: $updated_at,
            deleted_at: $deleted_at,
            usuario_inclusao: $usuario_inclusao,
            usuario_alteracao: $usuario_alteracao
        );
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function setNome(string $nome): void
    {
        $this->nome = $nome;
    }

    public function getCpf(): string
    {
        return $this->cpf;
    }

    public function setCpf(string $cpf): void
    {
        $this->cpf = $cpf;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getSenha(): string
    {
        return $this->senha;
    }

    public function setSenha(string $senha): void
    {
        $this->senha = $senha;
    }

    public function getTipoUsuario(): TipoUsuario
    {
        return $this->tipoUsuario;
    }

    public function setTipoUsuario(TipoUsuario $tipoUsuario): void
    {
        $this->tipoUsuario = $tipoUsuario;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'cpf' => $this->cpf,
            'email' => $this->email,
            'tipo_usuario' => $this->tipoUsuario->getName(),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}