<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    public function handle($request, Closure $next)
    {
        // Recupera o idioma da sessão ou usa 'pt_BR' como padrão
        $locale = Session::get('locale', 'pt_BR');

        // Verifica se há um parâmetro 'lang' na URL
        if ($request->has('lang')) {
            $locale = $request->get('lang'); // Obtém o valor de 'lang'
            Session::put('locale', $locale); // Armazena na sessão
        }

        // Define o idioma da aplicação
        App::setLocale($locale);

        return $next($request);
    }
}