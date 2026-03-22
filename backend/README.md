# 🚀 SyStock - Desafio Técnico Backend

Bem-vindo ao **SyStock**, um sistema de gerenciamento de estoque desenvolvido em **Laravel 13** com **Arquitetura Limpa (Clean Architecture)** e **JWT Authentication**.

Este projeto foi construído focando em escalabilidade, segurança e alta performance em consultas SQL.

## 🛠 Diferenciais Implementados
-   **Arquitetura:** Repository Pattern + Domain Entities (Separação total de responsabilidade).
-   **Segurança:** Autenticação via JWT (Tymon JWT-Auth) e travas de privilégio (Admin vs Usuário).
-   **Docker:** Ambiente completo com Docker Compose (App + Database).
-   **Relatórios Analíticos:** Endpoints específicos utilizando **SQL Puro (Raw Queries)** e recursos avançados do PostgreSQL (`DISTINCT ON`, `JSON_AGG`, `COALESCE`).
-   **Boas Práticas:** Clean Code, PSR-12, Padronização de JSON Response e Tratamento de Exceções.
-   **Documentação:** API documentada via **Swagger** (L5-Swagger).

---

## 🚀 Como Executar o Projeto

### Pré-requisitos
-   Docker e Docker Compose instalados.

### Passos para Instalação

1.  **Clonar o Repositório:**
    ```bash
    git clone https://github.com/AdrianoEusebio/systock_challenge.git
    cd systock_challenge/backend
    ```

2.  **Preparar o Ambiente:**
    ```bash
    cp .env.example .env
    ```

3.  **Subir os Containers:**
    ```bash
    docker-compose up -d --build
    ```

4.  **Instalar Dependências e Configurar Chaves:**
    ```bash
    docker-compose exec app composer install
    docker-compose exec app php artisan key:generate
    docker-compose exec app php artisan jwt:secret --force
    ```

5.  **Executar Migrations e Seeders:**
    ```bash
    docker-compose exec app php artisan migrate --seed
    ```

---

## 📖 Documentação da API (Swagger)
A API possui documentação interativa para teste de todos os endpoints:

**URL do Swagger:** `http://localhost:8000/api/documentation`

---

## 📊 Relatórios SQL (Diferenciais Extras)
Além dos CRUDs de Usuário e Produto, foram implementados os seguintes relatórios em SQL Puro:

-   `GET /api/relatorio`: Geral de usuários e médias de preço.
-   `GET /api/relatorio/maiores-estoques`: Ranking de usuários por contagem de itens.
-   `GET /api/relatorio/produtos-mais-caros`: Produto premium de cada usuário.
-   `GET /api/relatorio/faixas-precos`: Análise de distribuição por preço.

As queries brutas podem ser encontradas em: `database/consultas.sql`.

---

## 🧪 Estrutura de Pastas
```
├── app/
│   ├── Domain/         # Lógica de negócio e Entidades (Puro)
│   ├── Infrastructure/  # Repositórios (Eloquent/SQL)
│   ├── Http/           # Controllers (Gerenciamento de Requisição)
│   ├── Providers/      # Injeção de Dependências
├── database/           # Migrations e Seeders
├── routes/             # Rotas API (Auth, Usuários, Produtos e Relatórios)
```

---
**Desenvolvido por:** Adriano Eusebio
