<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierItem extends Model
{
    use HasFactory;

    protected $table = 'supplier_items';

    protected $fillable = [
        'supplier_id',
        'item_id',
        'buy_price',
        'is_active',
    ];

    protected $casts = [
        'buy_price' => 'integer',
        'is_active' => 'boolean',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
