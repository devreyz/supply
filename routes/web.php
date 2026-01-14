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
    
    // ZePocket Supply - Gestão de Compras
    Route::get('/zepocket', function () {
        return view('zepocket');
    })->name('zepocket');
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
