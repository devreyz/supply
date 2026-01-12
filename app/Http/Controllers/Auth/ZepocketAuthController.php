<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class ZepocketAuthController extends Controller
{
    /**
     * Redireciona para a página de autenticação do ZePocket
     */
    public function redirect()
    {
        return Socialite::driver('zepocket')->redirect();
    }

    /**
     * Callback após autenticação no ZePocket
     */
    public function callback()
    {
        try {
            // Obtém os dados do usuário do ZePocket
            $zepocketUser = Socialite::driver('zepocket')->user();

            // Busca ou cria o usuário localmente
            $user = User::updateOrCreate(
                ['zepocket_id' => $zepocketUser->getId()],
                [
                    'name' => $zepocketUser->getName(),
                    'email' => $zepocketUser->getEmail(),
                    'avatar' => $zepocketUser->getAvatar(),
                    'email_verified_at' => now(),
                ]
            );

            // Se o usuário não tem empresa atual, tenta definir uma
            if (!$user->current_company_id) {
                $firstCompany = $user->companies()->first();
                
                if ($firstCompany) {
                    $user->update(['current_company_id' => $firstCompany->id]);
                } else {
                    // Se não tem empresa, cria uma empresa padrão
                    $company = Company::create([
                        'name' => $user->name . ' - Empresa',
                        'document' => '', // Será preenchido posteriormente
                        'owner_id' => $zepocketUser->getId(),
                    ]);

                    $user->companies()->attach($company->id, ['role' => 'owner']);
                    $user->update(['current_company_id' => $company->id]);
                }
            }

            // Autentica o usuário
            Auth::login($user, true);

            // Redireciona para o dashboard
            return redirect()->intended('/admin');

        } catch (\Exception $e) {
            \Log::error('ZePocket OAuth Error: ' . $e->getMessage());
            
            return redirect('/login')->with('error', 'Erro ao autenticar com ZePocket. Tente novamente.');
        }
    }

    /**
     * Logout do usuário
     */
    public function logout()
    {
        Auth::logout();
        
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/');
    }
}
