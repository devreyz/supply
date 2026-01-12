<?php

namespace App\Providers;

use App\Services\Socialite\ZepocketProvider;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Contracts\Factory as SocialiteFactory;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class AppServiceProvider extends ServiceProvider
{
  /**
   * Register any application services.
   */
  public function register(): void
  {
    //
  }

  /**
   * Bootstrap any application services.
   */
  public function boot(): void
  {
    // $accessToken = env("MERCADO_PAGO_ACCESS_TOKEN");
    // if (!$accessToken) {
    //   throw new \Exception(
    //     env("MERCADO_PAGO_ACCESS_TOKEN")
    //   );
    // }

    // MercadoPagoConfig::setAccessToken($accessToken);
    // Set the application locale based on the session or default to 'pt_BR'
    $locale = Session::get('locale', 'pt_BR');
    App::setLocale($locale);

    // Check if the request has a 'lang' parameter and update the session
    if (request()->has('lang')) {
      $lang = request()->get('lang');
      Session::put('locale', $lang);
      App::setLocale($lang);
    }

    Blade::if("role", function ($role) {
      return auth()->check() &&
        auth()
        ->user()
        ->hasRole($role);
    });

    // Registrar o provider customizado do Socialite para ZePocket
    $socialite = $this->app->make(SocialiteFactory::class);
    $socialite->extend('zepocket', function ($app) use ($socialite) {
      $config = $app['config']['services.zepocket'];
      return $socialite->buildProvider(ZepocketProvider::class, $config);
    });
  }
}

