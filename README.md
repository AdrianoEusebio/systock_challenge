# 📦 Desafio Full Stack: SYSTOCK (Vue.js + Laravel)

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

**Avaliação de Escrita SQL**
Para comprovar as habilidades em SQL puro e DB Engine Raw Queries, atendi **AMBAS** as alternativas propostas no desafio:
1. Pela rota (Mini-Relatório): Criadas rotas e telas exclusivas `/reports` consumindo endpoints criados via `DB::select` (ver `SqlReportController`).
2. Pelo arquivo físico: Localizado em `backend/database/consultas.sql` contendo exatamente as _Query A, B e C_ requisitadas.

---

## 🏗️ Como Rodar a Aplicação

A arquitetura do projeto possui os ambienes conteinerizados isolando totalmente a dependência da sua máquina (Sail / Docker). 

### Passo 1: Configurar a API (Backend / Docker)
Sugerimos rodar o Backend diretamente com **Docker Compose**, garantindo facilidade no uso e que o PostgreSQL seja gerado sem precisar criar tabelas manuais. A instalação de dependências, chave criptográfica e migrações são lidadas automaticamente via script em `backend/docker`.

1. Acesse o terminal da pasta backend:
   ```bash
   cd backend
   ```
2. Suba os containers do Docker Compose em background (ele carregará a API PHP 8 integrado com Nginx + PostgreSql alinhados):
   ```bash
   docker compose up -d
   ```
   > 💡 O processo de inicialização do container rodará os comandos de infraestrutura (*`composer install`, `key:generate`, `migrate:fresh --seed`*), gerando os usuários e produtos base automaticamente.

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
Use as credenciais abaixo vindas do nosso Seeder para testar o sistema:

- **👑 Administrador:** `admin@systock.com.mx` / Senha: `123456`
- **👤 Cliente Conta:** `cliente@systock.com.mx` / Senha: `123456`

Ou crie uma conta livremente pelo botão `Registrar`. Lembre-se que apenas os Administradores têm privilégios completos.

### 📚 Documentação da API (Swagger)
Para visualizar a documentação de rotas e estrutura com o Swagger em uma UI iterativa, acesse o link logo após o Backend estar rodando no Docker:
🔗 **Swagger UI**: [http://localhost:8000/api/documentation](http://localhost:8000/api/documentation)

Espero que o sistema vá além das expectativas e alcance positivamente a sua análise de código. Sinta-se a vontade para navegar. Boa avaliação! 🍀
