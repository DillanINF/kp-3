<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PoPendingItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'invoice_no',
        'po_no',
        'customer_id',
        'item_id',
        'qty',
        'price',
        'status',
    ];

    protected $casts = [
        'qty' => 'integer',
        'price' => 'integer',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
