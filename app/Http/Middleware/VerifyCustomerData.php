<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifyCustomerData
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Verifica se o usuário está autenticado
        if ($user) {
            // Verifica se o usuário tem um registro de customer
            if (!$user->customer) {
                // Redireciona para a página de criação de dados de cliente
                return redirect()
                    ->route('customer.create')
                    ->with('message', 'Por favor, crie seu perfil de cliente antes de prosseguir com a compra.');
            }

            $customer = $user->customer;

            // Verifica se os campos necessários do customer estão preenchidos
            if (empty($customer->first_name) || empty($customer->last_name) || empty($customer->country)) {
                // Redireciona para a página de atualização de dados
                return redirect()
                    ->route('customer.create')
                    ->with('message', 'Por favor, complete os seus dados antes de prosseguir com a compra.');
            }
        }

        return $next($request);
    }
}