<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'code',
        'type',
    ];

    /**
     * Produto associado
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
