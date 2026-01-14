<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductUserSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'sale_price',
        'min_stock',
        'current_stock',
    ];

    protected $casts = [
        'sale_price' => 'decimal:2',
        'min_stock' => 'integer',
        'current_stock' => 'integer',
    ];

    /**
     * Usuário dono das configurações
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Produto relacionado
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Calcula margem de lucro baseada no melhor preço
     */
    public function getMarginAttribute(): ?float
    {
        if (!$this->sale_price) return null;

        $bestPrice = $this->product->getBestPriceForUser($this->user_id);
        if (!$bestPrice) return null;

        return $this->sale_price - $bestPrice->cost_price;
    }

    /**
     * Calcula porcentagem de margem
     */
    public function getMarginPercentAttribute(): ?float
    {
        if (!$this->sale_price || $this->sale_price <= 0) return null;

        $margin = $this->margin;
        if ($margin === null) return null;

        return ($margin / $this->sale_price) * 100;
    }
}
