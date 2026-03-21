<?php

namespace App\Infrastructure\Repositories\Traits;

use App\Domain\Shared\BaseModelEntity;
use Illuminate\Database\Eloquent\Model;

trait RepositoryTrait
{
    /**
     * @param BaseModelEntity $entity
     * @return BaseModelEntity
     */
    protected function performCreate(BaseModelEntity $entity): BaseModelEntity
    {
        $data = $this->mapToModel($entity);
        $model = $this->modelClass::create($data);

        return $this->toEntity($model);
    }

    /**
     * @param int $id
     * @param BaseModelEntity $entity
     * @return BaseModelEntity
     */
    protected function performUpdate(int $id, BaseModelEntity $entity): BaseModelEntity
    {
        $model = $this->modelClass::findOrFail($id);
        $data = $this->mapToModel($entity, $model);
        $model->update($data);

        return $this->toEntity($model->fresh());
    }

    /**
     * @param array $filters
     * @param array $orderBy
     * @param int $limit
     * @param int $page
     * @return array
     */
    protected function performFindAll(array $filters = [], array $orderBy = [], int $limit = 15, int $page = 1): array
    {
        $query = $this->modelClass::query();

        foreach ($filters as $field => $value) {
            if ($value !== null && $value !== '') {
                if (is_array($value) && isset($value['operator'], $value['value'])) {
                    $query->where($field, $value['operator'], $value['value']);
                } else {
                    $query->where($field, 'like', "%{$value}%");
                }
            }
        }

        foreach ($orderBy as $field => $direction) {
            $query->orderBy($field, $direction);
        }

        $paginator = $query->paginate($limit, ['*'], 'page', $page);

        $entities = collect($paginator->items())->map(function (Model $model) {
            return $this->toEntity($model);
        });

        return [
            'data' => $entities->all(),
            'total' => $paginator->total(),
            'per_page' => $paginator->perPage(),
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
        ];
    }
}
