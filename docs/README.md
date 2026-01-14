# Documentação ZeTools

Bem-vindo à documentação oficial do **ZeTools** - Plataforma de Micro-SaaS com OAuth 2.0.

## 📚 Índice de Documentação

### OAuth 2.0 (Sistema de Autenticação)

1. **[Documentação Completa OAuth](OAUTH_SETUP.md)**

    - Guia detalhado de implementação
    - Configuração do provedor (ZeTools)
    - Configuração do cliente (aplicações)
    - APIs e endpoints
    - Segurança e boas práticas

2. **[Guia Rápido OAuth](OAUTH_QUICKSTART.md)**

    - Início rápido em 10 minutos
    - Checklist de configuração
    - Comandos essenciais
    - Troubleshooting comum

3. **[Exemplos de Código](OAUTH_EXAMPLES.md)**

    - Provider customizado completo
    - Controllers de autenticação
    - Middleware de verificação
    - API Resources
    - Testes automatizados
    - Integração Vue.js
    - Blade Components

4. **[Diagramas e Fluxos](OAUTH_DIAGRAMS.md)**
    - Fluxo visual completo
    - Anatomia das requisições
    - Estados da aplicação
    - Diagramas de banco de dados
    - Checklist de segurança
    - Métricas e monitoramento

---

## 🚀 Por onde começar?

### Você é o Provedor (ZeTools Core)?

1. Leia: [OAUTH_SETUP.md - Parte 1](OAUTH_SETUP.md#parte-1-configurando-o-zetools-como-provedor-oauth)
2. Instale Laravel Passport
3. Configure escopos e rotas
4. Crie clientes OAuth

### Você está criando uma Aplicação Cliente?

1. Leia: [OAUTH_QUICKSTART.md](OAUTH_QUICKSTART.md)
2. Configure o provider customizado
3. Implemente o controller de autenticação
4. Teste o fluxo completo

### Quer ver código pronto?

1. Acesse: [OAUTH_EXAMPLES.md](OAUTH_EXAMPLES.md)
2. Copie os exemplos necessários
3. Adapte para seu caso de uso

### Precisa entender o fluxo?

1. Veja: [OAUTH_DIAGRAMS.md](OAUTH_DIAGRAMS.md)
2. Analise os diagramas visuais
3. Entenda cada etapa do processo

---

## 🎯 Casos de Uso

### Caso 1: Login Único (SSO)

Usuário faz login uma vez no ZeTools e acessa todas as aplicações sem precisar autenticar novamente.

**Documentação relevante:**

-   [Fluxo OAuth Completo](OAUTH_DIAGRAMS.md#fluxo-completo-de-autenticação)
-   [Controller de Autenticação](OAUTH_EXAMPLES.md#exemplo-2-controller-de-autenticação-completo)

### Caso 2: Verificação de Assinatura

Aplicação verifica se usuário tem assinatura ativa antes de permitir acesso.

**Documentação relevante:**

-   [API de Verificação](OAUTH_SETUP.md#14-controller-oauth)
-   [Middleware de Acesso](OAUTH_EXAMPLES.md#exemplo-3-middleware-de-verificação-de-acesso)

### Caso 3: Sincronização de Dados

Aplicação obtém dados do usuário e assinaturas do ZeTools.

**Documentação relevante:**

-   [API Resources](OAUTH_EXAMPLES.md#exemplo-5-resources-para-api)
-   [Provider Methods](OAUTH_EXAMPLES.md#exemplo-1-provider-customizado-completo)

### Caso 4: Renovação de Token

Token expirado precisa ser renovado automaticamente.

**Documentação relevante:**

-   [Refresh Token](OAUTH_SETUP.md#41-renovação-de-tokens)
-   [Controller Refresh](OAUTH_EXAMPLES.md#exemplo-2-controller-de-autenticação-completo)

---

## 🔐 Segurança

### Principais Recomendações

1. **HTTPS Obrigatório**

    - Sempre use HTTPS em produção
    - Nunca envie tokens via HTTP

2. **Secrets Seguros**

    - Client secrets no .env
    - Nunca commitar secrets no Git
    - Rotacionar periodicamente

3. **Validação de Redirect URIs**

    - Sempre validar URIs de callback
    - Não permitir redirects arbitrários

4. **Rate Limiting**

    - Limitar requisições à API
    - Prevenir abuso

5. **Tokens com Expiração**
    - Access tokens: 15 dias
    - Refresh tokens: 30 dias
    - Limpar tokens expirados

**Mais detalhes:** [Checklist de Segurança](OAUTH_DIAGRAMS.md#checklist-de-segurança)

---

## 🛠️ Stack Tecnológica

| Componente | Tecnologia           |
| ---------- | -------------------- |
| Framework  | Laravel 11.x         |
| OAuth      | Laravel Passport     |
| Socialite  | Laravel Socialite    |
| Database   | MySQL / PostgreSQL   |
| Cache      | Redis (recomendado)  |
| Queue      | Redis / Database     |
| Frontend   | Livewire + Alpine.js |
| CSS        | Tailwind CSS         |

---

## 📊 Arquitetura

```
┌─────────────────────────────────────────────────┐
│              ZeTools Core (Provider)            │
│                zetools.com.br                   │
│  ┌──────────────────────────────────────────┐  │
│  │  Laravel Passport (OAuth 2.0)            │  │
│  │  - Authorization Server                  │  │
│  │  - Token Management                      │  │
│  │  - Scopes & Permissions                  │  │
│  └──────────────────────────────────────────┘  │
│                      │                          │
│                      │ OAuth API                │
│                      ▼                          │
│  ┌──────────────────────────────────────────┐  │
│  │  Subscription Management                 │  │
│  │  - Services & Plans                      │  │
│  │  - User Access Control                   │  │
│  │  - Payment Integration (MercadoPago)     │  │
│  └──────────────────────────────────────────┘  │
└─────────────────────────────────────────────────┘
                      │
                      │ OAuth 2.0 Flow
                      │
    ┌─────────────────┼─────────────────┐
    │                 │                 │
    ▼                 ▼                 ▼
┌─────────┐      ┌─────────┐      ┌─────────┐
│ Gôndola │      │Etiqueta │      │ Margem  │
│ Cliente │      │ Cliente │      │ Cliente │
└─────────┘      └─────────┘      └─────────┘
```

---

## 🧪 Testes

### Testar Provedor (ZeTools)

```bash
# Criar cliente OAuth
php artisan passport:client

# Testar endpoint de usuário
curl -X GET https://zetools.com.br/api/user \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"

# Executar testes
php artisan test --filter OAuthTest
```

### Testar Cliente (Aplicação)

```bash
# Fluxo completo
1. Acesse: https://app.zetools.com.br/auth/zetools
2. Faça login no ZeTools
3. Autorize o acesso
4. Verifique redirecionamento para /dashboard
```

**Mais exemplos:** [Testes Automatizados](OAUTH_EXAMPLES.md#exemplo-6-testes-automatizados)

---

## 🐛 Troubleshooting

### Problemas Comuns

| Erro                                       | Solução                                     |
| ------------------------------------------ | ------------------------------------------- |
| "Client authentication failed"             | Verificar client_id e client_secret no .env |
| "The redirect URI provided does not match" | URL de callback deve estar exata no banco   |
| "Unauthenticated"                          | Verificar token no header Authorization     |
| "Token expired"                            | Implementar refresh token automático        |

**Guia completo:** [Troubleshooting](OAUTH_SETUP.md#troubleshooting)

---

## 📖 Recursos Adicionais

### Documentação Externa

-   [Laravel Passport Docs](https://laravel.com/docs/11.x/passport)
-   [OAuth 2.0 RFC 6749](https://datatracker.ietf.org/doc/html/rfc6749)
-   [Laravel Socialite Docs](https://laravel.com/docs/11.x/socialite)
-   [JWT.io - JWT Debugger](https://jwt.io)

### Ferramentas Úteis

-   [Postman](https://www.postman.com/) - Testar APIs
-   [OAuth Debugger](https://oauthdebugger.com/) - Debug fluxo OAuth
-   [JSON Formatter](https://jsonformatter.org/) - Formatar JSON

---

## 💡 Contribuindo

Encontrou um erro na documentação? Tem uma sugestão de melhoria?

1. Abra uma issue no repositório
2. Descreva o problema ou sugestão
3. Envie um Pull Request (se aplicável)

---

## 📝 Changelog

### v1.0.0 (2026-01-13)

-   ✅ Documentação inicial OAuth 2.0
-   ✅ Guia rápido de configuração
-   ✅ Exemplos de código completos
-   ✅ Diagramas e fluxos visuais
-   ✅ Rebrand completo: ZePocket → ZeTools

---

## 📧 Suporte

-   **Email**: suporte@zetools.com.br
-   **Documentação**: [docs.zetools.com.br](https://docs.zetools.com.br)
-   **Status**: [status.zetools.com.br](https://status.zetools.com.br)

---

**Última atualização:** 13 de Janeiro de 2026  
**Versão da Documentação:** 1.0.0
