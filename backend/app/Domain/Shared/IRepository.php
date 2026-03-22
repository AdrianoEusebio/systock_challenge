<?php

namespace App\Domain\Shared;

interface IRepository
{
    public function findById(int $id): ?BaseModelEntity;

    public function findAll(array $filters = [], array $orderBy = [], int $limit = 15, int $page = 1): array;

    public function delete(int $id): bool;
}