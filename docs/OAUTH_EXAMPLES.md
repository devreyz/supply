# Exemplos de Código - ZeTools OAuth

## Exemplo 1: Provider Customizado Completo

```php
<?php
// app/Providers/ZeToolsProvider.php

namespace App\Providers;

use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\User;
use Laravel\Socialite\Two\ProviderInterface;
use Illuminate\Support\Facades\Http;

class ZeToolsProvider extends AbstractProvider implements ProviderInterface
{
    protected $scopes = ['read-user', 'read-subscriptions'];
    protected $scopeSeparator = ' ';

    protected function getBaseUrl(): string
    {
        return config('services.zetools.base_url', 'https://zetools.com.br');
    }

    protected function getAuthUrl($state): string
    {
        return $this->buildAuthUrlFromBase(
            $this->getBaseUrl() . '/oauth/authorize',
            $state
        );
    }

    protected function getTokenUrl(): string
    {
        return $this->getBaseUrl() . '/oauth/token';
    }

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

    protected function mapUserToObject(array $user): User
    {
        return (new User)->setRaw($user)->map([
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'avatar' => $user['avatar'] ?? null,
        ]);
    }

    public function hasAccessToService(string $token, string $serviceSlug): bool
    {
        try {
            $response = Http::withToken($token)
                ->get($this->getBaseUrl() . "/api/user/has-access/{$serviceSlug}");

            return $response->successful() && ($response->json('has_access') ?? false);
        } catch (\Exception $e) {
            logger()->error('ZeTools Access Check Failed', [
                'service' => $serviceSlug,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    public function getUserSubscriptions(string $token): array
    {
        try {
            $response = Http::withToken($token)
                ->get($this->getBaseUrl() . '/api/user/subscriptions');

            return $response->successful()
                ? ($response->json('subscriptions') ?? [])
                : [];
        } catch (\Exception $e) {
            logger()->error('ZeTools Subscriptions Fetch Failed', [
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }
}
```

---

## Exemplo 2: Controller de Autenticação Completo

```php
<?php
// app/Http/Controllers/Auth/ZeToolsAuthController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class ZeToolsAuthController extends Controller
{
    protected string $serviceSlug = 'gondola'; // Alterar conforme aplicação

    public function redirect(): RedirectResponse
    {
        return Socialite::driver('zetools')
            ->scopes(['read-user', 'read-subscriptions'])
            ->with(['state' => base64_encode(json_encode([
                'timestamp' => now()->timestamp,
                'service' => $this->serviceSlug,
            ]))])
            ->redirect();
    }

    public function callback(): RedirectResponse
    {
        try {
            $zetoolsUser = Socialite::driver('zetools')->user();
            $token = $zetoolsUser->token;

            // Verificar acesso
            $provider = Socialite::driver('zetools');
            if (!$provider->hasAccessToService($token, $this->serviceSlug)) {
                return redirect()->route('no-access')
                    ->with('error', 'Você não possui assinatura ativa deste serviço.');
            }

            // Criar/atualizar usuário
            $user = User::updateOrCreate(
                ['zetools_id' => $zetoolsUser->getId()],
                [
                    'name' => $zetoolsUser->getName(),
                    'email' => $zetoolsUser->getEmail(),
                    'avatar' => $zetoolsUser->getAvatar(),
                    'zetools_token' => $token,
                    'zetools_refresh_token' => $zetoolsUser->refreshToken,
                    'token_expires_at' => now()->addDays(15),
                ]
            );

            // Obter assinaturas e armazenar
            $subscriptions = $provider->getUserSubscriptions($token);
            $user->update([
                'subscriptions_cache' => json_encode($subscriptions),
                'subscriptions_cached_at' => now(),
            ]);

            Auth::login($user, true);

            return redirect()->route('dashboard')
                ->with('success', 'Bem-vindo ao ' . config('app.name') . '!');

        } catch (Exception $e) {
            logger()->error('ZeTools OAuth Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('login')
                ->with('error', 'Erro ao autenticar. Tente novamente.');
        }
    }

    public function logout(): RedirectResponse
    {
        $user = Auth::user();

        if ($user && $user->zetools_token) {
            // Revogar token no ZeTools (opcional)
            try {
                Http::withToken($user->zetools_token)
                    ->post(config('services.zetools.base_url') . '/api/token/revoke');
            } catch (Exception $e) {
                logger()->warning('Failed to revoke ZeTools token', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Você saiu com sucesso.');
    }

    public function refreshToken(User $user): bool
    {
        try {
            $response = Http::asForm()->post(
                config('services.zetools.base_url') . '/oauth/token',
                [
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $user->zetools_refresh_token,
                    'client_id' => config('services.zetools.client_id'),
                    'client_secret' => config('services.zetools.client_secret'),
                    'scope' => 'read-user read-subscriptions',
                ]
            );

            if ($response->successful()) {
                $data = $response->json();

                $user->update([
                    'zetools_token' => $data['access_token'],
                    'zetools_refresh_token' => $data['refresh_token'] ?? $user->zetools_refresh_token,
                    'token_expires_at' => now()->addDays(15),
                ]);

                return true;
            }
        } catch (Exception $e) {
            logger()->error('Token Refresh Failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }

        return false;
    }
}
```

---

## Exemplo 3: Middleware de Verificação de Acesso

```php
<?php
// app/Http/Middleware/EnsureHasServiceAccess.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\Response;

class EnsureHasServiceAccess
{
    protected string $serviceSlug = 'gondola';

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->zetools_token) {
            return redirect()->route('login')
                ->with('error', 'Você precisa estar autenticado.');
        }

        // Verificar se token expirou
        if ($user->token_expires_at && $user->token_expires_at->isPast()) {
            $controller = app(ZeToolsAuthController::class);

            if (!$controller->refreshToken($user)) {
                auth()->logout();
                return redirect()->route('login')
                    ->with('error', 'Sua sessão expirou. Faça login novamente.');
            }

            // Recarregar usuário após refresh
            $user->refresh();
        }

        // Cache da verificação de acesso (5 minutos)
        $cacheKey = "user_{$user->id}_access_{$this->serviceSlug}";

        $hasAccess = Cache::remember($cacheKey, 300, function () use ($user) {
            try {
                $provider = Socialite::driver('zetools');
                return $provider->hasAccessToService(
                    $user->zetools_token,
                    $this->serviceSlug
                );
            } catch (\Exception $e) {
                logger()->error('Service Access Check Failed', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);

                // Em caso de erro, permite acesso (fail open)
                // Mas loga para análise posterior
                return true;
            }
        });

        if (!$hasAccess) {
            Cache::forget($cacheKey);
            auth()->logout();

            return redirect()->route('subscription-required')
                ->with('error', 'Sua assinatura expirou ou foi cancelada.');
        }

        return $next($request);
    }
}
```

---

## Exemplo 4: API Controller no ZeTools (Provider)

```php
<?php
// app/Http/Controllers/Api/OAuth/UserController.php

namespace App\Http\Controllers\Api\OAuth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\UserResource;
use App\Http\Resources\SubscriptionResource;

class UserController extends Controller
{
    /**
     * Retorna informações do usuário autenticado via OAuth
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json(new UserResource($request->user()));
    }

    /**
     * Retorna assinaturas ativas do usuário
     */
    public function subscriptions(Request $request): JsonResponse
    {
        $user = $request->user();

        $subscriptions = $user->subscriptions()
            ->with(['service:id,name,slug,icon', 'plan:id,name'])
            ->active()
            ->get();

        return response()->json([
            'subscriptions' => SubscriptionResource::collection($subscriptions),
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
            'checked_at' => now()->toIso8601String(),
        ]);
    }

    /**
     * Retorna lista de todos os serviços e status de acesso
     */
    public function allServices(Request $request): JsonResponse
    {
        $user = $request->user();
        $services = \App\Models\Service::where('is_active', true)->get();

        $servicesData = $services->map(function ($service) use ($user) {
            return [
                'slug' => $service->slug,
                'name' => $service->name,
                'icon' => $service->icon,
                'url' => $service->full_url,
                'has_access' => $user->hasActiveSubscription($service->slug),
            ];
        });

        return response()->json([
            'services' => $servicesData,
        ]);
    }

    /**
     * Revoga o token atual
     */
    public function revokeToken(Request $request): JsonResponse
    {
        $request->user()->token()->revoke();

        return response()->json([
            'message' => 'Token revogado com sucesso',
        ]);
    }
}
```

---

## Exemplo 5: Resources para API

```php
<?php
// app/Http/Resources/UserResource.php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'avatar' => $this->avatar,
            'roles' => $this->roles->pluck('name'),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
```

```php
<?php
// app/Http/Resources/SubscriptionResource.php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'service' => [
                'slug' => $this->service->slug,
                'name' => $this->service->name,
                'icon' => $this->service->icon,
                'url' => $this->service->full_url,
            ],
            'plan' => [
                'name' => $this->plan->name ?? null,
                'price' => $this->plan->price ?? null,
            ],
            'status' => $this->status,
            'type' => $this->type,
            'starts_at' => $this->starts_at->toIso8601String(),
            'expires_at' => $this->expires_at?->toIso8601String(),
            'is_active' => $this->isActive(),
        ];
    }
}
```

---

## Exemplo 6: Testes Automatizados

```php
<?php
// tests/Feature/OAuthTest.php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Service;
use App\Models\Subscription;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_get_their_info_via_api()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $response = $this->getJson('/api/user');

        $response->assertOk()
            ->assertJsonStructure([
                'id', 'name', 'email', 'avatar', 'roles', 'created_at'
            ]);
    }

    public function test_user_can_check_service_access()
    {
        $user = User::factory()->create();
        $service = Service::factory()->create(['slug' => 'gondola']);

        Subscription::factory()->create([
            'user_id' => $user->id,
            'service_id' => $service->id,
            'status' => 'active',
            'expires_at' => now()->addDays(30),
        ]);

        Passport::actingAs($user);

        $response = $this->getJson('/api/user/has-access/gondola');

        $response->assertOk()
            ->assertJson([
                'has_access' => true,
                'service' => 'gondola',
            ]);
    }

    public function test_unauthenticated_user_cannot_access_api()
    {
        $response = $this->getJson('/api/user');

        $response->assertUnauthorized();
    }

    public function test_user_without_subscription_has_no_access()
    {
        $user = User::factory()->create();
        Service::factory()->create(['slug' => 'gondola']);

        Passport::actingAs($user);

        $response = $this->getJson('/api/user/has-access/gondola');

        $response->assertOk()
            ->assertJson([
                'has_access' => false,
            ]);
    }
}
```

---

## Exemplo 7: Vue.js Integration (Frontend)

```vue
<!-- resources/js/components/ZeToolsLogin.vue -->
<template>
    <div
        class="flex items-center justify-center min-h-screen bg-gradient-to-br from-indigo-50 to-purple-50"
    >
        <div class="bento-card max-w-md w-full p-8">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold gradient-text mb-2">
                    Bem-vindo ao {{ appName }}
                </h1>
                <p class="text-slate-600">Faça login com sua conta ZeTools</p>
            </div>

            <button
                @click="loginWithZeTools"
                :disabled="loading"
                class="w-full py-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-2xl font-bold hover:shadow-xl transition-all disabled:opacity-50"
            >
                <span v-if="!loading">Entrar com ZeTools</span>
                <span v-else>Redirecionando...</span>
            </button>

            <p class="text-xs text-slate-400 text-center mt-6">
                Você será redirecionado para zetools.com.br
            </p>
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";

const props = defineProps({
    appName: { type: String, required: true },
});

const loading = ref(false);

const loginWithZeTools = () => {
    loading.value = true;
    window.location.href = "/auth/zetools";
};
</script>
```

---

## Exemplo 8: Blade Component Reutilizável

```blade
<!-- resources/views/components/zetools-button.blade.php -->
@props(['size' => 'md'])

@php
$sizeClasses = [
    'sm' => 'px-4 py-2 text-sm',
    'md' => 'px-6 py-4 text-base',
    'lg' => 'px-8 py-5 text-lg',
];
@endphp

<a href="{{ route('auth.zetools') }}"
   {{ $attributes->merge(['class' => "inline-flex items-center justify-center gap-3 {$sizeClasses[$size]} bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-2xl font-bold shadow-lg hover:shadow-xl transition-all hover:scale-105 active:scale-95"]) }}>

    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <rect x="3" y="3" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2"/>
        <path d="M3 9h18M9 21V9" stroke="currentColor" stroke-width="2"/>
    </svg>

    {{ $slot ?? 'Entrar com ZeTools' }}
</a>
```

**Uso:**

```blade
<!-- Tamanho padrão -->
<x-zetools-button />

<!-- Tamanho customizado -->
<x-zetools-button size="lg">
    Fazer Login
</x-zetools-button>

<!-- Com classes adicionais -->
<x-zetools-button class="w-full">
    Continuar
</x-zetools-button>
```

---

Consulte a documentação completa em [docs/OAUTH_SETUP.md](../OAUTH_SETUP.md)
