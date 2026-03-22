<?php

namespace App\Http\Controllers;

use App\Domain\Shared\IRepository;
use App\Http\Controllers\Traits\ControllerTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

abstract class Controller
{
    use ControllerTrait;

    abstract protected function storeRules(): array;
    abstract protected function updateRules(int $id): array;

    public function __construct(protected ?IRepository $repository = null) {}


    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->query();
            $orderBy = (array)  $request->input('order_by', []);
            $limit   = (int)    $request->input('limit', 15);
            $page    = (int)    $request->input('page', 1);

            unset($filters['order_by'], $filters['limit'], $filters['page']);

            $items = $this->repository->findAll($filters, $orderBy, $limit, $page);
            return $this->successResponse($items);

        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    public function show(int $id): JsonResponse
    {
        $item = $this->repository->findById($id);

        if (!$item) {
            return $this->errorResponse('Recurso não encontrado', 404);
        }

        return $this->successResponse($item->toArray());
    }


    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->repository->delete($id);

        if (!$deleted) {
            return $this->errorResponse('Recurso não encontrado', 404);
        }

        return $this->successResponse(null, 'Deletado com sucesso');
    }

    protected function successResponse(mixed $data = null, string $message = 'Success', int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ], $status);
    }

    protected function errorResponse(string $message = 'Error', int $status = 400, mixed $errors = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors'  => $errors,
        ], $status);
    }
}
