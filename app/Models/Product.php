<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'category_id',
        'name',
        'brand',
        'description',
        'unit',
        'ean',
        'min_stock',
        'photo_path',
        'image_url',
        'is_active',
        'is_global',
        'created_by',
    ];

    protected $casts = [
        'min_stock' => 'decimal:2',
        'is_active' => 'boolean',
        'is_global' => 'boolean',
    ];

    /**
     * Empresa proprietária
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Usuário que criou o produto
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Categoria do produto
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Códigos do produto (EAN, interno, etc)
     */
    public function codes(): HasMany
    {
        return $this->hasMany(ProductCode::class);
    }

    /**
     * Itens de cotação relacionados
     */
    public function quoteItems(): HasMany
    {
        return $this->hasMany(QuoteItem::class);
    }

    /**
     * Configurações do produto por usuário (preço de venda, estoque) - ZePocket
     */
    public function userSettings(): HasMany
    {
        return $this->hasMany(ProductUserSetting::class);
    }

    /**
     * Configuração para um usuário específico - ZePocket
     */
    public function settingsForUser(int $userId): ?ProductUserSetting
    {
        return $this->userSettings()->where('user_id', $userId)->first();
    }

    /**
     * Cotações/preços de fornecedores - ZePocket
     */
    public function supplierPrices(): HasMany
    {
        return $this->hasMany(ProductSupplierPrice::class);
    }

    /**
     * Fornecedores que vendem este produto (via pivot) - ZePocket
     */
    public function suppliers(): BelongsToMany
    {
        return $this->belongsToMany(Supplier::class, 'product_supplier_prices')
            ->withPivot('cost_price', 'previous_price', 'last_quoted_at')
            ->withTimestamps();
    }

    /**
     * Menor preço disponível para um usuário - ZePocket
     */
    public function getBestPriceForUser(int $userId): ?ProductSupplierPrice
    {
        return $this->supplierPrices()
            ->whereHas('supplier', fn($q) => $q->where('user_id', $userId)->where('is_active', true))
            ->orderBy('cost_price')
            ->first();
    }

    /**
     * Itens de pedido - ZePocket
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Scope para filtrar por empresa
     */
    public function scopeForCompany(Builder $query, int $companyId): Builder
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope para produtos ativos
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: apenas produtos globais (validados) - ZePocket
     */
    public function scopeGlobal($query)
    {
        return $query->where('is_global', true);
    }

    /**
     * Scope para busca por nome ou código
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('brand', 'like', "%{$search}%")
              ->orWhere('ean', 'like', "%{$search}%")
              ->orWhereHas('codes', function ($q) use ($search) {
                  $q->where('code', 'like', "%{$search}%");
              });
        });
    }

    /**
     * Obtém o código EAN principal (fallback para campo direto ou tabela codes)
     */
    public function getEanCodeAttribute(): ?string
    {
        return $this->ean ?? $this->codes()->where('type', 'ean')->first()?->code;
    }

    /**
     * Serializa para API/IndexedDB - ZePocket
     */
    public function toSyncArray(int $userId): array
    {
        $settings = $this->settingsForUser($userId);
        $bestPrice = $this->getBestPriceForUser($userId);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'brand' => $this->brand,
            'unit' => $this->unit,
            'ean' => $this->ean_code,
            'image_url' => $this->image_url ?? $this->photo_path,
            'is_global' => $this->is_global,
            'sale_price' => $settings?->sale_price,
            'min_stock' => $settings?->min_stock ?? $this->min_stock,
            'current_stock' => $settings?->current_stock,
            'best_price' => $bestPrice?->cost_price,
            'best_supplier_id' => $bestPrice?->supplier_id,
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
