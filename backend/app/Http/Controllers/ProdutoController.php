<?php

namespace App\Http\Controllers;

use App\Domain\Shared\BaseModelEntity;
use App\Domain\Produto\IProdutoRepository;
use App\Domain\Produto\Models\ProdutoEntity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

#[OA\Tag(name: "Produtos", description: "Endpoints para gerenciamento de estoque de produtos")]
class ProdutoController extends Controller
{
    /**
     * Injetamos apenas o Repositório de Produto.
     */
    public function __construct(IProdutoRepository $repository)
    {
        parent::__construct($repository);
    }

    #[OA\Get(
        path: "/api/produtos",
        summary: "Listar produtos",
        tags: ["Produtos"],
        security: [["bearerAuth" => []]]
    )]
    #[OA\Parameter(name: "nome", in: "query", schema: new OA\Schema(type: "string"))]
    #[OA\Response(response: 200, description: "Lista de produtos retornada com sucesso")]
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        if ($user && $user->tipo_usuario !== 1) {
            $request->merge(['usuario_id' => $user->id]);
        }
        return parent::index($request);
    }

    #[OA\Post(
        path: "/api/produtos",
        summary: "Criar novo produto",
        tags: ["Produtos"],
        security: [["bearerAuth" => []]]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["nome", "preco", "descricao"],
            properties: [
                new OA\Property(property: "nome", type: "string", example: "Notebook Gamer"),
                new OA\Property(property: "preco", type: "number", format: "float", example: 4500.00),
                new OA\Property(property: "descricao", type: "string", example: "Notebook potente para jogos e edição")
            ]
        )
    )]
    #[OA\Response(response: 201, description: "Produto criado com sucesso")]
    public function store(Request $request): JsonResponse
    {
        return $this->performStore($request);
    }

    #[OA\Get(
        path: "/api/produtos/{id}",
        summary: "Buscar produto por ID",
        tags: ["Produtos"],
        security: [["bearerAuth" => []]]
    )]
    #[OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))]
    #[OA\Response(response: 200, description: "Produto encontrado")]
    #[OA\Response(response: 404, description: "Produto não encontrado")]
    public function show(int $id): JsonResponse
    {
        $user = Auth::user();
        /** @var ProdutoEntity|null $produto */
        $produto = $this->repository->findById($id);

        if (!$produto) {
            return $this->errorResponse('Produto não encontrado', 404);
        }

        if ($user && $user->tipo_usuario !== 1 && $produto->getUsuarioId() !== $user->id) {
            return $this->errorResponse('Não autorizado', 403);
        }

        return $this->successResponse($produto->toArray());
    }

    #[OA\Put(
        path: "/api/produtos/{id}",
        summary: "Atualizar produto",
        tags: ["Produtos"],
        security: [["bearerAuth" => []]]
    )]
    #[OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))]
    #[OA\RequestBody(
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "nome", type: "string"),
                new OA\Property(property: "preco", type: "number", format: "float"),
                new OA\Property(property: "descricao", type: "string")
            ]
        )
    )]
    #[OA\Response(response: 200, description: "Produto atualizado com sucesso")]
    public function update(Request $request, int $id): JsonResponse
    {
        return $this->performUpdate($request, $id);
    }

    #[OA\Delete(
        path: "/api/produtos/{id}",
        summary: "Remover produto",
        tags: ["Produtos"],
        security: [["bearerAuth" => []]]
    )]
    #[OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))]
    #[OA\Response(response: 200, description: "Produto removido com sucesso")]
    public function destroy(int $id): JsonResponse
    {
        return parent::destroy($id);
    }

    protected function storeRules(): array
    {
        return [
            'nome' => 'required|string|max:255',
            'preco' => 'required|numeric|min:0',
            'descricao' => 'required|string|max:1000',
        ];
    }

    protected function updateRules(int $id): array
    {
        return [
            'nome' => 'sometimes|string|max:255',
            'preco' => 'sometimes|numeric|min:0',
            'descricao' => 'sometimes|string|max:1000',
        ];
    }

    #[OA\Get(
        path: "/api/produtos/usuario/{usuarioId}",
        summary: "Listar produtos de um usuário específico",
        tags: ["Produtos"],
        security: [["bearerAuth" => []]]
    )]
    #[OA\Parameter(name: "usuarioId", in: "path", required: true, schema: new OA\Schema(type: "integer"))]
    #[OA\Response(response: 200, description: "Lista de produtos filtrada por usuário")]
    public function findByUsuarioId(Request $request, int $usuarioId): JsonResponse
    {
        $user = Auth::user();
        if ($user && $user->tipo_usuario !== 1 && $user->id !== $usuarioId) {
            return $this->errorResponse('Não autorizado para visualizar produtos de terceiros', 403);
        }

        try {
            $filters = $request->query();
            $orderBy = (array) $request->input('order_by', []);
            $limit = (int) $request->input('limit', 15);
            $page = (int) $request->input('page', 1);
            unset($filters['order_by'], $filters['limit'], $filters['page']);

            /** @var \App\Infrastructure\Repositories\ProdutoRepository $repository */
            $repository = $this->repository;
            $items = $repository->findByUsuarioId($usuarioId, $filters, $orderBy, $limit, $page);
            
            return $this->successResponse($items);

        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    protected function toEntity(array $data, ?BaseModelEntity $existing = null): ProdutoEntity
    {
        /** @var ProdutoEntity|null $existing */
        $user = Auth::user();

        return new ProdutoEntity(
            nome: $data['nome'] ?? $existing?->getNome(),
            preco: (float) ($data['preco'] ?? $existing?->getPreco()),
            descricao: $data['descricao'] ?? $existing?->getDescricao(),
            usuarioId: (int) ($existing?->getUsuarioId() ?? $user?->getAuthIdentifier()),
            usuarioInclusao: $existing?->getUsuarioInclusao() ?? $user?->nome ?? 'System',
            usuarioAlteracao: $user?->nome ?? 'System',
            id: $existing?->getId() ?? 0,
        );
    }
}
