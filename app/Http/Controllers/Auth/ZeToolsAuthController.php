<?php

// ...existing code...

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Laravel\Socialite\Facades\Socialite;

class ZeToolsAuthController extends Controller
{
    protected string $serviceSlug = 'gondola';

    public function redirect(): RedirectResponse
    {
        // O método scopes() existe no Socialite, mas deve ser chamado na instância do driver
        return Socialite::driver('zetools')
            ->scopes(['read-user'])
            ->redirect();
    }

    public function callback(): RedirectResponse
    {
        try {
            logger()->info('Iniciando callback OAuth ZeTools');

            // 1. Obter usuário do Socialite
            $zetoolsUser = Socialite::driver('zetools')->user();

            // 2. Obter dados brutos que o ZeTools enviou (incluindo o que adicionamos na API)
            $userData = $zetoolsUser->getRaw();

            logger()->info('Dados recebidos do ZeTools', [
                'email' => $zetoolsUser->email,
                'has_active_gondola' => $userData['has_active_gondola'] ?? 'não definido',
            ]);

            // 3. Verificar acesso (usando o campo que criamos no ZeTools)
            // Se for admin no ZeTools, o campo has_active_gondola virá true pelo bypass que criamos
            $hasAccess = $userData['has_active_gondola'] ?? false;

            if (! $hasAccess) {
                logger()->warning('Acesso negado: Usuário sem assinatura ativa', ['email' => $zetoolsUser->email]);

                // Redireciona para a tela de login indicando falta de assinatura
                return redirect()->route('login')
                    ->with('error', 'Você não tem assinatura ativa para acessar o Gôndola. Por favor, assine no ZeTools.')
                    ->with('no_subscription', true)
                    ->with('checkout_url', url( env("ZETOOLS_BASE_URL") . '/checkout/gondola'));
            }

            // 4. Criar ou atualizar usuário local
            // Primeiro tentamos pelo zetools_id, se não encontrar, tentamos pelo email para vincular a conta
            $user = User::where('zetools_id', $zetoolsUser->id)->first();

            if (! $user) {
                $user = User::where('email', $zetoolsUser->email)->first();
            }

            // Extrair data de expiração da assinatura do Gôndola
            $subscriptionData = $userData['subscription_status'] ?? [];
            $gondolaSubscription = collect($subscriptionData)->firstWhere('service_slug', 'gondola');
            $serviceExpiresAt = $gondolaSubscription['expires_at'] ?? null;

            // Se não tiver data de expiração, assumir 30 dias (ou null para acesso vitalício)
            $serviceAccessExpires = $serviceExpiresAt
                ? \Carbon\Carbon::parse($serviceExpiresAt)
                : now()->addDays(30);

            $userDataToUpdate = [
                'zetools_id' => $zetoolsUser->id,
                'name' => $zetoolsUser->name,
                'email' => $zetoolsUser->email,
                'avatar' => $zetoolsUser->avatar,
                'zetools_token' => $zetoolsUser->token,
                'zetools_refresh_token' => $zetoolsUser->refreshToken,
                'token_expires_at' => now()->addSeconds($zetoolsUser->expiresIn ?? 1296000),
                'service_access_expires_at' => $serviceAccessExpires,
                'last_access_check_at' => now(),
                'subscriptions_cache' => $subscriptionData, // Salva o array completo
                'email_verified_at' => now(), // Usuário vindo do OAuth é considerado verificado
            ];

            if ($user) {
                $user->update($userDataToUpdate);
            } else {
                $user = User::create($userDataToUpdate);
            }

            Auth::login($user, true);

            logger()->info('Login ZeTools realizado com sucesso', ['user_id' => $user->id]);

            return redirect()->intended('/')
                ->with('success', 'Bem-vindo ao Gôndola!');

        } catch (Exception $e) {
            logger()->error('Erro Crítico no OAuth ZeTools', [
                'mensagem' => $e->getMessage(),
                'linha' => $e->getLine(),
            ]);

            return redirect()->route('login')
                ->with('error', 'Falha na comunicação com ZeTools.');
        }
    }

    public function logout(): RedirectResponse
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Sessão encerrada.');
    }

    /**
     * Switch account: logout current user (if any) and redirect to ZeTools OAuth
     */
    public function switch(): RedirectResponse
    {
        if (Auth::check()) {
            Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
        }

        return redirect()->route('auth.zetools');
    }

    /**
     * Exemplo de como renovar o token usando a Facade Http padrão
     */
    public function refreshToken(User $user): bool
    {
        try {
            $response = Http::asForm()->post(config('services.zetools.base_url').'/oauth/token', [
                'grant_type' => 'refresh_token',
                'refresh_token' => $user->zetools_refresh_token,
                'client_id' => config('services.zetools.client_id'),
                'client_secret' => config('services.zetools.client_secret'),
                'scope' => 'read-user',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $user->update([
                    'zetools_token' => $data['access_token'],
                    'zetools_refresh_token' => $data['refresh_token'] ?? $user->zetools_refresh_token,
                    'token_expires_at' => now()->addSeconds($data['expires_in']),
                ]);

                return true;
            }
        } catch (Exception $e) {
            logger()->error('Falha ao renovar token', ['id' => $user->id, 'erro' => $e->getMessage()]);
        }

        return false;
    }
}
