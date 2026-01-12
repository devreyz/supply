# 🚀 ZePocket Gôndola - Guia de Início Rápido

## ✅ Instalação Concluída!

O código base do **ZePocket Gôndola** foi gerado com sucesso. Todas as estruturas essenciais estão prontas:

### 📦 O que foi criado:

#### 1️⃣ **Banco de Dados** (✅ Migrations Executadas)

-   ✅ Estrutura Multi-tenant (companies, users, company_user)
-   ✅ Catálogo (categories, suppliers, products, product_codes)
-   ✅ Sistema de Cotações (quotes, quote_items, quote_responses, quote_response_items)
-   ✅ Suporte Shared Hosting (cache, sessions, jobs via MySQL)

#### 2️⃣ **Models Eloquent** (✅ Com Relacionamentos)

```
app/Models/
├── Company.php          # Multi-tenancy
├── User.php             # Usuários + ZePocket OAuth
├── Category.php         # Categorias hierárquicas
├── Supplier.php         # Fornecedores
├── Product.php          # Produtos
├── ProductCode.php      # Códigos EAN/Interno
├── Quote.php            # Cotações
├── QuoteItem.php        # Itens de cotação
├── QuoteResponse.php    # Respostas de fornecedores
└── QuoteResponseItem.php # Preços por produto
```

#### 3️⃣ **Autenticação OAuth2** (✅ Socialite Customizado)

```
app/Services/Socialite/
└── ZepocketProvider.php  # Provider customizado para ZePocket Core

app/Http/Controllers/Auth/
└── ZepocketAuthController.php  # Controller de autenticação
```

**Rotas de Auth:**

-   `GET  /auth/zepocket` → Redireciona para login ZePocket
-   `GET  /auth/zepocket/callback` → Callback OAuth2
-   `POST /logout` → Logout

#### 4️⃣ **FilamentPHP Resources** (✅ Admin Panel)

```
app/Filament/Resources/
├── ProductResource.php      # CRUD de Produtos
│   └── Pages/
│       ├── ListProducts.php
│       ├── CreateProduct.php
│       └── EditProduct.php
├── SupplierResource.php     # CRUD de Fornecedores
    └── Pages/
        ├── ListSuppliers.php
        ├── CreateSupplier.php
        └── EditSupplier.php
```

**Acesso ao Painel Admin:**

```
http://127.0.0.1:8001/admin
```

#### 5️⃣ **Interface Bento UI** (✅ Mobile First)

```
resources/views/quotes/
└── index.blade.php  # Interface operacional de cotações

app/Http/Controllers/
├── QuoteController.php   # Lógica de cotações
└── ProductController.php # Busca de produtos (API)
```

**Acesso à Interface:**

```
http://127.0.0.1:8001/quotes
```

---

## 🎯 Próximos Passos

### 1️⃣ **Configure o OAuth2 do ZePocket Core**

Edite o arquivo `.env` e adicione as credenciais:

```env
ZEPOCKET_CLIENT_ID=seu_client_id_aqui
ZEPOCKET_CLIENT_SECRET=seu_client_secret_aqui
ZEPOCKET_REDIRECT_URI=http://127.0.0.1:8001/auth/zepocket/callback
ZEPOCKET_BASE_URL=https://zepocket.com.br
```

### 2️⃣ **Faça login com as credenciais de teste**

```
Email: admin@godola.test
Senha: password
```

Ou acesse via ZePocket OAuth (após configurar):

```
http://127.0.0.1:8001/auth/zepocket
```

### 3️⃣ **Explore o sistema**

#### **Painel Administrativo (Filament)**

```bash
http://127.0.0.1:8001/admin
```

-   **Produtos**: Cadastre novos produtos, categorias e códigos EAN
-   **Fornecedores**: Gerencie fornecedores e avaliações
-   Upload de fotos, filtros avançados, busca em tempo real

#### **Interface de Cotações (Bento UI)**

```bash
http://127.0.0.1:8001/quotes
```

-   **Lançamento Rápido**: Adicione cotações via busca inteligente
-   **Grid de Produtos**: Visualize produtos cotados
-   **Comparativo**: Compare preços entre fornecedores
-   Interface Mobile First com abas deslizantes

---

## 📚 Estrutura de Dados

### 🏢 Multi-tenancy

Cada empresa (`Company`) tem seus próprios:

-   Produtos
-   Fornecedores
-   Categorias
-   Cotações

Usuários podem pertencer a múltiplas empresas.

### 📦 Fluxo de Cotação

```
1. Quote (Cotação)
   └─ QuoteItem (Produtos solicitados)
      └─ QuoteResponseItem (Preços dos fornecedores)
         └─ QuoteResponse (Resposta completa de cada fornecedor)
```

**Exemplo:**

```
Cotação #123 - "Compra Semanal 15/01"
├─ Item: Arroz 5kg (Qtd: 10)
│  ├─ Fornecedor A: R$ 18,50
│  ├─ Fornecedor B: R$ 17,90 ✅ Melhor preço
│  └─ Fornecedor C: R$ 19,00
└─ Item: Feijão 1kg (Qtd: 20)
   ├─ Fornecedor A: R$ 7,50 ✅ Melhor preço
   └─ Fornecedor B: R$ 8,00
```

---

## 🛠️ Comandos Úteis

### Desenvolvimento

```bash
# Iniciar servidor
php artisan serve

# Compilar assets
npm run dev

# Limpar cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Banco de Dados

```bash
# Rodar migrations
php artisan migrate

# Resetar e recriar banco (⚠️ apaga dados)
php artisan migrate:fresh

# Criar dados de teste
php artisan db:seed --class=GondolaSeeder
```

### Produção

```bash
# Otimizações
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Build assets
npm run build
```

---

## 🔐 Segurança Multi-tenant

Todos os Models usam **scopes automáticos** para filtrar por `company_id`:

```php
// Exemplo no ProductResource
public static function getEloquentQuery(): Builder
{
    return parent::getEloquentQuery()
        ->where('company_id', auth()->user()->current_company_id);
}
```

Isso garante que usuários **nunca** vejam dados de outras empresas.

---

## 📱 Funcionalidades da Interface Bento UI

### ✅ Implementadas

-   ✅ Busca inteligente de produtos
-   ✅ Lançamento rápido de cotações
-   ✅ Grid responsivo de produtos
-   ✅ Abas mobile-friendly
-   ✅ Auto-complete de produtos

### 🔜 A Implementar (Próxima Fase)

-   [ ] Comparativo visual de preços
-   [ ] Exportação de relatórios (PDF/Excel)
-   [ ] Gráficos de análise de preços
-   [ ] Histórico de cotações
-   [ ] Notificações push
-   [ ] Scanner de código de barras (mobile)

---

## 📖 Documentação Completa

Consulte o arquivo `README_GONDOLA.md` para:

-   Arquitetura detalhada
-   Diagramas de relacionamento
-   API endpoints
-   Configuração de produção
-   Troubleshooting

---

## 🎨 Customização

### Adicionar novos campos em Produto

```php
// 1. Criar migration
php artisan make:migration add_brand_to_products --table=products

// 2. Adicionar campo no ProductResource.php
Forms\Components\TextInput::make('brand')
    ->label('Marca')
    ->maxLength(255),
```

### Criar novo Resource no Filament

```bash
php artisan make:filament-resource Category --generate
```

---

## 🐛 Problemas Comuns

### ❌ Erro "Class ZepocketProvider not found"

```bash
composer dump-autoload
```

### ❌ Erro de permissão em storage

```bash
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
```

### ❌ Sessão expira rapidamente

Edite `.env`:

```env
SESSION_LIFETIME=1440
SESSION_DRIVER=database
```

---

## 🚀 Deploy para Produção (cPanel)

1. **Upload via FTP/Git**
2. **Configure .env no servidor**
3. **Execute migrations**
    ```bash
    php artisan migrate --force
    ```
4. **Otimize cache**
    ```bash
    php artisan optimize
    ```
5. **Configure permissões**
    ```bash
    chmod -R 755 storage/
    ```

---

## 💡 Dicas

-   Use `php artisan tinker` para testar queries
-   Monitore logs em `storage/logs/laravel.log`
-   Teste autenticação OAuth em ambiente de staging primeiro
-   Faça backup do banco antes de migrations em produção

---

## 📞 Suporte

Para dúvidas sobre o **ZePocket Core OAuth**, consulte:

-   Documentação: https://zepocket.com.br/docs/oauth
-   Suporte: https://zepocket.com.br/support

---

**🎉 Sistema pronto para uso! Bom desenvolvimento!**
