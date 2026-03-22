<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Shared\BaseModelEntity;
use App\Domain\Shared\IRepository;
use App\Infrastructure\Repositories\Traits\RepositoryTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * @template T of BaseModelEntity
 * @implements IRepository<T>
 */
abstract class Repository implements IRepository
{
    use RepositoryTrait;

    /** @var class-string<Model> */
    protected string $modelClass;

    /**
     * @param Model $model
     * @return T
     */
    abstract protected function toEntity(Model $model): BaseModelEntity;
    
    /**
     * @param T $entity
     * @param Model|null $model
     * @return array
     */
    abstract protected function mapToModel(BaseModelEntity $entity, ?Model $model = null): array;

    /**
     * @param int $id
     * @return T|null
     */
    public function findById(int $id): ?BaseModelEntity
    {
        $model = $this->modelClass::find($id);
        return $model ? $this->toEntity($model) : null;
    }

    /**
     * @param array $filters
     * @param array $orderBy
     * @param int $limit
     * @param int $page
     * @return array{data: T[], total: int, per_page: int, current_page: int, last_page: int}
     */
    public function findAll(array $filters = [], array $orderBy = [], int $limit = 15, int $page = 1): array
    {
        return $this->performFindAll($filters, $orderBy, $limit, $page);
    }

    public function delete(int $id): bool
    {
        $model = $this->modelClass::find($id);
        if (!$model) {
            return false;
        }
        return (bool) $model->delete();
    }
}
