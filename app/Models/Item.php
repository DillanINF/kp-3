<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'item_type',
        'supplier_id',
        'name',
        'unit',
        'stock',
        'price',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'stock' => 'integer',
        'price' => 'integer',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
}
