<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuoteItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_id',
        'product_id',
        'quantity',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
    ];

    /**
     * Cotação associada
     */
    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    /**
     * Produto solicitado
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Respostas para este item
     */
    public function responseItems(): HasMany
    {
        return $this->hasMany(QuoteResponseItem::class);
    }

    /**
     * Obtém o melhor preço para este item
     */
    public function getBestPriceAttribute(): ?float
    {
        return $this->responseItems()
            ->whereHas('quoteResponse', fn($q) => $q->where('status', 'submitted'))
            ->min('unit_price');
    }
}
