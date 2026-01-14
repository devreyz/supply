<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\Response;

class EnsureHasServiceAccess
{
    /**
     * Verificar se usuário tem acesso ao serviço
     * Sistema otimizado com validação local e revalidação periódica
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Se não estiver autenticado, deixa o middleware 'auth' lidar
        if (!$user) {
            return $next($request);
        }

        // 1. Verificar se token OAuth expirou
        if ($user->token_expires_at && $user->token_expires_at->isPast()) {
            auth()->logout();
            return redirect()->route('login')
                ->with('error', 'Sua sessão expirou. Por favor, faça login novamente.');
        }

        // 2. Verificar se tem token ZeTools válido
        if (!$user->zetools_token || !$user->zetools_id) {
            auth()->logout();
            return redirect()->route('login')
                ->with('error', 'Acesso não autorizado. Faça login pelo ZeTools.');
        }

        // 3. Verificar se a assinatura expirou (validação local, sem requisição HTTP)
        if ($user->service_access_expires_at && $user->service_access_expires_at->isPast()) {
            auth()->logout();
            return redirect()->route('login')
                ->with('error', 'Sua assinatura do Gôndola expirou. Por favor, renove no ZeTools.');
        }

        // 4. Revalidação periódica (1x por dia) para sincronizar com ZeTools
        // Isso evita requisições a cada page load, mas mantém a sincronia
        if (!$user->last_access_check_at || $user->last_access_check_at->addDay()->isPast()) {
            $this->revalidateAccess($user);
        }

        return $next($request);
    }

    /**
     * Revalidar acesso com ZeTools (chamado apenas 1x por dia)
     */
    protected function revalidateAccess($user): void
    {
        try {
            // Fazer requisição ao ZeTools para verificar status atualizado
            $provider = Socialite::driver('zetools');
            $hasAccess = $provider->hasAccessToService($user->zetools_token, 'gondola');

            if (!$hasAccess) {
                // Acesso foi revogado, marcar para expiração imediata
                $user->update([
                    'service_access_expires_at' => now()->subDay(),
                    'last_access_check_at' => now(),
                ]);
                return;
            }

            // Buscar dados atualizados da assinatura
            $response = Http::withToken($user->zetools_token)
                ->get(config('services.zetools.base_url') . '/api/user');

            if ($response->successful()) {
                $userData = $response->json();
                $subscriptionData = $userData['subscription_status'] ?? [];
                $gondolaSubscription = collect($subscriptionData)->firstWhere('service_slug', 'gondola');
                $serviceExpiresAt = $gondolaSubscription['expires_at'] ?? null;

                $user->update([
                    'service_access_expires_at' => $serviceExpiresAt 
                        ? \Carbon\Carbon::parse($serviceExpiresAt) 
                        : now()->addDays(30),
                    'last_access_check_at' => now(),
                    'subscriptions_cache' => $subscriptionData,
                ]);
            } else {
                // Erro na requisição, manter data atual e tentar novamente depois
                $user->update(['last_access_check_at' => now()]);
            }

        } catch (\Exception $e) {
            logger()->error('Erro na revalidação de acesso', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            // Em caso de erro, não bloquear o usuário, apenas atualizar timestamp
            $user->update(['last_access_check_at' => now()]);
        }
    }
}
