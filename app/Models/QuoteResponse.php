<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuoteResponse extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'quote_id',
        'supplier_id',
        'total_value',
        'notes',
        'status',
        'submitted_at',
    ];

    protected $casts = [
        'total_value' => 'decimal:2',
        'submitted_at' => 'datetime',
    ];

    /**
     * Cotação associada
     */
    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    /**
     * Fornecedor que respondeu
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Itens da resposta (preços)
     */
    public function items(): HasMany
    {
        return $this->hasMany(QuoteResponseItem::class);
    }

    /**
     * Calcula e atualiza o valor total da resposta
     */
    public function calculateTotal(): void
    {
        $total = $this->items()->sum(function ($item) {
            return $item->unit_price * ($item->quote_item->quantity ?? 1);
        });

        $this->update(['total_value' => $total]);
    }
}
