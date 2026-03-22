# 📦 Desafio Full Stack: SYSTOCK (Vue.js + Laravel)

Bem-vindo ao repositório do aplicativo web SYSTOCK, criado e projetado com foco total em boas práticas, modernidade e arquitetura de software para o desafio Full Stack.

## 🚀 Tecnologias e Diferenciais Implementados

**Front-end**:
- **Vue.js 3**: Framework progressivo adotado usando sua Composition API nativa (`<script setup>`).
- **Vuetify 3**: Integração do ecossistema e componentes. (Bônus: Design moderno e App-like, além do material padrão).
- **Vite**: Usado em substituição ao clássico Webpack (`vue.config.js`) resultando em builds ultrarrápidas, seguindo o padrão ouro atual do ecossistema Vue (`vite.config.js`).
- **Axios**: Gerenciamento limpo com Interceptors para capturar erro 401 e Tokens.
- **Pinia**: Gerenciamento de estado (Store) para a Autenticação.

**Back-end**:
- **Laravel 11 / PHP 8+**: API RESTful robusta, adotando padrão **Repository Pattern** em vez de colocar lógica direto nos Controllers.
- **PostgreSQL**: Sólido gerenciamento de dados.
- **JWT (JSON Web Token)**: Implementação de autenticação nativa e segura para o projeto.
- **Filtros e Paginação**: Paginação construída tanto no Back-end quanto consumida no Front-end visualmente com metadados.

**Avaliação de Escrita SQL (Diferencial Concluído! 🏆)**
Para comprovar as habilidades em SQL puro e DB Engine Raw Queries, atendi **AMBAS** as alternativas propostas no desafio:
1. Pela rota (Mini-Relatório): Criadas rotas e telas exclusivas `/reports` consumindo endpoints criados via `DB::select` (ver `SqlReportController`).
2. Pelo arquivo físico: Localizado em `backend/database/consultas.sql` contendo exatamente as _Query A, B e C_ requisitadas.

---

## 🏗️ Como Rodar a Aplicação

A arquitetura do projeto possui os ambienes conteinerizados isolando totalmente a dependência da sua máquina (Sail / Docker). 

### Passo 1: Configurar a API (Backend / Docker)
Sugerimos rodar o Backend pelo pacote **Laravel Sail**, garantindo que o PostgreSQL nativo também seja levantado automaticamente sem precisar criar tabelas manuais, veja:

1. Acesse o terminal da pasta backend:
   ```bash
   cd backend
   ```
2. Instale as dependências com o composer da sua máquina (ou use os containers de imagem se preferir):
   ```bash
   composer install
   ```
3. Copie o arquivo de ambiente:
   ```bash
   cp .env.example .env
   ```
4. Suba os containers do Docker pelo Laravel Sail (ele carregará a Stack do App Web PHP 8 + Servidor + PostgreSql local + Redis e mais):
   ```bash
   ./vendor/bin/sail up -d
   ```
5. Com os containers rodando, crie a key, as migrações e rode nossas Populações Base (_Seeders_):
   ```bash
   ./vendor/bin/sail artisan key:generate
   ./vendor/bin/sail artisan migrate:fresh --seed
   ```
   > 💡 O comando `--seed` gerará usuários pré-cadastrados, incluindo 1 perfil Administrador contido no `DatabaseSeeder`, populando automaticamente sua base!

### Passo 2: Configurar o App (Frontend)
Abra uma nova aba em seu terminal.

1. Acesse o Frontend:
   ```bash
   cd frontend
   ```
2. Instale os módulos NPM:
   ```bash
   npm install
   ```
3. Rode o servidor de desenvolvimento:
   ```bash
   npm run dev
   ```

Acesse o endereço local apresentado no final do terminal (geralmente `http://localhost:5173`) para visualizar a tela de Login do SYSTOCK!

### Como Entrar no Sistema
Use credenciais vindas do "Database Seeder" recém populado, ou crie uma conta livremente pelo `Registrar`, percebendo que só um "Administrador" terá totais privilégios no sistema para excluir.

Espero que o sistema vá além das expectativas e alcance positivamente a sua análise de código. Sinta-se a vontade para navegar. Boa avaliação! 🍀
