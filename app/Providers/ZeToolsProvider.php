<?php

namespace App\Providers;

use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\User;
use Laravel\Socialite\Two\ProviderInterface;
use Illuminate\Support\Facades\Http;

class ZeToolsProvider extends AbstractProvider implements ProviderInterface
{
    protected $scopes = ['read-user', 'read-subscriptions'];
    protected $scopeSeparator = ' ';

    /**
     * Obter a URL base do ZeTools
     */
    protected function getBaseUrl(): string
    {
        return config('services.zetools.base_url', 'http://localhost:8000');
    }

    /**
     * URL de autorização OAuth
     */
    protected function getAuthUrl($state): string
    {
        return $this->buildAuthUrlFromBase(
            $this->getBaseUrl() . '/oauth/authorize',
            $state
        );
    }

    /**
     * URL para troca de código por token
     */
    protected function getTokenUrl(): string
    {
        return $this->getBaseUrl() . '/oauth/token';
    }

    /**
     * Obter dados do usuário usando o token
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
     * Mapear dados do usuário para objeto User do Socialite
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
     * Verificar se usuário tem acesso a um serviço específico
     */
    public function hasAccessToService(string $token, string $serviceSlug): bool
    {
        try {
            $response = Http::withToken($token)
                ->acceptJson()
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

    /**
     * Obter assinaturas ativas do usuário
     */
    public function getUserSubscriptions(string $token): array
    {
        try {
            $response = Http::withToken($token)
                ->acceptJson()
                ->get($this->getBaseUrl() . '/api/user/subscriptions');

            if ($response->successful()) {
                return $response->json('subscriptions') ?? [];
            }

            return [];
        } catch (\Exception $e) {
            logger()->error('ZeTools Subscriptions Fetch Failed', [
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    /**
     * Renovar token usando refresh token
     */
    public function refreshAccessToken(string $refreshToken): ?array
    {
        try {
            $response = Http::asForm()->post($this->getTokenUrl(), [
                'grant_type' => 'refresh_token',
                'refresh_token' => $refreshToken,
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'scope' => implode(' ', $this->scopes),
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            logger()->error('Token Refresh Failed', [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }
}
