<?php

namespace App\Domain\Produto\Models;

use App\Domain\Shared\BaseModelEntity;
use JsonSerializable;

class ProdutoEntity extends BaseModelEntity implements JsonSerializable
{
    public function __construct(
        private string $nome,
        private float $preco,
        private string $descricao,
        private int $usuarioId,
        ?string $usuarioInclusao = null,
        ?string $usuarioAlteracao = null,
        int $id = 0,
        ?\DateTime $created_at = null,
        ?\DateTime $updated_at = null,
        ?\DateTime $deleted_at = null
    ) {
        parent::__construct($id, $created_at, $updated_at, $deleted_at, $usuarioInclusao, $usuarioAlteracao);
    }

    public function getNome(): string { return $this->nome; }
    public function setNome(string $nome): void { $this->nome = $nome; }

    public function getPreco(): float { return $this->preco; }
    public function setPreco(float $preco): void { $this->preco = $preco; }

    public function getDescricao(): string { return $this->descricao; }
    public function setDescricao(string $descricao): void { $this->descricao = $descricao; }

    public function getUsuarioId(): int { return $this->usuarioId; }
    public function setUsuarioId(int $usuarioId): void { $this->usuarioId = $usuarioId; }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'nome' => $this->nome,
            'preco' => $this->preco,
            'descricao' => $this->descricao,
            'usuario_id' => $this->usuarioId,
            'usuario_inclusao' => $this->getUsuarioInclusao(),
            'usuario_alteracao' => $this->getUsuarioAlteracao(),
            'created_at' => $this->getDataInclusao()?->format('Y-m-d H:i:s'),
            'updated_at' => $this->getDataAlteracao()?->format('Y-m-d H:i:s')
        ];
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}