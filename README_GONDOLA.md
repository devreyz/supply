# ZePocket Gôndola - Sistema de Gestão de Cotações e Compras

Sistema SaaS Multi-tenant para gestão de cotações e compras no varejo B2B, integrado ao ecossistema ZePocket.

## 🎯 Características Principais

-   **Multi-tenancy**: Suporte a múltiplas empresas com isolamento de dados
-   **OAuth2 Integration**: Autenticação via ZePocket Core (Laravel Socialite)
-   **Shared Hosting Friendly**: Configurado para rodar em hospedagem compartilhada (cPanel)
-   **Gestão de Cotações**: Interface Bento UI para lançamento rápido de cotações
-   **Admin Panel**: FilamentPHP v3 para gestão completa
-   **Mobile First**: Interface responsiva otimizada para uso em smartphones

## 🚀 Stack Tecnológica

-   **Backend**: Laravel 11 + PHP 8.2
-   **Frontend**: Blade + TailwindCSS + Alpine.js
-   **Admin**: FilamentPHP v3
-   **Database**: MySQL (InnoDB)
-   **Cache/Session/Queue**: MySQL (database driver)
-   **Storage**: Local (storage/app/public)

## 📦 Instalação

### 1. Clone o repositório e instale dependências

```bash
# Instalar dependências PHP
composer install

# Instalar dependências Node.js
npm install
```

### 2. Configure o ambiente

```bash
# Copiar arquivo de configuração
cp .env.example .env

# Gerar chave da aplicação
php artisan key:generate
```

### 3. Configure o banco de dados no `.env`

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=godola
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Configure as credenciais OAuth2 do ZePocket Core

```env
ZEPOCKET_CLIENT_ID=seu_client_id
ZEPOCKET_CLIENT_SECRET=seu_client_secret
ZEPOCKET_REDIRECT_URI=http://seu-dominio.com/auth/zepocket/callback
ZEPOCKET_BASE_URL=https://zepocket.com.br
```

### 5. Execute as migrations

```bash
# Criar tabelas do banco de dados
php artisan migrate

# Criar link simbólico para storage público
php artisan storage:link
```

### 6. (Opcional) Seed de dados de teste

```bash
php artisan db:seed
```

### 7. Compile assets

```bash
# Desenvolvimento
npm run dev

# Produção
npm run build
```

### 8. Inicie o servidor

```bash
# Servidor de desenvolvimento
php artisan serve

# Acesse: http://127.0.0.1:8000
```

## 🏗️ Estrutura do Banco de Dados

### Estrutura Organizacional

-   `companies` - Empresas (multi-tenancy)
-   `users` - Usuários sincronizados com ZePocket Core
-   `company_user` - Relacionamento N:N entre usuários e empresas

### Catálogo

-   `categories` - Categorias hierárquicas de produtos
-   `suppliers` - Fornecedores
-   `products` - Produtos da empresa
-   `product_codes` - Códigos EAN/Interno dos produtos

### Sistema de Cotações

-   `quotes` - Cotações (draft, open, closed, cancelled)
-   `quote_items` - Produtos solicitados em cada cotação
-   `quote_responses` - Respostas dos fornecedores
-   `quote_response_items` - Preços por produto de cada fornecedor
-   `quote_comparisons` - Histórico de comparações

### Suporte (Shared Hosting)

-   `sessions` - Sessões do usuário
-   `cache` / `cache_locks` - Cache de aplicação
-   `jobs` / `failed_jobs` - Fila de jobs

## 🔐 Autenticação OAuth2

O sistema utiliza um provider customizado do Laravel Socialite para autenticar via ZePocket Core:

### Fluxo de Autenticação

1. Usuário clica em "Login com ZePocket"
2. Redireciona para `https://zepocket.com.br/oauth/authorize`
3. Usuário autoriza a aplicação
4. Callback retorna para `/auth/zepocket/callback`
5. Sistema busca ou cria usuário local
6. Se não tiver empresa, cria uma empresa padrão
7. Usuário é autenticado e redirecionado para o dashboard

### Rotas de Autenticação

```php
GET  /auth/zepocket           # Redireciona para OAuth
GET  /auth/zepocket/callback  # Callback após autenticação
POST /logout                   # Logout do usuário
```

## 🎨 Interface Bento UI

A interface operacional usa o design "Bento UI" otimizado para mobile:

### Funcionalidades

-   **Lançamento Rápido**: Adicionar cotações rapidamente via busca inteligente
-   **Grid de Produtos**: Visualização em cards dos produtos cotados
-   **Comparativo**: Tabela comparativa de preços por fornecedor
-   **Busca Fuzzy**: Busca avançada de produtos por nome ou código

### Acesso

```
GET /quotes
```

## 🛠️ Painel Administrativo (Filament)

O FilamentPHP fornece interface completa para gestão:

### Resources Disponíveis

-   **Produtos** (`/admin/products`)

    -   CRUD completo
    -   Upload de fotos
    -   Gestão de códigos EAN
    -   Filtros por categoria e status

-   **Fornecedores** (`/admin/suppliers`)

    -   Cadastro de fornecedores
    -   Sistema de avaliação (1-5 estrelas)
    -   Contatos e documentos

-   **Categorias** (a implementar)
-   **Cotações** (a implementar)

### Acesso ao Painel

```
GET /admin
```

## 📁 Estrutura de Arquivos Criados

```
app/
├── Models/
│   ├── Company.php
│   ├── User.php (atualizado)
│   ├── Category.php
│   ├── Supplier.php
│   ├── Product.php
│   ├── ProductCode.php
│   ├── Quote.php
│   ├── QuoteItem.php
│   ├── QuoteResponse.php
│   ├── QuoteResponseItem.php
│   └── QuoteComparison.php
├── Services/
│   └── Socialite/
│       └── ZepocketProvider.php
├── Http/Controllers/
│   ├── Auth/
│   │   └── ZepocketAuthController.php
│   ├── QuoteController.php
│   └── ProductController.php
└── Filament/Resources/
    ├── ProductResource.php
    ├── ProductResource/Pages/
    ├── SupplierResource.php
    └── SupplierResource/Pages/

database/migrations/
├── 2024_01_01_000001_create_core_tables.php
├── 2024_01_01_000002_create_catalog_tables.php
└── 2024_01_01_000003_create_quotes_tables.php

resources/views/
└── quotes/
    └── index.blade.php

config/
├── session.php (atualizado)
├── cache.php (atualizado)
├── queue.php (atualizado)
├── filesystems.php (atualizado)
└── services.php (atualizado)
```

## 🔧 Configuração para Produção (Shared Hosting)

### 1. Upload via FTP

-   Envie todos os arquivos para `public_html/`
-   Mova a pasta `public/*` para a raiz do `public_html`
-   Ajuste o `index.php` para apontar para o diretório correto

### 2. Permissões

```bash
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
```

### 3. .htaccess (já incluído)

Certifique-se de que o mod_rewrite está ativo

### 4. Variáveis de Ambiente

Configure as variáveis no painel cPanel ou via `.env`

### 5. Otimizações

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 🐛 Troubleshooting

### Erro de permissão em storage

```bash
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
```

### Cache não funciona

Verifique se as tabelas `cache` e `cache_locks` foram criadas:

```bash
php artisan migrate
```

### Sessão expira rapidamente

Aumente `SESSION_LIFETIME` no `.env`:

```env
SESSION_LIFETIME=1440  # 24 horas
```

## 📝 Próximos Passos

1. ✅ Implementar Resource de Categorias no Filament
2. ✅ Implementar Resource de Cotações no Filament
3. ✅ Adicionar sistema de comparação de preços
4. ✅ Implementar exportação de relatórios (PDF/Excel)
5. ✅ Adicionar notificações por e-mail
6. ✅ Implementar histórico de cotações
7. ✅ Dashboard com gráficos e estatísticas

## 📄 Licença

Proprietário - ZePocket © 2026

## 👥 Suporte

Para suporte, entre em contato através do [ZePocket Core](https://zepocket.com.br/support)
