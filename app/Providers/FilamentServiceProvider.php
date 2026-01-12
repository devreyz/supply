<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;
use Filament\Navigation\NavigationGroup;

class FilamentServiceProvider extends ServiceProvider
{
  public function boot(): void
  {
    NavigationGroup::make()
      ->label("Usuários")
      ->icon("heroicon-o-user-group"); // Ícone do grupo "Usuários"

    NavigationGroup::make()
      ->label("Produtos")
      ->icon("heroicon-o-cube"); // Ícone do grupo "Produtos"

    NavigationGroup::make()
      ->label("Administração")
      ->icon("heroicon-o-shield-check"); // Ícone do grupo "Administração"

    // Obtém o idioma salvo na sessão ou o idioma padrão do Laravel
    $locale = Session::get("locale", config("app.locale"));

    // Define o idioma global do Laravel
    App::setLocale($locale);

    // Define o idioma do Filament nos painéis
    Filament::registerRenderHook(
      "panels::body.start",
      fn() => Filament::getCurrentPanel()?->setLocale($locale)
    );
  }
}
