# Guia Rápido: Configurar OAuth no ZeTools

## Para o Provedor (ZeTools - zetools.com.br)

### 1. Instalar Passport

```bash
composer require laravel/passport
php artisan migrate
php artisan passport:install
```

### 2. Criar Cliente OAuth para cada aplicação

```bash
php artisan passport:client

# Exemplo para Gôndola:
# Nome: Gôndola
# Redirect: https://gondola.zetools.com.br/auth/callback
```

**Anote o Client ID e Secret gerados!**

### 3. Configurar Auth Provider

```php
// app/Providers/AuthServiceProvider.php
use Laravel\Passport\Passport;

public function boot(): void
{
    Passport::routes();
    Passport::tokensExpireIn(now()->addDays(15));
}
```

### 4. Atualizar User Model

```php
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
}
```

### 5. Criar Rotas de API

```php
// routes/api.php
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
```

---

## Para o Cliente (Gôndola, Etiqueta, etc.)

### 1. Instalar Socialite

```bash
composer require laravel/socialite
```

### 2. Configurar .env

```env
ZETOOLS_CLIENT_ID=1
ZETOOLS_CLIENT_SECRET=seu_secret_aqui
ZETOOLS_REDIRECT_URI=https://gondola.zetools.com.br/auth/callback
ZETOOLS_BASE_URL=https://zetools.com.br
```

### 3. Configurar services.php

```php
// config/services.php
'zetools' => [
    'client_id' => env('ZETOOLS_CLIENT_ID'),
    'client_secret' => env('ZETOOLS_CLIENT_SECRET'),
    'redirect' => env('ZETOOLS_REDIRECT_URI'),
    'base_url' => env('ZETOOLS_BASE_URL'),
],
```

### 4. Criar Provider Customizado

Copie o arquivo `ZeToolsProvider.php` da documentação completa para:

```
app/Providers/ZeToolsProvider.php
```

### 5. Registrar Provider

```php
// app/Providers/AppServiceProvider.php
use Laravel\Socialite\Facades\Socialite;
use App\Providers\ZeToolsProvider;

public function boot(): void
{
    Socialite::extend('zetools', function ($app) {
        $config = $app['config']['services.zetools'];
        return Socialite::buildProvider(ZeToolsProvider::class, $config);
    });
}
```

### 6. Criar Rotas de Auth

```php
// routes/web.php
Route::get('/auth/zetools', [ZeToolsAuthController::class, 'redirect']);
Route::get('/auth/callback', [ZeToolsAuthController::class, 'callback']);
```

### 7. Criar Controller

Copie o `ZeToolsAuthController.php` da documentação completa.

### 8. Atualizar Migration de Users

```php
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('zetools_id')->unique();
    $table->string('name');
    $table->string('email')->unique();
    $table->string('avatar')->nullable();
    $table->text('zetools_token')->nullable();
    $table->text('zetools_refresh_token')->nullable();
    $table->timestamps();
});
```

### 9. Testar

1. Acesse: `https://gondola.zetools.com.br/auth/zetools`
2. Faça login no ZeTools
3. Autorize o acesso
4. Você será redirecionado de volta autenticado!

---

## Checklist

### No Provedor (ZeTools)

-   [ ] Passport instalado
-   [ ] Cliente OAuth criado
-   [ ] Rotas API `/user` funcionando
-   [ ] Guard `api` configurado para Passport

### No Cliente (Gôndola)

-   [ ] Socialite instalado
-   [ ] Provider customizado criado
-   [ ] Credenciais no .env
-   [ ] Rotas de auth criadas
-   [ ] Migration de users atualizada

---

## Testando a Integração

### 1. Teste o endpoint de usuário no ZeTools

```bash
curl -X GET https://zetools.com.br/api/user \
  -H "Authorization: Bearer {TOKEN}" \
  -H "Accept: application/json"
```

### 2. Teste o fluxo completo

```
1. Acesse https://gondola.zetools.com.br/auth/zetools
2. Será redirecionado para ZeTools
3. Faça login (se necessário)
4. Clique em "Autorizar"
5. Será redirecionado de volta autenticado
```

---

## Problemas Comuns

### "Client authentication failed"

→ Verifique client_id e client_secret no .env

### "The redirect URI provided does not match"

→ URL de callback deve estar exatamente igual no cliente OAuth

### "Unauthenticated"

→ Verifique se o token está sendo enviado corretamente no header

---

## Próximos Passos

1. Implementar refresh token
2. Adicionar middleware de verificação de assinatura
3. Configurar rate limiting
4. Implementar revogação de tokens

Consulte a documentação completa em `docs/OAUTH_SETUP.md`
