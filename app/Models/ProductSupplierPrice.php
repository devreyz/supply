<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductSupplierPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'product_id',
        'cost_price',
        'previous_price',
        'last_quoted_at',
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'previous_price' => 'decimal:2',
        'last_quoted_at' => 'datetime',
    ];

    /**
     * Fornecedor
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Produto
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Variação de preço (positivo = aumento, negativo = desconto)
     */
    public function getPriceVariationAttribute(): ?float
    {
        if (!$this->previous_price) return null;
        return $this->cost_price - $this->previous_price;
    }

    /**
     * Porcentagem de variação
     */
    public function getPriceVariationPercentAttribute(): ?float
    {
        if (!$this->previous_price || $this->previous_price <= 0) return null;
        return (($this->cost_price - $this->previous_price) / $this->previous_price) * 100;
    }

    /**
     * Indica se o preço subiu
     */
    public function getIsPriceIncreaseAttribute(): bool
    {
        return $this->price_variation !== null && $this->price_variation > 0;
    }

    /**
     * Indica se o preço desceu
     */
    public function getIsPriceDecreaseAttribute(): bool
    {
        return $this->price_variation !== null && $this->price_variation < 0;
    }

    /**
     * Scope: ordenar por menor preço
     */
    public function scopeCheapest($query)
    {
        return $query->orderBy('cost_price', 'asc');
    }

    /**
     * Scope: por produto
     */
    public function scopeForProduct($query, int $productId)
    {
        return $query->where('product_id', $productId);
    }

    /**
     * Scope: por fornecedor
     */
    public function scopeForSupplier($query, int $supplierId)
    {
        return $query->where('supplier_id', $supplierId);
    }

    /**
     * Serializa para API/IndexedDB
     */
    public function toSyncArray(): array
    {
        return [
            'id' => $this->id,
            'supplier_id' => $this->supplier_id,
            'product_id' => $this->product_id,
            'cost_price' => (float) $this->cost_price,
            'previous_price' => $this->previous_price ? (float) $this->previous_price : null,
            'price_variation' => $this->price_variation,
            'price_variation_percent' => $this->price_variation_percent,
            'is_price_increase' => $this->is_price_increase,
            'is_price_decrease' => $this->is_price_decrease,
            'last_quoted_at' => $this->last_quoted_at?->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
