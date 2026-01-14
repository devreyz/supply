<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'user_id',
        'name',
        'contact_name',
        'phone',
        'email',
        'whatsapp',
        'document',
        'address',
        'rating',
        'settings',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'settings' => 'array',
        'rating' => 'integer',
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
     * Usuário dono do fornecedor - ZePocket
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Respostas de cotações deste fornecedor
     */
    public function quoteResponses(): HasMany
    {
        return $this->hasMany(QuoteResponse::class);
    }

    /**
     * Preços de produtos deste fornecedor - ZePocket
     */
    public function productPrices(): HasMany
    {
        return $this->hasMany(ProductSupplierPrice::class);
    }

    /**
     * Produtos fornecidos (via pivot) - ZePocket
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_supplier_prices')
            ->withPivot('cost_price', 'previous_price', 'last_quoted_at')
            ->withTimestamps();
    }

    /**
     * Pedidos para este fornecedor - ZePocket
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Scope para filtrar por empresa
     */
    public function scopeForCompany(Builder $query, int $companyId): Builder
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope: do usuário - ZePocket
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: apenas ativos - ZePocket
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para fornecedores com boa avaliação
     */
    public function scopeHighRated(Builder $query, int $minRating = 4): Builder
    {
        return $query->where('rating', '>=', $minRating);
    }

    /**
     * Formata WhatsApp para link - ZePocket
     */
    public function getWhatsappLinkAttribute(): ?string
    {
        if (!$this->whatsapp) return null;
        
        $number = preg_replace('/\D/', '', $this->whatsapp);
        if (strlen($number) < 10) return null;
        
        // Adiciona código do Brasil se não tiver
        if (strlen($number) <= 11) {
            $number = '55' . $number;
        }
        
        return "https://wa.me/{$number}";
    }

    /**
     * Serializa para API/IndexedDB - ZePocket
     */
    public function toSyncArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'contact_name' => $this->contact_name,
            'phone' => $this->phone,
            'email' => $this->email,
            'whatsapp' => $this->whatsapp,
            'whatsapp_link' => $this->whatsapp_link,
            'notes' => $this->notes,
            'is_active' => $this->is_active,
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
