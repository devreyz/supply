# Gôndola - Integração OAuth com ZeTools

## 🎯 Visão Geral

O Gôndola agora utiliza o **ZeTools** como **único provedor de autenticação** via OAuth 2.0. Todos os usuários devem fazer login através do ZeTools e ter uma assinatura ativa do serviço "gondola" para acessar a aplicação.

## 🔧 Configuração

### 1. Variáveis de Ambiente

Adicione as seguintes variáveis no seu arquivo `.env`:

```env
# ZeTools OAuth Configuration
ZETOOLS_CLIENT_ID=1
ZETOOLS_CLIENT_SECRET=seu_client_secret_do_zetools
ZETOOLS_REDIRECT_URI=http://localhost:8001/auth/callback
ZETOOLS_BASE_URL=http://localhost:8000
```

### 2. Executar Migrations

```bash
php artisan migrate
```

Isso criará os campos necessários na tabela `users`:

-   `zetools_id` - ID do usuário no ZeTools
-   `zetools_token` - Access token OAuth
-   `zetools_refresh_token` - Refresh token
-   `token_expires_at` - Data de expiração do token
-   `subscriptions_cache` - Cache das assinaturas

### 3. Obter Credenciais OAuth

No servidor ZeTools (porta 8000), crie um cliente OAuth:

```bash
cd /caminho/do/zetools
php artisan passport:client
```

Informações necessárias:

-   **Nome do Cliente**: Gôndola
-   **Redirect URI**: `http://localhost:8001/auth/callback`
-   **Cliente Confidencial**: Sim

Copie o **Client ID** e **Client Secret** gerados para o `.env` do Gôndola.

## 🚀 Como Funciona

### Fluxo de Autenticação

1. **Usuário acessa** `http://localhost:8001`
2. **Clica em "Entrar com ZeTools"** → Rota: `/auth/zetools`
3. **Redireciona para ZeTools** (porta 8000)
4. **Usuário faz login** no ZeTools (se necessário)
5. **ZeTools solicita autorização** do app Gôndola
6. **Usuário autoriza**
7. **ZeTools redireciona** → `/auth/callback?code=...`
8. **Gôndola troca código por token**
9. **Verifica assinatura ativa** do serviço "gondola"
10. **Cria/atualiza usuário local**
11. **Loga usuário** → Redireciona para `/app`

### Middleware de Verificação

Todas as rotas protegidas usam o middleware `service.access` que:

-   ✅ Verifica se o token está válido
-   ✅ Renova token automaticamente se expirado
-   ✅ Verifica assinatura ativa no ZeTools
-   ✅ Cacheia verificações por 5 minutos

## 📋 Estrutura de Arquivos

```
app/
├── Http/
│   ├── Controllers/Auth/
│   │   └── ZeToolsAuthController.php    # Controla login/logout
│   └── Middleware/
│       └── EnsureHasServiceAccess.php   # Verifica assinatura
├── Models/
│   └── User.php                          # Campos zetools_*
└── Providers/
    ├── AppServiceProvider.php            # Registra ZeToolsProvider
    └── ZeToolsProvider.php               # Provider OAuth customizado

config/
└── services.php                          # Config zetools

database/migrations/
└── 2026_01_13_000001_add_zetools_fields_to_users_table.php

routes/
└── web.php                               # Rotas OAuth
```

## 🛠️ Rotas Principais

| Rota             | Método | Descrição                      |
| ---------------- | ------ | ------------------------------ |
| `/auth/zetools`  | GET    | Redireciona para OAuth ZeTools |
| `/auth/callback` | GET    | Callback do OAuth              |
| `/logout`        | POST   | Faz logout e revoga token      |
| `/app`           | GET    | Dashboard (protegido)          |

## 🔐 Segurança

### Middlewares Aplicados

```php
Route::middleware(['auth', 'verified', 'service.access'])->group(function () {
    // Rotas protegidas
});
```

### Renovação Automática de Token

O middleware verifica se o token está próximo de expirar e renova automaticamente usando o refresh token.

### Cache de Verificações

Para não sobrecarregar a API do ZeTools, as verificações de acesso são cacheadas por 5 minutos.

## 🧪 Testando

### 1. Iniciar Servidores

**Terminal 1 - ZeTools (Provider):**

```bash
cd /caminho/do/zetools
php artisan serve --port=8000
```

**Terminal 2 - Gôndola (Cliente):**

```bash
cd /caminho/do/gondola
php artisan serve --port=8001
```

### 2. Testar Fluxo Completo

1. Acesse: `http://localhost:8001`
2. Clique em "Entrar com ZeTools"
3. Faça login no ZeTools
4. Autorize o acesso do Gôndola
5. Será redirecionado para `/app` autenticado

### 3. Verificar Token

```bash
php artisan tinker
>>> $user = User::first();
>>> $user->zetools_token;  // Ver token
>>> $user->token_expires_at;  // Ver expiração
```

## 🐛 Troubleshooting

### Erro: "Client authentication failed"

**Solução:** Verifique se `ZETOOLS_CLIENT_ID` e `ZETOOLS_CLIENT_SECRET` estão corretos no `.env`

### Erro: "The redirect URI provided does not match"

**Solução:** A URL de callback deve ser exatamente `http://localhost:8001/auth/callback` no cliente OAuth

### Erro: "Você não tem assinatura ativa"

**Solução:** No ZeTools, crie uma assinatura para o usuário no serviço "gondola"

### Token Expirado

**Solução:** O refresh token automático cuida disso. Se falhar, o usuário será deslogado automaticamente.

## 📚 Documentação Adicional

-   [Documentação OAuth Completa](./docs/OAUTH_SETUP.md)
-   [Guia Rápido](./docs/OAUTH_QUICKSTART.md)
-   [Diagramas de Fluxo](./docs/OAUTH_DIAGRAMS.md)

## 🔄 Migração de Dados

Se você tinha usuários com login Google/ZePocket, será necessário:

1. Exportar dados dos usuários atuais
2. Criar contas no ZeTools
3. Associar `zetools_id` aos usuários existentes

## 📝 Notas Importantes

-   ⚠️ **Apenas ZeTools** é suportado para autenticação
-   ⚠️ **Assinatura obrigatória** para acesso
-   ⚠️ Tokens expiram em **15 dias**
-   ⚠️ Refresh tokens expiram em **30 dias**
-   ⚠️ Cache de verificações: **5 minutos**

---

**Última atualização:** 13 de Janeiro de 2026  
**Versão:** 1.0.0
