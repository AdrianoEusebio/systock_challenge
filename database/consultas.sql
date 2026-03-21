SELECT u.id, u.nome, u.email, COUNT(p.id) as total_produtos
FROM usuario u
LEFT JOIN produto p ON u.id = p.usuario_id
GROUP BY u.id, u.nome, u.email
ORDER BY total_produtos DESC, u.nome ASC;

SELECT DISTINCT ON (p.usuario_id) 
    u.nome as nome_usuario, 
    p.nome as nome_produto, 
    p.preco
FROM produto p
JOIN usuario u ON u.id = p.usuario_id
ORDER BY p.usuario_id, p.preco DESC;

SELECT 
    CASE 
        WHEN p.preco <= 100 THEN p.preco
        WHEN p.preco > 100 AND p.preco <= 1000 THEN p.preco
        ELSE p.preco
    END as faixa_preco,
    COUNT(p.id) as quantidade
FROM produto p
GROUP BY faixa_preco
ORDER BY MIN(p.preco) ASC;

SELECT 
    u.id, 
    u.nome, 
    u.email,
    COUNT(p.id) as total_produtos,
    ROUND(COALESCE(AVG(p.preco), 0), 2) as media_preco_produtos
FROM usuario u
LEFT JOIN produto p ON u.id = p.usuario_id
GROUP BY u.id, u.nome, u.email
ORDER BY total_produtos DESC, u.nome ASC