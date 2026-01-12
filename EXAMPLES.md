# 🔌 ZePocket Gôndola - Exemplos de Código

## 📋 Índice

1. [Trabalhando com Models](#trabalhando-com-models)
2. [Sistema de Cotações](#sistema-de-cotações)
3. [Multi-tenancy](#multi-tenancy)
4. [Scopes Úteis](#scopes-úteis)
5. [Filament Customizações](#filament-customizações)

---

## Trabalhando com Models

### Criar Produto com Código EAN

```php
use App\Models\Product;

$product = Product::create([
    'company_id' => auth()->user()->current_company_id,
    'category_id' => 1,
    'name' => 'Arroz Tio João 5kg',
    'unit' => 'CX',
    'min_stock' => 10,
    'is_active' => true,
]);

// Adicionar código EAN
$product->codes()->create([
    'code' => '7891234567890',
    'type' => 'ean',
]);

// Acessar EAN via attribute
echo $product->ean; // 7891234567890
```

### Buscar Produto por Código

```php
use App\Models\Product;

// Busca por EAN
$product = Product::whereHas('codes', function ($query) {
    $query->where('code', '7891234567890');
})->first();

// Usando scope de busca
$products = Product::search('Arroz')->get();
```

### Criar Fornecedor

```php
use App\Models\Supplier;

$supplier = Supplier::create([
    'company_id' => auth()->user()->current_company_id,
    'name' => 'Atacadão Norte',
    'contact_name' => 'João Silva',
    'phone' => '(11) 98765-4321',
    'email' => 'contato@atacadaonorte.com.br',
    'rating' => 4,
]);
```

---

## Sistema de Cotações

### Criar Cotação Completa

```php
use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\QuoteResponse;

// 1. Criar cotação
$quote = Quote::create([
    'company_id' => auth()->user()->current_company_id,
    'created_by' => auth()->id(),
    'title' => 'Compra Semanal - ' . now()->format('d/m/Y'),
    'status' => 'open',
    'deadline' => now()->addDays(3),
]);

// 2. Adicionar produtos
$items = [
    ['product_id' => 1, 'quantity' => 10],
    ['product_id' => 2, 'quantity' => 5],
    ['product_id' => 3, 'quantity' => 20],
];

foreach ($items as $item) {
    $quote->items()->create($item);
}

// 3. Receber resposta de fornecedor
$response = $quote->responses()->create([
    'supplier_id' => 1,
    'status' => 'submitted',
    'submitted_at' => now(),
]);

// 4. Adicionar preços por item
$response->items()->create([
    'quote_item_id' => 1,
    'unit_price' => 18.50,
]);

$response->items()->create([
    'quote_item_id' => 2,
    'unit_price' => 7.90,
]);

// 5. Calcular total automaticamente
$response->calculateTotal();
```

### Comparar Preços entre Fornecedores

```php
$quote = Quote::with(['items.product', 'responses.supplier', 'responses.items'])->find(1);

$comparison = [];

foreach ($quote->items as $item) {
    $comparison[$item->product->name] = [];

    foreach ($quote->responses as $response) {
        $responseItem = $response->items->where('quote_item_id', $item->id)->first();

        if ($responseItem) {
            $comparison[$item->product->name][$response->supplier->name] = [
                'price' => $responseItem->unit_price,
                'subtotal' => $responseItem->subtotal,
            ];
        }
    }
}

// Resultado:
// [
//     'Arroz Tio João 5kg' => [
//         'Atacadão Norte' => ['price' => 18.50, 'subtotal' => 185.00],
//         'Distribuidora Silva' => ['price' => 17.90, 'subtotal' => 179.00],
//     ],
//     ...
// ]
```

### Obter Melhor Preço por Produto

```php
use App\Models\QuoteItem;

$item = QuoteItem::with('responseItems.quoteResponse')->find(1);

// Melhor preço via attribute
$bestPrice = $item->best_price;

// Ou manualmente
$bestPrice = $item->responseItems()
    ->whereHas('quoteResponse', fn($q) => $q->where('status', 'submitted'))
    ->min('unit_price');
```

### Fechar Cotação e Selecionar Fornecedor

```php
$quote = Quote::find(1);

// Encontrar melhor resposta (menor preço total)
$bestResponse = $quote->best_response;

// Criar comparação
$quote->comparisons()->create([
    'selected_response_id' => $bestResponse->id,
    'comparison_data' => [
        'total_savings' => 150.00,
        'alternative_suppliers' => 3,
    ],
    'compared_at' => now(),
]);

// Fechar cotação
$quote->update(['status' => 'closed']);
```

---

## Multi-tenancy

### Verificar Empresa Atual

```php
// No controller
$companyId = auth()->user()->current_company_id;

// Filtrar dados
$products = Product::where('company_id', $companyId)->get();
```

### Trocar Empresa Ativa

```php
use App\Models\Company;

$company = Company::find(2);

// Verificar se usuário tem acesso
if (auth()->user()->companies->contains($company)) {
    auth()->user()->update(['current_company_id' => $company->id]);
}
```

### Listar Empresas do Usuário

```php
$companies = auth()->user()->companies()
    ->withPivot('role')
    ->get();

foreach ($companies as $company) {
    echo $company->name . ' - ' . $company->pivot->role;
}
```

### Verificar Permissões

```php
$user = auth()->user();
$company = Company::find(1);

// É owner?
if ($user->isOwnerOf($company)) {
    // Tem permissão total
}

// É admin?
if ($user->isAdminOf($company)) {
    // Tem permissões administrativas
}
```

---

## Scopes Úteis

### Filtrar por Empresa

```php
// Produtos da empresa atual
$products = Product::forCompany(auth()->user()->current_company_id)->get();

// Fornecedores ativos
$suppliers = Supplier::forCompany($companyId)->highRated(4)->get();

// Cotações abertas
$openQuotes = Quote::forCompany($companyId)->open()->get();
```

### Busca de Produtos

```php
// Busca por nome ou código
$products = Product::search('Arroz')->active()->get();

// Com categoria
$products = Product::forCompany($companyId)
    ->where('category_id', 1)
    ->active()
    ->get();
```

### Categorias Hierárquicas

```php
use App\Models\Category;

// Categorias raiz
$rootCategories = Category::forCompany($companyId)->roots()->get();

// Com subcategorias
$categories = Category::with('children')->roots()->get();

foreach ($categories as $category) {
    echo $category->name;
    foreach ($category->children as $child) {
        echo '  - ' . $child->name;
    }
}
```

---

## Filament Customizações

### Adicionar Campo Condicional

```php
Forms\Components\Select::make('category_id')
    ->label('Categoria')
    ->relationship('category', 'name')
    ->reactive()
    ->afterStateUpdated(function ($state, callable $set) {
        // Limpar subcategoria quando categoria muda
        $set('subcategory_id', null);
    }),

Forms\Components\Select::make('subcategory_id')
    ->label('Subcategoria')
    ->options(function (callable $get) {
        $categoryId = $get('category_id');
        if (!$categoryId) return [];

        return Category::where('parent_id', $categoryId)->pluck('name', 'id');
    })
    ->hidden(fn (callable $get) => !$get('category_id')),
```

### Table com Badge Customizado

```php
Tables\Columns\BadgeColumn::make('rating')
    ->label('Avaliação')
    ->formatStateUsing(fn ($state) => str_repeat('⭐', $state))
    ->colors([
        'danger' => 1,
        'warning' => 2,
        'primary' => 3,
        'success' => fn ($state) => $state >= 4,
    ]),
```

### Action Customizada na Tabela

```php
Tables\Actions\Action::make('addQuote')
    ->label('Adicionar à Cotação')
    ->icon('heroicon-o-plus-circle')
    ->action(function (Product $record) {
        $quote = Quote::firstOrCreate([
            'company_id' => auth()->user()->current_company_id,
            'status' => 'draft',
        ], [
            'created_by' => auth()->id(),
            'title' => 'Rascunho',
        ]);

        $quote->items()->firstOrCreate([
            'product_id' => $record->id,
        ], [
            'quantity' => 1,
        ]);

        Notification::make()
            ->title('Produto adicionado à cotação')
            ->success()
            ->send();
    }),
```

### Widget de Dashboard

```php
// app/Filament/Widgets/StatsOverview.php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Product;
use App\Models\Quote;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $companyId = auth()->user()->current_company_id;

        return [
            Stat::make('Produtos', Product::forCompany($companyId)->count())
                ->description('Total de produtos cadastrados')
                ->icon('heroicon-o-cube'),

            Stat::make('Cotações Abertas', Quote::forCompany($companyId)->open()->count())
                ->description('Aguardando resposta')
                ->icon('heroicon-o-document-text')
                ->color('warning'),

            Stat::make('Fornecedores', Supplier::forCompany($companyId)->count())
                ->description('Fornecedores cadastrados')
                ->icon('heroicon-o-building-storefront'),
        ];
    }
}
```

---

## Testes com Tinker

```bash
php artisan tinker
```

```php
// Criar produto rapidamente
$product = App\Models\Product::create([
    'company_id' => 1,
    'name' => 'Teste',
    'unit' => 'UN',
    'is_active' => true,
]);

// Buscar com relacionamentos
$quote = App\Models\Quote::with('items.product', 'responses.supplier')->first();

// Testar scopes
App\Models\Product::forCompany(1)->active()->count();

// Ver SQL executado
App\Models\Product::forCompany(1)->toSql();
```

---

## 🔥 Dicas Avançadas

### N+1 Query Prevention

```php
// ❌ Ruim (N+1 queries)
$products = Product::all();
foreach ($products as $product) {
    echo $product->category->name;
}

// ✅ Bom (2 queries apenas)
$products = Product::with('category')->get();
foreach ($products as $product) {
    echo $product->category->name;
}
```

### Cache de Consultas Frequentes

```php
use Illuminate\Support\Facades\Cache;

// Cache por 1 hora
$suppliers = Cache::remember('suppliers_' . $companyId, 3600, function () use ($companyId) {
    return Supplier::forCompany($companyId)->get();
});
```

### Observer para Logs Automáticos

```php
// app/Observers/QuoteObserver.php

namespace App\Observers;

use App\Models\Quote;

class QuoteObserver
{
    public function created(Quote $quote)
    {
        activity()
            ->performedOn($quote)
            ->log('Cotação criada');
    }

    public function updated(Quote $quote)
    {
        if ($quote->isDirty('status')) {
            activity()
                ->performedOn($quote)
                ->log("Status alterado para {$quote->status}");
        }
    }
}

// app/Providers/AppServiceProvider.php
use App\Models\Quote;
use App\Observers\QuoteObserver;

public function boot()
{
    Quote::observe(QuoteObserver::class);
}
```

---

**📚 Para mais exemplos, consulte a documentação oficial do Laravel e FilamentPHP.**
