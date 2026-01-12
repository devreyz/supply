<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuoteResponseItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_response_id',
        'quote_item_id',
        'unit_price',
        'quantity',
        'notes',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'quantity' => 'decimal:2',
    ];

    /**
     * Resposta associada
     */
    public function quoteResponse(): BelongsTo
    {
        return $this->belongsTo(QuoteResponse::class);
    }

    /**
     * Item da cotação original
     */
    public function quoteItem(): BelongsTo
    {
        return $this->belongsTo(QuoteItem::class);
    }

    /**
     * Calcula o subtotal deste item
     */
    public function getSubtotalAttribute(): float
    {
        return $this->unit_price * ($this->quoteItem->quantity ?? 1);
    }
}
