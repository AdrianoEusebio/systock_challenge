<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes as OA;

#[OA\Tag(name: "Relatórios", description: "Consultas analíticas em SQL puro")]
class SqlReportController extends Controller
{
    public function __construct()
    {
        parent::__construct(null);
    }

    private function checkAdmin(): ?JsonResponse
    {
        $user = Auth::user();
        if ($user && $user->tipo_usuario !== 1) {
            return response()->json(['success' => false, 'message' => 'Acesso negado aos relatórios'], 403);
        }
        return null;
    }

    #[OA\Get(
        path: "/api/relatorio",
        summary: "Relatório Geral (Usuários e Médias)",
        tags: ["Relatórios"],
        security: [["bearerAuth" => []]]
    )]
    #[OA\Response(response: 200, description: "Relatório geral retornado com sucesso")]
    public function relatorioSql(): JsonResponse
    {
        if ($error = $this->checkAdmin()) return $error;

        try {
            $sql = "
                SELECT 
                    u.id, u.nome, u.email,
                    COUNT(p.id) as total_produtos,
                    ROUND(COALESCE(AVG(p.preco), 0), 2) as media_preco_produtos
                FROM usuario u
                LEFT JOIN produto p ON u.id = p.usuario_id
                GROUP BY u.id, u.nome, u.email
                ORDER BY total_produtos DESC, u.nome ASC
            ";
            return $this->successResponse(DB::select($sql));
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    #[OA\Get(
        path: "/api/relatorio/maiores-estoques",
        summary: "Usuários com mais produtos",
        tags: ["Relatórios"],
        security: [["bearerAuth" => []]]
    )]
    #[OA\Response(response: 200, description: "Ranking de estoques retornado")]
    public function maioresEstoques(): JsonResponse
    {
        if ($error = $this->checkAdmin()) return $error;

        try {
            $sql = "
                SELECT u.id, u.nome, u.email, COUNT(p.id) as total_produtos
                FROM usuario u
                LEFT JOIN produto p ON u.id = p.usuario_id
                GROUP BY u.id, u.nome, u.email
                ORDER BY total_produtos DESC, u.nome ASC
            ";
            return $this->successResponse(DB::select($sql));
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    #[OA\Get(
        path: "/api/relatorio/produtos-mais-caros",
        summary: "Produto mais caro de cada usuário",
        tags: ["Relatórios"],
        security: [["bearerAuth" => []]]
    )]
    #[OA\Response(response: 200, description: "Produtos premium por usuário")]
    public function produtosMaisCaros(): JsonResponse
    {
        if ($error = $this->checkAdmin()) return $error;

        try {
            $sql = "
                SELECT DISTINCT ON (p.usuario_id) 
                    u.nome as nome_usuario, 
                    p.nome as nome_produto, 
                    p.preco
                FROM produto p
                JOIN usuario u ON u.id = p.usuario_id
                ORDER BY p.usuario_id, p.preco DESC
            ";
            return $this->successResponse(DB::select($sql));
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    #[OA\Get(
        path: "/api/relatorio/faixas-precos",
        summary: "Quantidade de produtos por faixa de preço",
        tags: ["Relatórios"],
        security: [["bearerAuth" => []]]
    )]
    #[OA\Response(response: 200, description: "Análise de precificação retornada")]
    public function faixasPrecos(): JsonResponse
    {
        if ($error = $this->checkAdmin()) return $error;

        try {
            $sql = "
                SELECT 
                    CASE 
                        WHEN p.preco <= 100 THEN 'Até 100 (Econômico)'
                        WHEN p.preco > 100 AND p.preco <= 1000 THEN '100 a 1000 (Médio)'
                        ELSE 'Acima de 1000 (Premium)'
                    END as faixa_preco,
                    COUNT(*) as quantidade
                FROM produto p
                GROUP BY faixa_preco
                ORDER BY MIN(p.preco) ASC
            ";
            return $this->successResponse(DB::select($sql));
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    protected function storeRules(): array { return []; }
    protected function updateRules(int $id): array { return []; }
}
