<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureHasServiceAccess
{
    /**
     * Verificar se usuário tem acesso ao serviço
     * Verificação simplificada - o acesso já foi validado no login OAuth
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Se não estiver autenticado, deixa o middleware 'auth' lidar
        if (! $user) {
            return $next($request);
        }

        // Verificar se token OAuth expirou (sem fazer requisições extras)
        if ($user->token_expires_at && $user->token_expires_at->isPast()) {
            auth()->logout();

            return redirect()->route('login')
                ->with('error', 'Sua sessão expirou. Por favor, faça login novamente.');
        }

        // Verificar se tem token ZeTools válido
        // A verificação de acesso já foi feita no momento do login OAuth
        if (! $user->zetools_token || ! $user->zetools_id) {
            auth()->logout();

            return redirect()->route('login')
                ->with('error', 'Acesso não autorizado. Faça login pelo ZeTools.');
        }

        return $next($request);
    }
}
