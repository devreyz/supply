<?php

use App\Models\User;
use App\Models\Slide;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CustomerController;
use App\Models\Shop\Product;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UnidadesController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Auth\SocialController;
use App\Http\Controllers\Auth\ZeToolsAuthController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

/*Route::post("/payment", [PaymentController::class, "processPayment"]);*/

// Página inicial

// Rotas Autenticadas
Route::middleware(['auth', 'service.access'])->group(function () {
    // App principal
    Route::view("/", "welcome")->name("Home");
    
    // Sistema de Cotações (Bento UI)
    Route::get('/quotes', [\App\Http\Controllers\QuoteController::class, 'index'])->name('quotes.index');
    Route::post('/quotes/quick', [\App\Http\Controllers\QuoteController::class, 'storeQuick'])->name('quotes.store-quick');
    
    // Busca de produtos (API)
    Route::get('/api/products/search', [\App\Http\Controllers\ProductController::class, 'search'])->name('products.search');
});


Route::get("lang/{lang}", function ($lang) {
  // Verifique se o idioma é válido
  $availableLocales = ["pt_BR", "en", "es"]; // Adicione outros idiomas conforme necessário

  if (in_array($lang, $availableLocales)) {
    App::setLocale($lang); // Define o idioma no backend
    session(["locale" => $lang]); // Armazena o idioma na sessão
  }

  return redirect()->back(); // Redireciona de volta para a página anterior
})->name('setLanguage');


require __DIR__ . "/auth.php";

// Rotas de autenticação ZeTools OAuth2 (Provider Principal)
Route::middleware('guest')->group(function () {
  Route::get('auth/zetools', [ZeToolsAuthController::class, 'redirect'])->name('auth.zetools');
  Route::get('auth/callback', [ZeToolsAuthController::class, 'callback'])->name('auth.zetools.callback');
  // Switch account: logout and redirect to ZeTools for new login
  Route::get('auth/switch', [ZeToolsAuthController::class, 'switch'])->name('auth.switch');
});

Route::post('logout', [ZeToolsAuthController::class, 'logout'])->name('logout')->middleware('auth');

// Rotas de login Social (Google) - Desabilitadas, usar apenas ZeTools
/*
Route::middleware('guest')->group(function () {
  Route::get('auth/google', [SocialController::class, 'redirectToGoogle'])->name('auth.google');
  Route::get('auth/google/callback', [SocialController::class, 'handleGoogleCallback'])->name('auth.google.callback');
  // Compatibilidade com rota /login/google usada por callback local
  Route::get('login/google', [SocialController::class, 'redirectToGoogle'])->name('login.google');
  Route::get('login/google/callback', [SocialController::class, 'handleGoogleCallback'])->name('login.google.callback');
});
*/
