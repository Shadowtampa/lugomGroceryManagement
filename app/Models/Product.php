<?php

namespace App\Models;

use App\Enums\UnidadeMedida;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $casts = [
        'unidade_medida' => UnidadeMedida::class
    ];

    /**
     * Get all of the inventories for the Product
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

    /**
     * Get the stock of the product for a specific family
     *
     * @param int $family_id
     * @return int
     */
    public function stock(int $family_id): int
    {
        $inventory = $this->inventories()
            ->where('family_id', $family_id)
            ->first();

        return $inventory ? $inventory->stock : 0;
    }
}
