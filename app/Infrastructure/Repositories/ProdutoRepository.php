<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Shared\BaseModelEntity;
use App\Domain\Produto\IProdutoRepository;
use App\Domain\Produto\Models\ProdutoEntity;
use App\Domain\Usuario\IUsuarioRepository;
use App\Domain\Shared\Enums\TipoUsuario;
use App\Models\Produto;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * @extends Repository<ProdutoEntity>
 * @implements IProdutoRepository
 */
class ProdutoRepository extends Repository implements IProdutoRepository
{
    protected string $modelClass = Produto::class;

    public function __construct(private IUsuarioRepository $usuarioRepository) 
    {}

    /**
     * @param int $usuarioId
     * @return array
     * @throws \Exception
     */
    public function findByUsuarioId(int $usuarioId): array
    {
        $usuario = $this->usuarioRepository->findById($usuarioId);
        if (!$usuario) {
            throw new \Exception('Usuário não encontrado');
        }

        /** @var Usuario|null $authUser */
        $authUser = Auth::user();

        if ($authUser && $authUser->tipo_usuario !== TipoUsuario::ADMIN->value) {
            if ($authUser->id !== $usuarioId) {
                throw new \Exception('Usuário não autorizado para visualizar produtos de terceiros');
            }
        }

        return $this->findAll(['usuario_id' => $usuarioId]);
    }

    public function create(ProdutoEntity $entity): ProdutoEntity
    {
        return $this->performCreate($entity);
    }

    public function update(int $id, ProdutoEntity $entity): ProdutoEntity
    {
        return $this->performUpdate($id, $entity);
    }

    protected function mapToModel(BaseModelEntity $entity, ?Model $model = null): array
    {
        /** @var ProdutoEntity $entity */
        return [
            'nome' => $entity->getNome(),
            'preco' => $entity->getPreco(),
            'descricao' => $entity->getDescricao(),
            'usuario_id' => $entity->getUsuarioId(),
            'usuario_inclusao' => $entity->getUsuarioInclusao(),
            'usuario_alteracao' => $entity->getUsuarioAlteracao(),
        ];
    }

    protected function toEntity(Model $model): ProdutoEntity
    {
        /** @var Produto $model */
        return new ProdutoEntity(
            nome: $model->nome,
            preco: (float) $model->preco,
            descricao: $model->descricao,
            usuarioId: (int) $model->usuario_id,
            usuarioInclusao: $model->usuario_inclusao,
            usuarioAlteracao: $model->usuario_alteracao,
            id: (int) $model->id,
            created_at: $model->created_at instanceof Carbon ? $model->created_at->toDateTime() : new \DateTime(),
            updated_at: $model->updated_at instanceof Carbon ? $model->updated_at->toDateTime() : null,
            deleted_at: $model->deleted_at instanceof Carbon ? $model->deleted_at->toDateTime() : null,
        );
    }
}
