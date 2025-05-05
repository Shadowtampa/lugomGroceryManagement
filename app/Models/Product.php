<?php

namespace App\Models;

use App\Enums\UnidadeMedida;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $casts = [
        'unidade_medida' => UnidadeMedida::class
    ];

    /**
     * Get the user that owns the Product.
     */
    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class, 'families_id');
    }
}
