<?php

namespace App\Http\Controllers;

use App\Domain\Usuario\IUsuarioRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Domain\Shared\BaseModelEntity;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

#[OA\Tag(name: "Autenticação", description: "Endpoints para autenticação de usuários via JWT")]
class AuthController extends Controller
{
    public function __construct(protected IUsuarioRepository $usuarioRepository)
    {
        parent::__construct($usuarioRepository);
    }

    #[OA\Post(
        path: "/api/auth/login",
        summary: "Realizar login",
        tags: ["Autenticação"]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["email", "senha"],
            properties: [
                new OA\Property(property: "email", type: "string", format: "email", example: "admin@systock.com"),
                new OA\Property(property: "senha", type: "string", format: "password", example: "123456")
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: "Login realizado com sucesso",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Success"),
                new OA\Property(property: "data", type: "object", properties: [
                    new OA\Property(property: "access_token", type: "string"),
                    new OA\Property(property: "token_type", type: "string", example: "bearer"),
                    new OA\Property(property: "expires_in", type: "integer")
                ])
            ]
        )
    )]
    #[OA\Response(response: 401, description: "Credenciais inválidas")]
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'senha' => 'required|string',
        ]);

        $creds = [
            'email' => $credentials['email'],
            'password' => $credentials['senha']
        ];

        if (! $token = Auth::attempt($creds)) {
            return $this->errorResponse('Credenciais inválidas', 401);
        }

        return $this->createNewToken($token);
    }

    #[OA\Post(
        path: "/api/auth/logout",
        summary: "Realizar logout",
        tags: ["Autenticação"],
        security: [["bearerAuth" => []]]
    )]
    #[OA\Response(response: 200, description: "Usuário desconectado com sucesso")]
    public function logout(): JsonResponse
    {
        Auth::logout();

        return $this->successResponse(null, 'Usuário desconectado com sucesso');
    }

    #[OA\Post(
        path: "/api/auth/refresh",
        summary: "Atualizar token",
        tags: ["Autenticação"],
        security: [["bearerAuth" => []]]
    )]
    #[OA\Response(response: 200, description: "Token atualizado com sucesso")]
    public function refresh(): JsonResponse
    {
        return $this->createNewToken(Auth::refresh());
    }

    #[OA\Get(
        path: "/api/auth/me",
        summary: "Perfil do usuário logado",
        tags: ["Autenticação"],
        security: [["bearerAuth" => []]]
    )]
    #[OA\Response(response: 200, description: "Dados do perfil retornados com sucesso")]
    public function userProfile(): JsonResponse
    {
        $userId = Auth::id();
        
        if (!$userId) {
            return $this->errorResponse('Não autenticado', 401);
        }

        $user = $this->usuarioRepository->findById($userId);

        if (!$user) {
            return $this->errorResponse('Dados do usuário não encontrados', 404);
        }

        return $this->successResponse($user->toArray());
    }

    protected function createNewToken($token): JsonResponse
    {
        return $this->successResponse([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60,
            'user' => $this->usuarioRepository->findById(Auth::id())?->toArray()
        ]);
    }

    protected function storeRules(): array { return []; }
    protected function updateRules(int $id): array { return []; }
    
}
