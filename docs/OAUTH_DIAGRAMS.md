# Diagrama de Fluxo OAuth - ZeTools

## Fluxo Completo de Autenticação

```
┌──────────────────────────────────────────────────────────────────────────────┐
│                         FLUXO OAUTH 2.0 - ZETOOLS                            │
└──────────────────────────────────────────────────────────────────────────────┘

┌─────────────┐         ┌─────────────┐         ┌─────────────┐
│   Usuário   │         │   Gôndola   │         │  ZeTools    │
│             │         │  (Cliente)  │         │ (Provider)  │
└──────┬──────┘         └──────┬──────┘         └──────┬──────┘
       │                       │                        │
       │  1. Acessa App        │                        │
       │─────────────────────►│                        │
       │                       │                        │
       │                       │  2. Redirect OAuth     │
       │                       │───────────────────────►│
       │                       │   GET /oauth/authorize │
       │                       │   ?client_id=1         │
       │                       │   &redirect_uri=...    │
       │                       │   &response_type=code  │
       │                       │   &scope=read-user     │
       │                       │                        │
       │  3. Página de Login   │                        │
       │◄──────────────────────────────────────────────│
       │                       │                        │
       │  4. Credenciais       │                        │
       │───────────────────────────────────────────────►│
       │  (email + senha)      │                        │
       │                       │                        │
       │  5. Tela Autorização  │                        │
       │◄──────────────────────────────────────────────│
       │  "Gôndola quer        │                        │
       │   acessar sua conta"  │                        │
       │                       │                        │
       │  6. Autorizar         │                        │
       │───────────────────────────────────────────────►│
       │                       │                        │
       │                       │  7. Redirect com Code  │
       │                       │◄───────────────────────│
       │◄──────────────────────│   ?code=ABC123...     │
       │                       │                        │
       │                       │  8. Trocar Code        │
       │                       │───────────────────────►│
       │                       │   POST /oauth/token    │
       │                       │   code=ABC123          │
       │                       │   client_id=1          │
       │                       │   client_secret=...    │
       │                       │                        │
       │                       │  9. Access Token       │
       │                       │◄───────────────────────│
       │                       │   {                    │
       │                       │     "access_token":    │
       │                       │     "token123...",     │
       │                       │     "refresh_token":   │
       │                       │     "refresh456..."    │
       │                       │   }                    │
       │                       │                        │
       │                       │  10. Obter Dados       │
       │                       │───────────────────────►│
       │                       │   GET /api/user        │
       │                       │   Bearer token123...   │
       │                       │                        │
       │                       │  11. User Data         │
       │                       │◄───────────────────────│
       │                       │   {id, name, email}    │
       │                       │                        │
       │                       │  12. Verificar Acesso  │
       │                       │───────────────────────►│
       │                       │   GET /api/user/       │
       │                       │   has-access/gondola   │
       │                       │                        │
       │                       │  13. Access Status     │
       │                       │◄───────────────────────│
       │                       │   {has_access: true}   │
       │                       │                        │
       │  14. Login Success    │                        │
       │◄──────────────────────│                        │
       │  Redirect /dashboard  │                        │
       │                       │                        │
```

---

## Anatomia de uma Requisição OAuth

### 1. Autorização (GET /oauth/authorize)

```http
GET /oauth/authorize?
    response_type=code&
    client_id=1&
    redirect_uri=https://gondola.zetools.com.br/auth/callback&
    scope=read-user read-subscriptions&
    state=randomString123
```

**Parâmetros:**

-   `response_type`: Sempre `code` para Authorization Code Flow
-   `client_id`: ID do cliente OAuth (obtido ao criar cliente)
-   `redirect_uri`: URL de callback (deve estar cadastrada)
-   `scope`: Permissões solicitadas (separadas por espaço)
-   `state`: String aleatória para prevenir CSRF

---

### 2. Troca de Código (POST /oauth/token)

```http
POST /oauth/token
Content-Type: application/x-www-form-urlencoded

grant_type=authorization_code&
code=ABC123DEF456&
redirect_uri=https://gondola.zetools.com.br/auth/callback&
client_id=1&
client_secret=seu_secret_aqui
```

**Resposta:**

```json
{
    "token_type": "Bearer",
    "expires_in": 1296000,
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "refresh_token": "def50200a8d9c7e4b..."
}
```

---

### 3. Acessar API (GET /api/user)

```http
GET /api/user
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGc...
Accept: application/json
```

**Resposta:**

```json
{
    "id": 1,
    "name": "João Silva",
    "email": "joao@example.com",
    "avatar": "https://...",
    "roles": ["user"],
    "created_at": "2024-01-01T00:00:00Z"
}
```

---

### 4. Verificar Acesso (GET /api/user/has-access/{service})

```http
GET /api/user/has-access/gondola
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGc...
Accept: application/json
```

**Resposta (Com Acesso):**

```json
{
    "has_access": true,
    "service": "gondola",
    "checked_at": "2024-01-13T12:00:00Z"
}
```

**Resposta (Sem Acesso):**

```json
{
    "has_access": false,
    "service": "gondola",
    "checked_at": "2024-01-13T12:00:00Z"
}
```

---

### 5. Renovar Token (POST /oauth/token - Refresh)

```http
POST /oauth/token
Content-Type: application/x-www-form-urlencoded

grant_type=refresh_token&
refresh_token=def50200a8d9c7e4b...&
client_id=1&
client_secret=seu_secret_aqui&
scope=read-user read-subscriptions
```

**Resposta:**

```json
{
    "token_type": "Bearer",
    "expires_in": 1296000,
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc... (novo)",
    "refresh_token": "def50200xyz789abc... (novo ou mesmo)"
}
```

---

## Fluxo de Estados da Aplicação

```
┌────────────────────────────────────────────────────────────┐
│                    ESTADOS DO CLIENTE                      │
└────────────────────────────────────────────────────────────┘

┌──────────────┐
│ Desautenticado│
│   (Guest)     │
└───────┬───────┘
        │
        │ Clica "Entrar"
        ▼
┌──────────────┐
│ Redirecionando│◄──── GET /auth/zetools
│   ZeTools     │      (route('auth.zetools'))
└───────┬───────┘
        │
        │ OAuth Authorize
        ▼
┌──────────────┐
│  No ZeTools   │
│ (Login/Auth)  │◄──── Fora da aplicação
└───────┬───────┘
        │
        │ Autoriza
        ▼
┌──────────────┐
│   Callback    │◄──── GET /auth/callback?code=...
│  Processing   │      (route('auth.zetools.callback'))
└───────┬───────┘
        │
        │ Token Exchange
        │ + User Creation
        │ + Access Check
        ▼
    ┌───────┐
    │Sucesso│?
    └───┬───┘
        │
    ┌───┴──────┐
    │          │
    │ SIM      │ NÃO
    ▼          ▼
┌──────────┐  ┌────────────┐
│Autenticado│  │Sem Acesso  │
│ (Logado)  │  │(No Access) │
└─────┬─────┘  └────────────┘
      │              │
      │              └──► Redirect: /subscription-required
      │
      ▼
┌──────────────┐
│   Dashboard   │◄──── Middleware: auth + service.access
│   Protegido   │
└───────────────┘
```

---

## Diagrama de Banco de Dados

### Provedor (ZeTools)

```
┌─────────────────────┐
│       users         │
├─────────────────────┤
│ id                  │
│ name                │
│ email               │
│ avatar              │
│ created_at          │
└──────────┬──────────┘
           │
           │ 1:N
           │
┌──────────▼──────────┐      ┌─────────────────────┐
│   subscriptions     │──N:1─│      services       │
├─────────────────────┤      ├─────────────────────┤
│ id                  │      │ id                  │
│ user_id             │      │ name                │
│ service_id          │──►   │ slug                │
│ plan_id             │      │ secret_key          │
│ status              │      │ redirect_url        │
│ expires_at          │      │ is_active           │
└─────────────────────┘      └─────────────────────┘

┌─────────────────────┐
│  oauth_clients      │◄──── Laravel Passport
├─────────────────────┤
│ id                  │
│ name                │
│ secret              │
│ redirect            │
│ revoked             │
└─────────────────────┘

┌─────────────────────┐
│  oauth_access_tokens│◄──── Laravel Passport
├─────────────────────┤
│ id                  │
│ user_id             │
│ client_id           │
│ scopes              │
│ revoked             │
│ expires_at          │
└─────────────────────┘
```

### Cliente (Gôndola, Etiqueta, etc.)

```
┌─────────────────────┐
│       users         │
├─────────────────────┤
│ id                  │
│ zetools_id          │◄──── ID do usuário no ZeTools
│ name                │
│ email               │
│ avatar              │
│ zetools_token       │◄──── Access Token
│ zetools_refresh     │◄──── Refresh Token
│ token_expires_at    │
│ subscriptions_cache │◄──── Cache JSON das assinaturas
│ created_at          │
└─────────────────────┘
```

---

## Checklist de Segurança

### No Provedor (ZeTools)

-   [x] HTTPS obrigatório em produção
-   [x] Tokens com expiração (15 dias)
-   [x] Refresh tokens (30 dias)
-   [x] Rate limiting nas rotas API
-   [x] CORS configurado corretamente
-   [x] Client secrets seguros (40+ caracteres)
-   [x] Redirect URIs validadas
-   [x] Scopes bem definidos
-   [x] Logs de acessos suspeitos
-   [x] Revogação de tokens funcionando

### No Cliente (Gôndola)

-   [x] Client secret no .env (nunca no código)
-   [x] State parameter para prevenir CSRF
-   [x] Tokens armazenados de forma segura
-   [x] Verificação periódica de assinatura
-   [x] Renovação automática de tokens
-   [x] Logout limpa tokens
-   [x] Middleware protege rotas sensíveis
-   [x] Cache de verificações (não abusar da API)
-   [x] Tratamento de erros adequado
-   [x] Logs de autenticação

---

## Performance e Otimizações

### Cache de Verificações

```php
// Não fazer em CADA requisição:
❌ $provider->hasAccessToService($token, 'gondola');

// Fazer com cache:
✅ Cache::remember("user_{$userId}_access_gondola", 300, function () {
    return $provider->hasAccessToService($token, 'gondola');
});
```

### Refresh Token Proativo

```php
// Renovar antes de expirar:
if ($user->token_expires_at->diffInHours(now()) < 24) {
    $this->refreshToken($user);
}
```

### Lazy Loading de Assinaturas

```php
// Cache local das assinaturas (atualizar 1x por hora):
$subscriptions = Cache::remember("user_{$userId}_subs", 3600, function () {
    return $provider->getUserSubscriptions($token);
});
```

---

## Monitoramento

### Métricas Importantes

1. **Taxa de Sucesso de Login**

    - Meta: > 99%
    - Monitorar: Falhas de OAuth

2. **Tempo de Resposta da API**

    - Meta: < 200ms
    - Endpoints: /api/user, /api/user/has-access

3. **Tokens Expirados**

    - Limpar periodicamente (passport:purge)
    - Monitorar uso de refresh tokens

4. **Erros de Autorização**
    - Log centralizado
    - Alertas para picos de erros

### Logs Essenciais

```php
// Logar eventos críticos:
logger()->info('OAuth Login Success', [
    'user_id' => $user->id,
    'client' => $request->input('client_id'),
    'scopes' => $scopes,
]);

logger()->error('OAuth Access Denied', [
    'user_id' => $user->id,
    'service' => $serviceSlug,
    'reason' => 'subscription_expired',
]);
```

---

Para implementação completa, consulte:

-   [Documentação OAuth Completa](OAUTH_SETUP.md)
-   [Guia Rápido](OAUTH_QUICKSTART.md)
-   [Exemplos de Código](OAUTH_EXAMPLES.md)
