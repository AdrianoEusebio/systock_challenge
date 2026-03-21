<?php

namespace App\Http\Controllers;

use App\Domain\Shared\BaseModelEntity;
use App\Domain\Shared\Enums\TipoUsuario;
use App\Domain\Usuario\IUsuarioRepository;
use App\Domain\Usuario\Models\UsuarioEntity;
use App\Domain\Usuario\Services\UsuarioService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(name: "Usuários", description: "Endpoints para gerenciamento de usuários")]
class UsuarioController extends Controller
{
 
    public function __construct(IUsuarioRepository $repository)
    {
        parent::__construct($repository);
    }

    #[OA\Get(
        path: "/api/usuarios",
        summary: "Listar usuários",
        tags: ["Usuários"],
        security: [["bearerAuth" => []]]
    )]
    #[OA\Parameter(name: "nome", in: "query", schema: new OA\Schema(type: "string"))]
    #[OA\Parameter(name: "email", in: "query", schema: new OA\Schema(type: "string"))]
    #[OA\Response(response: 200, description: "Lista de usuários retornada com sucesso")]
    #[OA\Response(response: 401, description: "Não autorizado")]
    public function index(Request $request): JsonResponse
    {
        return parent::index($request);
    }

    #[OA\Get(
        path: "/api/usuarios/{id}",
        summary: "Buscar usuário por ID",
        tags: ["Usuários"],
        security: [["bearerAuth" => []]]
    )]
    #[OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))]
    #[OA\Response(response: 200, description: "Usuário encontrado")]
    #[OA\Response(response: 404, description: "Usuário não encontrado")]
    public function show(int $id): JsonResponse
    {
        return parent::show($id);
    }

    #[OA\Post(
        path: "/api/usuarios",
        summary: "Criar novo usuário",
        tags: ["Usuários"],
        security: [["bearerAuth" => []]]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["nome", "cpf", "email", "senha", "tipo_usuario"],
            properties: [
                new OA\Property(property: "nome", type: "string"),
                new OA\Property(property: "cpf", type: "string", example: "12345678901"),
                new OA\Property(property: "email", type: "string", format: "email"),
                new OA\Property(property: "senha", type: "string", minLength: 6),
                new OA\Property(property: "tipo_usuario", type: "integer", enum: [1, 2], description: "1: Admin, 2: Cliente")
            ]
        )
    )]
    #[OA\Response(response: 201, description: "Usuário criado com sucesso")]
    #[OA\Response(response: 422, description: "Erro de validação")]
    public function store(Request $request): JsonResponse
    {
        return $this->performStore($request);
    }

    #[OA\Put(
        path: "/api/usuarios/{id}",
        summary: "Atualizar usuário existente",
        tags: ["Usuários"],
        security: [["bearerAuth" => []]]
    )]
    #[OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))]
    #[OA\RequestBody(
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "nome", type: "string"),
                new OA\Property(property: "cpf", type: "string"),
                new OA\Property(property: "email", type: "string", format: "email"),
                new OA\Property(property: "tipo_usuario", type: "integer")
            ]
        )
    )]
    #[OA\Response(response: 200, description: "Usuário atualizado com sucesso")]
    #[OA\Response(response: 404, description: "Usuário não encontrado")]
    public function update(Request $request, int $id): JsonResponse
    {
        return $this->performUpdate($request, $id);
    }

    #[OA\Delete(
        path: "/api/usuarios/{id}",
        summary: "Remover usuário",
        tags: ["Usuários"],
        security: [["bearerAuth" => []]]
    )]
    #[OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))]
    #[OA\Response(response: 200, description: "Usuário removido com sucesso")]
    #[OA\Response(response: 404, description: "Usuário não encontrado")]
    public function destroy(int $id): JsonResponse
    {
        return parent::destroy($id);
    }

    protected function storeRules(): array
    {
        return [
            'nome' => 'required|string|max:255',
            'cpf' => 'required|string|digits:11|unique:usuario,cpf',
            'email' => 'required|email|unique:usuario,email',
            'senha' => 'required|string|min:6',
            'tipo_usuario' => 'required|integer|in:1,2',
        ];
    }

    protected function updateRules(int $id): array
    {
        return [
            'nome' => 'sometimes|string|max:255',
            'cpf' => "sometimes|string|digits:11|unique:usuario,cpf,{$id}",
            'email' => "sometimes|email|unique:usuario,email,{$id}",
            'senha' => 'sometimes|string|min:6',
            'tipo_usuario' => 'sometimes|integer|in:1,2',
        ];
    }

    protected function toEntity(array $data, ?BaseModelEntity $existing = null): UsuarioEntity
    {
        /** @var UsuarioEntity|null $existing */
        return new UsuarioEntity(
            nome: $data['nome'] ?? $existing?->getNome(),
            cpf: $data['cpf'] ?? $existing?->getCpf(),
            email: $data['email'] ?? $existing?->getEmail(),
            senha: $data['senha'] ?? $existing?->getSenha(),
            tipoUsuario: isset($data['tipo_usuario'])
                ? TipoUsuario::from($data['tipo_usuario'])
                : $existing?->getTipoUsuario(),
            id: $existing?->getId() ?? 0,
        );
    }
}
