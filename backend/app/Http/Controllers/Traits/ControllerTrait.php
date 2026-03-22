<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

trait ControllerTrait
{
    protected function performStore(Request $request): JsonResponse
    {
        try {
            $data = $request->validate($this->storeRules());
            $entity = $this->toEntity($data);
            $item = $this->repository->create($entity);

            return $this->successResponse($item, 'Recurso criado com sucesso', 201);
        } catch (ValidationException $e) {
            return $this->errorResponse('Erro de validação', 422, $e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    protected function performUpdate(Request $request, int $id): JsonResponse
    {
        try {
            $data = $request->validate($this->updateRules($id));
            $existing = $this->repository->findById($id);

            if (!$existing) {
                return $this->errorResponse('Recurso não encontrado', 404);
            }

            $entity = $this->toEntity($data, $existing);
            $item = $this->repository->update($id, $entity);

            return $this->successResponse($item, 'Recurso atualizado com sucesso');
        } catch (ValidationException $e) {
            return $this->errorResponse('Erro de validação', 422, $e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
