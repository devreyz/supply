<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'name',
        'contact_name',
        'phone',
        'email',
        'document',
        'address',
        'rating',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
        'rating' => 'integer',
    ];

    /**
     * Empresa proprietária
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Respostas de cotações deste fornecedor
     */
    public function quoteResponses(): HasMany
    {
        return $this->hasMany(QuoteResponse::class);
    }

    /**
     * Scope para filtrar por empresa
     */
    public function scopeForCompany(Builder $query, int $companyId): Builder
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope para fornecedores com boa avaliação
     */
    public function scopeHighRated(Builder $query, int $minRating = 4): Builder
    {
        return $query->where('rating', '>=', $minRating);
    }
}
