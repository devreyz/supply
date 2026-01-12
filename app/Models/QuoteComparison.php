<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuoteComparison extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_id',
        'selected_response_id',
        'comparison_data',
        'compared_at',
    ];

    protected $casts = [
        'comparison_data' => 'array',
        'compared_at' => 'datetime',
    ];

    /**
     * Cotação associada
     */
    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    /**
     * Resposta selecionada
     */
    public function selectedResponse(): BelongsTo
    {
        return $this->belongsTo(QuoteResponse::class, 'selected_response_id');
    }
}
