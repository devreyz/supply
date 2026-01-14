# Documentação OAuth - ZeTools

## Visão Geral

O ZeTools funciona como um **provedor OAuth (Authorization Server)**, permitindo que aplicações externas (clientes OAuth) usem a autenticação centralizada do ZeTools para autenticar seus usuários.

## Arquitetura OAuth 2.0

```
┌─────────────────┐          ┌─────────────────┐          ┌─────────────────┐
│                 │          │                 │          │                 │
│  Aplicação      │◄────────►│  ZeTools        │◄────────►│  Usuário        │
│  Cliente        │  OAuth   │  (Provedor)     │  Login   │  Final          │
│  (Gôndola)      │  2.0     │  zetools.com.br │          │                 │
│                 │          │                 │          │                 │
└─────────────────┘          └─────────────────┘          └─────────────────┘
```

### Fluxo de Autenticação

1. **Usuário acessa aplicação cliente** (ex: gondola.zetools.com.br)
2. **Cliente redireciona para ZeTools** com parâmetros OAuth
3. **Usuário faz login no ZeTools** (se ainda não estiver autenticado)
4. **ZeTools solicita autorização** ao usuário
5. **Usuário autoriza** o acesso
6. **ZeTools redireciona de volta** para cliente com código de autorização
7. **Cliente troca código** por access token
8. **Cliente usa token** para acessar dados do usuário

---

## Parte 1: Configurando o ZeTools como Provedor OAuth

### 1.1 Instalação do Laravel Passport

O ZeTools usa **Laravel Passport** para implementar OAuth 2.0.

```bash
# Instalar Passport
composer require laravel/passport

# Executar migrations
php artisan migrate

# Instalar chaves de encriptação
php artisan passport:install

# Criar cliente para uso pessoal (opcional)
php artisan passport:client --personal
```

### 1.2 Configuração do Passport

**config/auth.php**

```php
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],

    'api' => [
        'driver' => 'passport',  // Alterado de 'sanctum' para 'passport'
        'provider' => 'users',
    ],
],
```

**app/Models/User.php**

```php
<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens; // Trait do Passport

    // ... resto do código
}
```

**app/Providers/AuthServiceProvider.php**

```php
<?php

namespace App\Providers;

use Laravel\Passport\Passport;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        // suas policies
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        // Rotas do Passport
        Passport::routes();

        // Tempo de vida dos tokens
        Passport::tokensExpireIn(now()->addDays(15));
        Passport::refreshTokensExpireIn(now()->addDays(30));
        Passport::personalAccessTokensExpireIn(now()->addMonths(6));

        // Escopos disponíveis
        Passport::tokensCan([
            'read-user' => 'Ler informações do usuário',
            'read-subscriptions' => 'Ler assinaturas ativas',
            'manage-account' => 'Gerenciar conta',
        ]);

        // Escopo padrão
        Passport::setDefaultScope([
            'read-user',
        ]);
    }
}
```

### 1.3 Criando Rotas de API OAuth

**routes/api.php**

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OAuth\UserController;

Route::middleware('auth:api')->group(function () {
    // Informações do usuário autenticado
    Route::get('/user', [UserController::class, 'me']);

    // Assinaturas ativas
    Route::get('/user/subscriptions', [UserController::class, 'subscriptions']);

    // Verificar acesso a serviço específico
    Route::get('/user/has-access/{serviceSlug}', [UserController::class, 'hasAccess']);
});
```

### 1.4 Controller OAuth

**app/Http/Controllers/Api/OAuth/UserController.php**

```php
<?php

namespace App\Http\Controllers\Api\OAuth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    /**
     * Retorna informações do usuário autenticado via OAuth
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $user->avatar,
            'created_at' => $user->created_at,
        ]);
    }

    /**
     * Retorna assinaturas ativas do usuário
     */
    public function subscriptions(Request $request): JsonResponse
    {
        $user = $request->user();

        $subscriptions = $user->subscriptions()
            ->with('service:id,name,slug')
            ->active()
            ->get()
            ->map(function ($subscription) {
                return [
                    'service' => $subscription->service->name,
                    'slug' => $subscription->service->slug,
                    'status' => $subscription->status,
                    'expires_at' => $subscription->expires_at,
                ];
            });

        return response()->json([
            'subscriptions' => $subscriptions,
        ]);
    }

    /**
     * Verifica se usuário tem acesso a um serviço específico
     */
    public function hasAccess(Request $request, string $serviceSlug): JsonResponse
    {
        $user = $request->user();
        $hasAccess = $user->hasActiveSubscription($serviceSlug);

        return response()->json([
            'has_access' => $hasAccess,
            'service' => $serviceSlug,
        ]);
    }
}
```

### 1.5 Criando Cliente OAuth via Admin

Os clientes OAuth devem ser cadastrados no painel Filament ou via artisan:

```bash
# Criar cliente OAuth via comando
php artisan passport:client

# Informações necessárias:
# - Nome do cliente: "Gôndola"
# - Redirect URI: https://gondola.zetools.com.br/auth/callback
# - Confidencial: Sim
```

**Ou via Seeder:**

**database/seeders/OAuthClientSeeder.php**

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Laravel\Passport\Client;
use Illuminate\Support\Str;

class OAuthClientSeeder extends Seeder
{
    public function run(): void
    {
        // Cliente: Gôndola
        Client::create([
            'name' => 'Gôndola',
            'secret' => Str::random(40),
            'redirect' => 'https://gondola.zetools.com.br/auth/callback',
            'personal_access_client' => false,
            'password_client' => false,
            'revoked' => false,
        ]);

        // Cliente: Etiqueta
        Client::create([
            'name' => 'Etiqueta',
            'secret' => Str::random(40),
            'redirect' => 'https://etiqueta.zetools.com.br/auth/callback',
            'personal_access_client' => false,
            'password_client' => false,
            'revoked' => false,
        ]);

        // Cliente: Margem
        Client::create([
            'name' => 'Margem',
            'secret' => Str::random(40),
            'redirect' => 'https://margem.zetools.com.br/auth/callback',
            'personal_access_client' => false,
            'password_client' => false,
            'revoked' => false,
        ]);
    }
}
```

### 1.6 Interface de Autorização Customizada

**resources/views/vendor/passport/authorize.blade.php**

```blade
<x-guest-layout>
    <div class="space-y-6">
        <div class="text-center">
            <div class="w-16 h-16 bg-indigo-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <i data-lucide="shield-check" class="w-8 h-8 text-indigo-600"></i>
            </div>
            <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Autorizar Aplicação</h2>
            <p class="text-slate-500 text-sm mt-2 font-medium">
                <strong>{{ $client->name }}</strong> está solicitando acesso à sua conta ZeTools
            </p>
        </div>

        <div class="bg-slate-50 rounded-2xl p-6 space-y-3">
            <h3 class="font-semibold text-slate-800 mb-3">Esta aplicação poderá:</h3>
            @foreach ($scopes as $scope)
                <div class="flex items-center gap-3">
                    <i data-lucide="check" class="w-5 h-5 text-emerald-600"></i>
                    <span class="text-slate-600">{{ $scope->description }}</span>
                </div>
            @endforeach
        </div>

        <form method="POST" action="{{ route('passport.authorizations.approve') }}">
            @csrf
            <input type="hidden" name="state" value="{{ $request->state }}">
            <input type="hidden" name="client_id" value="{{ $client->id }}">
            <input type="hidden" name="auth_token" value="{{ $authToken }}">

            <div class="grid grid-cols-2 gap-4">
                <button type="submit" name="approve" value="1"
                    class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-2xl font-bold shadow-lg hover:shadow-xl transition-all">
                    Autorizar
                </button>

                <button type="submit" name="deny" value="1"
                    class="px-6 py-4 bg-slate-100 text-slate-700 rounded-2xl font-semibold hover:bg-slate-200 transition-colors">
                    Cancelar
                </button>
            </div>
        </form>

        <p class="text-xs text-slate-400 text-center">
            Você poderá revogar este acesso a qualquer momento nas configurações da sua conta.
        </p>
    </div>
</x-guest-layout>
```

---

## Parte 2: Implementando Cliente OAuth (Aplicação Externa)

### 2.1 Instalação do Socialite

```bash
# Na aplicação cliente (ex: Gôndola)
composer require laravel/socialite
```

### 2.2 Configuração do Provider Customizado

**config/services.php**

```php
'zetools' => [
    'client_id' => env('ZETOOLS_CLIENT_ID'),
    'client_secret' => env('ZETOOLS_CLIENT_SECRET'),
    'redirect' => env('ZETOOLS_REDIRECT_URI'),
    'base_url' => env('ZETOOLS_BASE_URL', 'https://zetools.com.br'),
],
```

**.env**

```env
ZETOOLS_CLIENT_ID=1
ZETOOLS_CLIENT_SECRET=seu_secret_aqui
ZETOOLS_REDIRECT_URI=https://gondola.zetools.com.br/auth/callback
ZETOOLS_BASE_URL=https://zetools.com.br
```

### 2.3 Provider Socialite Customizado

**app/Providers/ZeToolsProvider.php**

```php
<?php

namespace App\Providers;

use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\User;
use Laravel\Socialite\Two\ProviderInterface;

class ZeToolsProvider extends AbstractProvider implements ProviderInterface
{
    /**
     * Scopes disponíveis
     */
    protected $scopes = ['read-user', 'read-subscriptions'];

    /**
     * Separador de escopos
     */
    protected $scopeSeparator = ' ';

    /**
     * URL base do ZeTools
     */
    protected function getBaseUrl(): string
    {
        return config('services.zetools.base_url');
    }

    /**
     * URL de autorização
     */
    protected function getAuthUrl($state): string
    {
        return $this->buildAuthUrlFromBase(
            $this->getBaseUrl() . '/oauth/authorize',
            $state
        );
    }

    /**
     * URL para obter token
     */
    protected function getTokenUrl(): string
    {
        return $this->getBaseUrl() . '/oauth/token';
    }

    /**
     * URL para obter dados do usuário
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get(
            $this->getBaseUrl() . '/api/user',
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                ],
            ]
        );

        return json_decode($response->getBody(), true);
    }

    /**
     * Mapeia dados do usuário para objeto User do Socialite
     */
    protected function mapUserToObject(array $user): User
    {
        return (new User)->setRaw($user)->map([
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'avatar' => $user['avatar'] ?? null,
        ]);
    }

    /**
     * Verifica se usuário tem acesso ao serviço
     */
    public function hasAccessToService(string $token, string $serviceSlug): bool
    {
        $response = $this->getHttpClient()->get(
            $this->getBaseUrl() . "/api/user/has-access/{$serviceSlug}",
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                ],
            ]
        );

        $data = json_decode($response->getBody(), true);

        return $data['has_access'] ?? false;
    }

    /**
     * Obtém assinaturas do usuário
     */
    public function getUserSubscriptions(string $token): array
    {
        $response = $this->getHttpClient()->get(
            $this->getBaseUrl() . '/api/user/subscriptions',
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                ],
            ]
        );

        $data = json_decode($response->getBody(), true);

        return $data['subscriptions'] ?? [];
    }
}
```

### 2.4 Registrando o Provider

**app/Providers/AppServiceProvider.php**

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Facades\Socialite;
use App\Providers\ZeToolsProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Registrar provider customizado do ZeTools
        Socialite::extend('zetools', function ($app) {
            $config = $app['config']['services.zetools'];

            return Socialite::buildProvider(ZeToolsProvider::class, $config);
        });
    }
}
```

### 2.5 Rotas de Autenticação no Cliente

**routes/web.php**

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\ZeToolsAuthController;

// Redirecionar para ZeTools OAuth
Route::get('/auth/zetools', [ZeToolsAuthController::class, 'redirect'])
    ->name('auth.zetools');

// Callback do ZeTools
Route::get('/auth/callback', [ZeToolsAuthController::class, 'callback'])
    ->name('auth.zetools.callback');

// Logout
Route::post('/logout', [ZeToolsAuthController::class, 'logout'])
    ->name('logout');
```

### 2.6 Controller de Autenticação no Cliente

**app/Http/Controllers/Auth/ZeToolsAuthController.php**

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class ZeToolsAuthController extends Controller
{
    /**
     * Slug do serviço (definir conforme aplicação)
     */
    protected string $serviceSlug = 'gondola'; // ou 'etiqueta', 'margem', etc.

    /**
     * Redireciona para página de autorização do ZeTools
     */
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('zetools')
            ->scopes(['read-user', 'read-subscriptions'])
            ->redirect();
    }

    /**
     * Processa callback do ZeTools
     */
    public function callback(): RedirectResponse
    {
        try {
            // Obter usuário do ZeTools
            $zetoolsUser = Socialite::driver('zetools')->user();

            // Obter token de acesso
            $token = $zetoolsUser->token;

            // Verificar se usuário tem acesso ao serviço
            $provider = Socialite::driver('zetools');
            $hasAccess = $provider->hasAccessToService($token, $this->serviceSlug);

            if (!$hasAccess) {
                return redirect()->route('no-access')
                    ->with('error', 'Você não possui assinatura ativa deste serviço.');
            }

            // Criar ou atualizar usuário local
            $user = User::updateOrCreate(
                ['zetools_id' => $zetoolsUser->getId()],
                [
                    'name' => $zetoolsUser->getName(),
                    'email' => $zetoolsUser->getEmail(),
                    'avatar' => $zetoolsUser->getAvatar(),
                    'zetools_token' => $token,
                    'zetools_refresh_token' => $zetoolsUser->refreshToken,
                ]
            );

            // Fazer login do usuário
            Auth::login($user, true);

            return redirect()->route('dashboard')
                ->with('success', 'Login realizado com sucesso!');

        } catch (Exception $e) {
            logger()->error('ZeTools OAuth Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('login')
                ->with('error', 'Erro ao autenticar com ZeTools. Tente novamente.');
        }
    }

    /**
     * Realizar logout
     */
    public function logout(): RedirectResponse
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login');
    }
}
```

### 2.7 Migration do Usuário no Cliente

**database/migrations/xxxx_create_users_table.php**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('zetools_id')->unique();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('avatar')->nullable();
            $table->text('zetools_token')->nullable();
            $table->text('zetools_refresh_token')->nullable();
            $table->timestamps();

            $table->index('zetools_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
```

### 2.8 Middleware de Verificação de Acesso

**app/Http/Middleware/EnsureHasServiceAccess.php**

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\Response;

class EnsureHasServiceAccess
{
    protected string $serviceSlug = 'gondola'; // Configurar conforme aplicação

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->zetools_token) {
            return redirect()->route('login')
                ->with('error', 'Você precisa estar autenticado.');
        }

        // Verificar se ainda tem acesso no ZeTools
        try {
            $provider = Socialite::driver('zetools');
            $hasAccess = $provider->hasAccessToService(
                $user->zetools_token,
                $this->serviceSlug
            );

            if (!$hasAccess) {
                auth()->logout();

                return redirect()->route('login')
                    ->with('error', 'Sua assinatura expirou. Renove para continuar.');
            }
        } catch (\Exception $e) {
            // Se houver erro na verificação, permite prosseguir
            // mas loga o erro para análise
            logger()->error('Service Access Check Failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }

        return $next($request);
    }
}
```

**Registrar Middleware em `app/Http/Kernel.php`:**

```php
protected $routeMiddleware = [
    // ... outros middlewares
    'service.access' => \App\Http\Middleware\EnsureHasServiceAccess::class,
];
```

**Usar nas rotas protegidas:**

```php
Route::middleware(['auth', 'service.access'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // ... outras rotas protegidas
});
```

---

## Parte 3: Testando o Fluxo Completo

### 3.1 Testar no Provedor (ZeTools)

```bash
# 1. Criar cliente OAuth
php artisan passport:client

# 2. Listar clientes criados
php artisan tinker
>>> \Laravel\Passport\Client::all();

# 3. Testar endpoint de usuário
curl -X GET https://zetools.com.br/api/user \
  -H "Authorization: Bearer {seu_token_aqui}" \
  -H "Accept: application/json"
```

### 3.2 Testar no Cliente (Gôndola)

1. Acesse: `https://gondola.zetools.com.br/auth/zetools`
2. Será redirecionado para ZeTools
3. Faça login (se necessário)
4. Autorize o acesso
5. Será redirecionado de volta para Gôndola
6. Usuário estará autenticado

### 3.3 Debug do Fluxo

**No ZeTools (Provider):**

```php
// routes/web.php
Route::get('/oauth/debug', function () {
    return [
        'clients' => \Laravel\Passport\Client::all(),
        'tokens' => \Laravel\Passport\Token::where('revoked', false)->count(),
    ];
})->middleware('auth');
```

**No Cliente:**

```php
// routes/web.php
Route::get('/debug/auth', function () {
    $user = auth()->user();

    if (!$user) {
        return ['authenticated' => false];
    }

    $provider = Socialite::driver('zetools');

    return [
        'authenticated' => true,
        'user' => $user,
        'has_access' => $provider->hasAccessToService(
            $user->zetools_token,
            'gondola'
        ),
    ];
})->middleware('auth');
```

---

## Parte 4: Boas Práticas e Segurança

### 4.1 Renovação de Tokens

**Implementar refresh token no cliente:**

```php
public function refreshToken(User $user): bool
{
    try {
        $response = Http::asForm()->post(config('services.zetools.base_url') . '/oauth/token', [
            'grant_type' => 'refresh_token',
            'refresh_token' => $user->zetools_refresh_token,
            'client_id' => config('services.zetools.client_id'),
            'client_secret' => config('services.zetools.client_secret'),
            'scope' => 'read-user read-subscriptions',
        ]);

        if ($response->successful()) {
            $data = $response->json();

            $user->update([
                'zetools_token' => $data['access_token'],
                'zetools_refresh_token' => $data['refresh_token'] ?? $user->zetools_refresh_token,
            ]);

            return true;
        }
    } catch (\Exception $e) {
        logger()->error('Token Refresh Failed', ['error' => $e->getMessage()]);
    }

    return false;
}
```

### 4.2 Revogar Tokens

**No ZeTools:**

```php
// app/Http/Controllers/Api/TokenController.php
public function revoke(Request $request)
{
    $request->user()->token()->revoke();

    return response()->json(['message' => 'Token revogado com sucesso']);
}
```

### 4.3 Rate Limiting

**No ZeTools (routes/api.php):**

```php
Route::middleware(['auth:api', 'throttle:60,1'])->group(function () {
    Route::get('/user', [UserController::class, 'me']);
    // ... outras rotas
});
```

### 4.4 CORS

**No ZeTools (config/cors.php):**

```php
'paths' => ['api/*', 'oauth/*'],
'allowed_origins' => [
    'https://gondola.zetools.com.br',
    'https://etiqueta.zetools.com.br',
    'https://margem.zetools.com.br',
],
'allowed_methods' => ['GET', 'POST'],
'allowed_headers' => ['*'],
'supports_credentials' => true,
```

---

## Resumo do Fluxo Completo

1. **Usuário acessa Gôndola** → Clica em "Entrar com ZeTools"
2. **Gôndola redireciona** → `https://zetools.com.br/oauth/authorize?client_id=...`
3. **Usuário faz login no ZeTools** (se necessário)
4. **ZeTools mostra tela de autorização** → Usuário clica em "Autorizar"
5. **ZeTools redireciona de volta** → `https://gondola.zetools.com.br/auth/callback?code=...`
6. **Gôndola troca código por token** → POST `/oauth/token`
7. **Gôndola usa token** → GET `/api/user` para obter dados
8. **Gôndola verifica assinatura** → GET `/api/user/has-access/gondola`
9. **Gôndola cria sessão local** → Usuário autenticado

---

## Comandos Úteis

```bash
# Ver clientes OAuth cadastrados
php artisan passport:client --list

# Limpar tokens expirados
php artisan passport:purge

# Ver tokens ativos
php artisan tinker
>>> \Laravel\Passport\Token::where('revoked', false)->count();

# Revogar todos os tokens de um usuário
>>> $user = User::find(1);
>>> $user->tokens()->update(['revoked' => true]);
```

---

## Troubleshooting

### Erro: "Client authentication failed"

-   Verifique se `client_id` e `client_secret` estão corretos no `.env`
-   Confirme que o cliente existe no banco: `SELECT * FROM oauth_clients`

### Erro: "The redirect URI provided does not match"

-   Verifique se a URL de callback está exatamente igual no banco
-   Não use `http` em produção, sempre `https`

### Erro: "Unauthenticated"

-   Token pode ter expirado, implemente refresh token
-   Verifique se o header `Authorization: Bearer {token}` está correto

### Usuário não tem acesso ao serviço

-   Verifique se há assinatura ativa: `$user->hasActiveSubscription('gondola')`
-   Confirme que o serviço está ativo: `Service::where('slug', 'gondola')->first()`

---

## Referências

-   [Laravel Passport Docs](https://laravel.com/docs/10.x/passport)
-   [OAuth 2.0 RFC 6749](https://datatracker.ietf.org/doc/html/rfc6749)
-   [Laravel Socialite Docs](https://laravel.com/docs/10.x/socialite)
