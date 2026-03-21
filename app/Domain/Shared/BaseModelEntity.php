<?php

namespace App\Domain\Shared;

use DateTime;

class BaseModelEntity implements \JsonSerializable
{
    public function __construct(
        protected int $id,
        protected ?DateTime $created_at,
        protected ?DateTime $updated_at,
        protected ?DateTime $deleted_at,
        protected ?string $usuario_inclusao,
        protected ?string $usuario_alteracao
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getDataInclusao(): DateTime
    {
        return $this->created_at;
    }

    public function setDataInclusao(DateTime $data_inclusao): void
    {
        $this->created_at = $data_inclusao;
    }

    public function getDataAlteracao(): ?DateTime
    {
        return $this->updated_at;
    }

    public function setDataAlteracao(?DateTime $data_alteracao): void
    {
        $this->updated_at = $data_alteracao;
    }

    public function getDeletedAt(): ?DateTime
    {
        return $this->deleted_at;
    }

    public function setDeletedAt(?DateTime $deleted_at): void
    {
        $this->deleted_at = $deleted_at;
    }

    public function getUsuarioInclusao(): string
    {
        return $this->usuario_inclusao;
    }

    public function setUsuarioInclusao(string $usuario_inclusao): void
    {
        $this->usuario_inclusao = $usuario_inclusao;
    }

    public function getUsuarioAlteracao(): string
    {
        return $this->usuario_alteracao;
    }

    public function setUsuarioAlteracao(string $usuario_alteracao): void
    {
        $this->usuario_alteracao = $usuario_alteracao;
    }

    public function jsonSerialize(): mixed
    {
        $data = [];

        foreach (get_object_vars($this) as $key => $value) {
            $data[$key] = $value instanceof \DateTime
                ? $value->format(\DateTime::ATOM)
                : $value;
        }

        return $data;
    }

    public function toArray(): array
    {
        return $this->jsonSerialize();
    }
}
