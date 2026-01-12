<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'category_id',
        'name',
        'description',
        'unit',
        'min_stock',
        'photo_path',
        'is_active',
    ];

    protected $casts = [
        'min_stock' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Empresa proprietária
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
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
     * Scope para busca por nome ou código
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where('name', 'like', "%{$search}%")
            ->orWhereHas('codes', function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%");
            });
    }

    /**
     * Obtém o código EAN principal
     */
    public function getEanAttribute(): ?string
    {
        return $this->codes()->where('type', 'ean')->first()?->code;
    }
}
