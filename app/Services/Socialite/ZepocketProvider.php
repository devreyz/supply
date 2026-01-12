<?php

namespace App\Services\Socialite;

use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\User;

class ZepocketProvider extends AbstractProvider implements ProviderInterface
{
    /**
     * O escopo padrão para autenticação.
     */
    protected $scopes = ['profile', 'email'];

    /**
     * O separador de escopo.
     */
    protected $scopeSeparator = ' ';

    /**
     * Obtém a URL de autorização.
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase(
            config('services.zepocket.base_url') . '/oauth/authorize',
            $state
        );
    }

    /**
     * Obtém a URL do token.
     */
    protected function getTokenUrl()
    {
        return config('services.zepocket.base_url') . '/oauth/token';
    }

    /**
     * Obtém os dados do usuário autenticado.
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get(
            config('services.zepocket.base_url') . '/api/user',
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
     * Mapeia os dados do usuário para o objeto User do Socialite.
     */
    protected function mapUserToObject(array $user)
    {
        return (new User)->setRaw($user)->map([
            'id' => $user['id'] ?? null,
            'nickname' => $user['username'] ?? null,
            'name' => $user['name'] ?? null,
            'email' => $user['email'] ?? null,
            'avatar' => $user['avatar'] ?? null,
        ]);
    }

    /**
     * Obtém os campos do token de acesso.
     */
    protected function getTokenFields($code)
    {
        return array_merge(parent::getTokenFields($code), [
            'grant_type' => 'authorization_code',
        ]);
    }
}
