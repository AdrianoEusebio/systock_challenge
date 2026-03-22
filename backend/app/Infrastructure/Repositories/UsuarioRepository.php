<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Shared\BaseModelEntity;
use App\Domain\Shared\Enums\TipoUsuario;
use App\Domain\Usuario\IUsuarioRepository;
use App\Domain\Usuario\Models\UsuarioEntity;
use App\Domain\Usuario\Services\UsuarioService;
use App\Models\Usuario;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * @extends Repository<UsuarioEntity>
 * @implements IUsuarioRepository
 */
class UsuarioRepository extends Repository implements IUsuarioRepository
{
    protected string $modelClass = Usuario::class;

    public function __construct(protected readonly UsuarioService $usuarioService) {}

    /**
     * @param array $filters
     * @param array $orderBy
     * @param int $limit
     * @param int $page
     * @return array
     */
    public function findAll(array $filters = [], array $orderBy = [], int $limit = 15, int $page = 1): array
    {
        /** @var Usuario|null $authUser */
        $authUser = Auth::user();

        if ($authUser && $authUser->tipo_usuario !== TipoUsuario::ADMIN->value) {
            $filters['tipo_usuario'] = ['operator' => '!=', 'value' => TipoUsuario::ADMIN->value];
        }

        $paginator = $this->performFindAll($filters, $orderBy, $limit, $page);
        
        /** @var UsuarioEntity $userEntity */
        foreach ($paginator['data'] as $userEntity) {
            $count = \App\Models\Produto::where('usuario_id', $userEntity->getId())->count();
            $userEntity->setTotalProdutos($count);
        }
        
        return $paginator;
    }

    /**
     * @param int $id
     * @return UsuarioEntity|null
     */
    public function findById(int $id): ?UsuarioEntity
    {
        $model = $this->modelClass::find($id);

        if (!$model) {
            return null;
        }

        /** @var Usuario|null $authUser */
        $authUser = Auth::user();

        if ($authUser && $authUser->tipo_usuario !== TipoUsuario::ADMIN->value) {
            if ($model->tipo_usuario === TipoUsuario::ADMIN->value) {
                return null;
            }
            if ($model->id !== $authUser->id) {
                return null;
            }
        }

        return $this->toEntity($model);
    }

    /**
     * @param UsuarioEntity $entity
     * @return UsuarioEntity
     */
    public function create(UsuarioEntity $entity): UsuarioEntity
    {
        return $this->performCreate($entity);
    }

    /**
     * @param int $id
     * @param UsuarioEntity $entity
     * @return UsuarioEntity
     */
    public function update(int $id, UsuarioEntity $entity): UsuarioEntity
    {
        return $this->performUpdate($id, $entity);
    }

    protected function mapToModel(BaseModelEntity $entity, ?Model $model = null): array
    {
        $data = [
            'nome' => $entity->getNome(),
            'cpf' => $entity->getCpf(),
            'email' => $entity->getEmail(),
        ];

        if ($entity->getTipoUsuario()) {
            if (!$model || $this->usuarioService->verificarAdministrador($entity->getTipoUsuario()->value)) {
                $data['tipo_usuario'] = $entity->getTipoUsuario()->value;
            }
        }

        $senha = $entity->getSenha();
        if ($senha) {
            if (!$model || !password_verify($senha, $model->senha)) {
                $data['senha'] = bcrypt($senha);
            }
        }

        return $data;
    }

    protected function toEntity(Model $model): UsuarioEntity
    {
        /** @var Usuario $model */
        return new UsuarioEntity(
            nome: $model->nome,
            cpf: $model->cpf,
            email: $model->email,
            senha: $model->senha ?? '',
            tipoUsuario: TipoUsuario::from($model->tipo_usuario),
            id: (int) $model->id,
            created_at: $model->created_at instanceof Carbon
                ? $model->created_at->toDateTime() : new \DateTime(),
            updated_at: $model->updated_at instanceof Carbon
                ? $model->updated_at->toDateTime() : null,
            deleted_at: $model->deleted_at instanceof Carbon
                ? $model->deleted_at->toDateTime() : null,
        );
    }
}
