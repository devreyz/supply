<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'unit_cost_snapshot',
        'unit_sale_snapshot',
        'subtotal',
        'profit_snapshot',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_cost_snapshot' => 'decimal:2',
        'unit_sale_snapshot' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'profit_snapshot' => 'decimal:2',
    ];

    /**
     * Pedido
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Produto
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Calcula subtotal automaticamente antes de salvar
     */
    protected static function booted()
    {
        static::saving(function ($item) {
            $item->subtotal = $item->quantity * $item->unit_cost_snapshot;
            
            if ($item->unit_sale_snapshot) {
                $item->profit_snapshot = ($item->unit_sale_snapshot - $item->unit_cost_snapshot) * $item->quantity;
            }
        });

        static::saved(function ($item) {
            // Recalcula totais do pedido
            $item->order->recalculateTotals();
        });

        static::deleted(function ($item) {
            // Recalcula totais do pedido
            $item->order->recalculateTotals();
        });
    }

    /**
     * Serializa para API/IndexedDB
     */
    public function toSyncArray(): array
    {
        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'product_id' => $this->product_id,
            'product_name' => $this->product?->name,
            'product_brand' => $this->product?->brand,
            'product_unit' => $this->product?->unit,
            'quantity' => $this->quantity,
            'unit_cost_snapshot' => (float) $this->unit_cost_snapshot,
            'unit_sale_snapshot' => $this->unit_sale_snapshot ? (float) $this->unit_sale_snapshot : null,
            'subtotal' => (float) $this->subtotal,
            'profit_snapshot' => $this->profit_snapshot ? (float) $this->profit_snapshot : null,
        ];
    }
}
