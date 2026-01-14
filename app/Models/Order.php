<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    const STATUS_DRAFT = 'draft';
    const STATUS_SENT = 'sent';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'user_id',
        'supplier_id',
        'status',
        'total_amount',
        'total_profit',
        'generated_at',
        'sent_at',
        'completed_at',
        'notes',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'total_profit' => 'decimal:2',
        'generated_at' => 'datetime',
        'sent_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Usuário dono do pedido
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Fornecedor do pedido
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Itens do pedido
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Recalcula totais do pedido
     */
    public function recalculateTotals(): self
    {
        $this->total_amount = $this->items()->sum('subtotal');
        $this->total_profit = $this->items()->sum('profit_snapshot');
        $this->save();
        
        return $this;
    }

    /**
     * Marca como enviado
     */
    public function markAsSent(): self
    {
        $this->status = self::STATUS_SENT;
        $this->sent_at = now();
        $this->save();
        
        return $this;
    }

    /**
     * Marca como concluído
     */
    public function markAsCompleted(): self
    {
        $this->status = self::STATUS_COMPLETED;
        $this->completed_at = now();
        $this->save();
        
        return $this;
    }

    /**
     * Cancela o pedido
     */
    public function cancel(): self
    {
        $this->status = self::STATUS_CANCELLED;
        $this->save();
        
        return $this;
    }

    /**
     * Clona os itens para um novo carrinho
     */
    public function cloneItems(): array
    {
        return $this->items->map(function ($item) {
            return [
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
            ];
        })->toArray();
    }

    /**
     * Scope: por status
     */
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: drafts
     */
    public function scopeDrafts($query)
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    /**
     * Scope: do usuário
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: recentes primeiro
     */
    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Gera texto para WhatsApp
     */
    public function toWhatsappText(): string
    {
        $text = "*PEDIDO DE COMPRA - {$this->supplier->name}*\n";
        $text .= "Data: " . now()->format('d/m/Y') . "\n";
        $text .= "----------------\n";
        
        foreach ($this->items as $item) {
            $subtotal = number_format($item->subtotal, 2, ',', '.');
            $text .= "[{$item->quantity}x] {$item->product->name} ({$item->product->unit}) - R\$ {$subtotal}\n";
        }
        
        $text .= "----------------\n";
        $total = number_format($this->total_amount, 2, ',', '.');
        $text .= "*TOTAL: R\$ {$total}*";
        
        return $text;
    }

    /**
     * Serializa para API/IndexedDB
     */
    public function toSyncArray(): array
    {
        return [
            'id' => $this->id,
            'supplier_id' => $this->supplier_id,
            'supplier_name' => $this->supplier?->name,
            'status' => $this->status,
            'total_amount' => (float) $this->total_amount,
            'total_profit' => (float) $this->total_profit,
            'items_count' => $this->items()->count(),
            'generated_at' => $this->generated_at?->toIso8601String(),
            'sent_at' => $this->sent_at?->toIso8601String(),
            'completed_at' => $this->completed_at?->toIso8601String(),
            'notes' => $this->notes,
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
